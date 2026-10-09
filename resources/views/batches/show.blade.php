@extends('layouts.app')

@section('title', 'Detail Batch #' . $batch->batch_number)

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <a href="{{ route('batches.index') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Batch
        </a>
        <h3 class="fw-bold mt-2 m-0">Batch #{{ $batch->batch_number }}</h3>
    </div>
    <div>
        @if($batch->status === 'draft')
            <form action="{{ route('batches.start', $batch->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-warning shadow-sm fw-bold">
                    <i class="bi bi-play-fill me-1"></i> Mulai Produksi (In Progress)
                </button>
            </form>
        @elseif($batch->status === 'in_progress')
            <button type="button" class="btn btn-success shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#completeBatchModal">
                <i class="bi bi-check-circle me-1"></i> Selesaikan Batch (Complete)
            </button>
        @endif
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Stat Header Card -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3">
            <div class="text-muted small">Status Batch</div>
            <div class="mt-1">
                <span class="badge fs-6 {{ $batch->status_badge_class }}">
                    {{ strtoupper(str_replace('_', ' ', $batch->status)) }}
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3">
            <div class="text-muted small">Target vs Actual Qty</div>
            <div class="fs-5 fw-bold text-dark mt-1">
                {{ number_format($batch->target_qty, 2) }} / {{ $batch->actual_qty ? number_format($batch->actual_qty, 2) : '-' }}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm p-3">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small">Yield Rate (Efisiensi Result)</span>
                <span class="badge {{ $batch->yield_badge_class }}">
                    {{ !is_null($batch->yield_rate) ? number_format($batch->yield_rate, 2) . '%' : 'N/A' }}
                </span>
            </div>
            <div class="progress mt-2" style="height: 10px;">
                <div class="progress-bar {{ $batch->yield_badge_class }}" role="progressbar" style="width: {{ min($batch->yield_rate ?? 0, 100) }}%"></div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Penggunaan Material -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-bold py-3">Rincian Penggunaan Bahan Baku</div>
    <div class="card-body p-0">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Kode Material</th>
                    <th>Nama Bahan Baku</th>
                    <th>Rencana (Planned)</th>
                    <th>Aktual Terpakai (Used)</th>
                    <th>Stok Gudang Saat Ini</th>
                </tr>
            </thead>
            <tbody>
                @foreach($batch->materialUsages as $usage)
                    <tr>
                        <td><code>{{ $usage->material->code }}</code></td>
                        <td class="fw-bold">{{ $usage->material->name }}</td>
                        <td>{{ number_format($usage->qty_planned, 2) }} {{ $usage->material->unit }}</td>
                        <td>{{ number_format($usage->qty_used, 2) }} {{ $usage->material->unit }}</td>
                        <td>
                            <span class="badge {{ $usage->material->stock_badge_class }}">
                                {{ number_format($usage->material->current_stock, 2) }} {{ $usage->material->unit }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Complete Batch -->
@if($batch->status === 'in_progress')
<div class="modal fade" id="completeBatchModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('batches.complete', $batch->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Selesaikan Production Batch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Total Hasil Produk Jadi (Actual Qty) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="actual_qty" class="form-control" placeholder="Target: {{ $batch->target_qty }}" required>
                    </div>

                    <h6 class="fw-bold mt-4 mb-2">Konfirmasi Penggunaan Bahan Baku Aktual:</h6>
                    @foreach($batch->materialUsages as $index => $usage)
                        <div class="row g-2 mb-2 align-items-center">
                            <div class="col-md-6">
                                <input type="hidden" name="materials_used[{{ $index }}][material_id]" value="{{ $usage->material_id }}">
                                <span class="small fw-bold">{{ $usage->material->name }}</span>
                            </div>
                            <div class="col-md-6">
                                <input type="number" step="0.01" name="materials_used[{{ $index }}][qty_used]" class="form-control form-control-sm" value="{{ $usage->qty_planned }}" required>
                            </div>
                        </div>
                    @endforeach

                    <h6 class="fw-bold mt-4 mb-2">Catat Limbah / Scrap (Jika Ada):</h6>
                    @foreach($batch->materialUsages as $index => $usage)
                        <div class="row g-2 mb-2">
                            <div class="col-md-4">
                                <input type="hidden" name="scrap[{{ $index }}][material_id]" value="{{ $usage->material_id }}">
                                <span class="small">{{ $usage->material->name }}</span>
                            </div>
                            <div class="col-md-3">
                                <input type="number" step="0.01" name="scrap[{{ $index }}][scrap_qty]" class="form-control form-control-sm" placeholder="Qty Scrap">
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="scrap[{{ $index }}][scrap_reason]" class="form-control form-control-sm" placeholder="Alasan (Cacat/Sisa Potong)">
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-bold">Selesaikan &amp; Potong Stok</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection