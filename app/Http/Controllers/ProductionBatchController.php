<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompleteBatchRequest;
use App\Http\Requests\StoreBatchRequest;
use App\Models\Material;
use App\Models\ProductionBatch;
use App\Models\Product;
use App\Services\ProductionBatchService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionBatchController extends Controller
{
    protected ProductionBatchService $batchService;

    public function __construct(ProductionBatchService $batchService)
    {
        $this->batchService = $batchService;
    }

    public function updateStatus(Request $request, ProductionBatch $batch)
{
    $request->validate([
        'status'             => 'required|in:draft,in_progress,completed,cancelled',
        'actual_qty'         => 'nullable|numeric|min:0',
        'normal_waste_qty'   => 'nullable|numeric|min:0',
        'abnormal_waste_qty' => 'nullable|numeric|min:0',
        'waste_notes'        => 'nullable|string',
    ]);

    try {
        DB::transaction(function () use ($batch, $request) {
            if ($request->status === 'completed' && $batch->status !== 'completed') {
                $totalWasteCost = 0;

                // 1. Potong Stok Bahan Baku
                foreach ($batch->materials as $item) {
                    $material = Material::lockForUpdate()->findOrFail($item->material_id);

                    if ($material->current_stock < $item->qty_planned) {
                        throw new \Exception("Stok '{$material->name}' kurang! Sisa: {$material->current_stock}, butuh: {$item->qty_planned}");
                    }

                    $material->decrement('current_stock', $item->qty_planned);

                    // Estimasi biaya limbah per unit bahan baku (jika ada unit_price di model Material)
                    $unitPrice = $material->unit_price ?? 0;
                    $totalWasteQty = ($request->normal_waste_qty ?? 0) + ($request->abnormal_waste_qty ?? 0);
                    $totalWasteCost += ($totalWasteQty * $unitPrice);
                }

                // 2. Hitung Yield Rate
                $actualQty = $request->actual_qty ?? $batch->target_qty;
                $yieldRate = ($batch->target_qty > 0) ? ($actualQty / $batch->target_qty) * 100 : 0;

                // 3. Update Batch dengan Data Limbah
                $batch->update([
                    'status'             => 'completed',
                    'actual_qty'         => $actualQty,
                    'yield_rate'         => $yieldRate,
                    'normal_waste_qty'   => $request->normal_waste_qty ?? 0,
                    'abnormal_waste_qty' => $request->abnormal_waste_qty ?? 0,
                    'waste_cost'         => $totalWasteCost,
                    'waste_notes'        => $request->waste_notes,
                ]);
            } else {
                $batch->update(['status' => $request->status]);
            }
        });

        return redirect()->back()->with('success', 'Status batch & pencatatan limbah berhasil diperbarui!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', $e->getMessage());
    }
}

    public function index(Request $request)
    {
        $query = ProductionBatch::withCount(['materialUsages', 'scrapLogs'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('batch_number', 'like', "%{$search}%")
                  ->orWhere('product_name', 'like', "%{$search}%");
            });
        }

        $batches = $query->paginate(10)->withQueryString();

        return view('batches.index', compact('batches'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();
        $materials = Material::orderBy('name')->get();

        return view('batches.create', compact('products', 'materials'));
    }

    public function store(StoreBatchRequest $request)
    {
        try {
            $batch = $this->batchService->createBatch($request->validated());

            return redirect()
                ->route('batches.show', $batch->id)
                ->with('success', "Batch produksi #{$batch->batch_number} berhasil dibuat.");
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(ProductionBatch $batch)
    {
        $batch->load(['materialUsages.material', 'scrapLogs.material']);
        $materials = Material::orderBy('name')->get();

        return view('batches.show', compact('batch', 'materials'));
    }

    public function start(ProductionBatch $batch)
    {
        try {
            $this->batchService->startBatch($batch->id);

            return redirect()
                ->route('batches.show', $batch->id)
                ->with('success', "Status batch #{$batch->batch_number} diubah menjadi In Progress.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function complete(CompleteBatchRequest $request, ProductionBatch $batch)
    {
        try {
            $validated = $request->validated();

            $this->batchService->completeBatch(
                $batch->id,
                (float) $validated['actual_qty'],
                $validated['materials_used'],
                $validated['scrap'] ?? []
            );

            return redirect()
                ->route('batches.show', $batch->id)
                ->with('success', "Batch #{$batch->batch_number} berhasil diselesaikan dan stok bahan baku telah dipotong.");
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
