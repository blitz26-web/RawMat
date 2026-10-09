<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompleteBatchRequest;
use App\Http\Requests\StoreBatchRequest;
use App\Models\Material;
use App\Models\ProductionBatch;
use App\Services\ProductionBatchService;
use Exception;
use Illuminate\Http\Request;
use App\Models\Product; 

class ProductionBatchController extends Controller
{
    protected ProductionBatchService $batchService;

    public function __construct(ProductionBatchService $batchService)
    {
        $this->batchService = $batchService;
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
        $products  = Product::orderBy('name')->get();
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