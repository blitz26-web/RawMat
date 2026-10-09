<?php

namespace App\Services;

use App\Events\MaterialStockDepleted;
use App\Models\BatchMaterialUsage;
use App\Models\Material;
use App\Models\ProductionBatch;
use App\Models\ScrapLog;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductionBatchService
{
    /**
     * Membuat Production Batch baru beserta alokasi rencana penggunaan bahan baku.
     *
     * @param array $data Data batch & daftar material yang direncanakan
     * @return ProductionBatch
     * @throws Exception
     */
    public function createBatch(array $data): ProductionBatch
    {
        return DB::transaction(function () use ($data) {
            // 1. Simpan Header Batch Produksi
            $batch = ProductionBatch::create([
                'batch_number' => $data['batch_number'],
                'product_name' => $data['product_name'],
                'target_qty'   => $data['target_qty'],
                'status'       => 'draft',
                'start_date'   => $data['start_date'] ?? null,
            ]);

            // 2. Simpan Rencana Penggunaan Bahan Baku (BatchMaterialUsage)
            if (!empty($data['materials']) && is_array($data['materials'])) {
                foreach ($data['materials'] as$item) {
                    BatchMaterialUsage::create([
                        'production_batch_id' => $batch->id,
                        'material_id'         => $item['material_id'],
                        'qty_planned'         => $item['qty_planned'],
                        'qty_used'            => 0.00,
                    ]);
                }
            }

            return $batch->load('materialUsages.material');
        });
    }

    /**
     * Mengubah status batch menjadi 'in_progress' dan mencatat waktu mulai.
     *
     * @param int $batchId
     * @return ProductionBatch
     */
    public function startBatch(int $batchId): ProductionBatch
    {
        $batch = ProductionBatch::findOrFail($batchId);

        if ($batch->status !== 'draft') {
            throw new Exception("Hanya batch berstatus 'draft' yang dapat dimulai.");
        }

        $batch->update([
            'status'     => 'in_progress',
            'start_date' => now(),
        ]);

        return $batch;
    }

    /**
     * Menyelesaikan Batch Produksi (Complete Batch):
     * 1. Menghitung persentase Yield Rate otomatis.
     * 2. Mengurangi stok aktual bahan baku pada tabel `materials`.
     * 3. Mencatat pemakaian aktual & log limbah/scrap.
     * 4. Memicu event peringatan Safety Stock jika stok kritis.
     *
     * @param int $batchId
     * @param float $actualQty Jumlah produk jadi aktual
     * @param array $materialsUsed Array format: [['material_id' => 1, 'qty_used' => 50.00], ...]
     * @param array $scrapData Array format: [['material_id' => 1, 'scrap_qty' => 2.50, 'scrap_reason' => 'Sisa Potong'], ...]
     * @return ProductionBatch
     * @throws Exception
     */
    public function completeBatch(int $batchId, float$actualQty, array $materialsUsed, array$scrapData = []): ProductionBatch
    {
        return DB::transaction(function () use ($batchId,$actualQty, $materialsUsed,$scrapData) {
            $batch = ProductionBatch::with('materialUsages')->findOrFail($batchId);

            if ($batch->status === 'completed') {
                throw new Exception("Production Batch ini sudah berstatus 'completed' sebelumnya.");
            }

            // 1. Validasi Kecukupan Stok Bahan Baku Sebelum Pengurangan
            foreach ($materialsUsed as$usage) {
                $material = Material::lockForUpdate()->findOrFail($usage['material_id']);
                
                if ($material->current_stock <$usage['qty_used']) {
                    throw new Exception("Stok bahan baku '{$material->name}' ({$material->code}) tidak mencukupi. Stok saat ini: {$material->current_stock} {$material->unit}, Dibutuhkan: {$usage['qty_used']} {$material->unit}.");
                }
            }

            // 2. Hitung Yield Rate (%) -> Rumus: (Actual Qty / Target Qty) * 100
            $yieldRate = 0.00;
            if ($batch->target_qty > 0) {$yieldRate = round(($actualQty / $batch->target_qty) * 100, 2);
            }

            // 3. Update Status Header Batch
            $batch->update([
                'actual_qty' => $actualQty,
                'status'     => 'completed',
                'end_date'   => now(),
                'yield_rate' => $yieldRate,
            ]);

            // 4. Update Material Usage & Potong Stok Baku di DB
            $affectedMaterials = [];

            foreach ($materialsUsed as$usage) {
                // Update / Insert ke tabel pivot batch_material_usages
                BatchMaterialUsage::updateOrCreate(
                    [
                        'production_batch_id' => $batch->id,
                        'material_id'         => $usage['material_id'],
                    ],
                    [
                        'qty_planned' => $usage['qty_planned'] ?? $usage['qty_used'],
                        'qty_used'    => $usage['qty_used'],
                    ]
                );

                // Potong Stok Material
                $material = Material::findOrFail($usage['material_id']);
                $material->decrement('current_stock',$usage['qty_used']);
                
                // Simpan reference material yang diperbarui untuk pengecekan Safety Stock
                $affectedMaterials[] =$material->fresh();
            }

            // 5. Catat Log Limbah / Scrap jika ada
            if (!empty($scrapData) && is_array($scrapData)) {
                foreach ($scrapData as$scrap) {
                    if (isset($scrap['scrap_qty']) &&$scrap['scrap_qty'] > 0) {
                        ScrapLog::create([
                            'production_batch_id' => $batch->id,
                            'material_id'         => $scrap['material_id'],
                            'scrap_qty'           => $scrap['scrap_qty'],
                            'scrap_reason'        => $scrap['scrap_reason'] ?? 'Sisa Hasil Produksi',
                        ]);
                    }
                }
            }

            // 6. Trigger Event Peringatan Safety Stock untuk Bahan Baku yang Kritis
            foreach ($affectedMaterials as$material) {
                if ($material->is_low_stock) {
                    // Trigger Event (Akan dikonsumsi oleh Listener untuk kirim Notifikasi/Toast)
                    event(new MaterialStockDepleted($material));
                    Log::warning("Safety Stock Warning: Bahan baku {$material->name} ({$material->code}) berada di bawah safety stock. Stok sisa: {$material->current_stock} {$material->unit}.");
                }
            }

            return $batch->fresh(['materialUsages.material', 'scrapLogs.material']);
        });
    }
}