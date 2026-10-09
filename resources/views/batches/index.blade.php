@extends('layouts.app')

@section('title', 'Daftar Batch Produksi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold m-0"><i class="bi bi-diagram-3 me-2"></i>Production Batches</h3>
        <p class="text-muted small m-0">Kelola antrean dan riwayat alur batch produksi manufaktur.</p>
    </div>
    <a href="{{ route('batches.create') }}" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-circle me-1"></i> Buat Batch Baru
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('batches.index') }}" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari No Batch / Nama Produk..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">-- Semua Status --</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100"><i class="bi bi-filter me-1"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No. Batch</th>
                        <th>Produk</th>
                        <th>Target Qty</th>
                        <th>Actual Qty</th>
                        <th>Yield Rate</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($batches as $batch)
                        <tr>
                            <td class="fw-bold">{{ $batch->batch_number }}</td>
                            <td>{{ $batch->product_name }}</td>
                            <td>{{ number_format($batch->target_qty, 2) }}</td>
                            <td>{{ $batch->actual_qty ? number_format($batch->actual_qty, 2) : '-' }}</td>
                            <td>
                                @if(!is_null($batch->yield_rate))
                                    <span class="badge {{ $batch->yield_badge_class }}">
                                        {{ number_format($batch->yield_rate, 2) }}%
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $batch->status_badge_class }}">
                                    {{ strtoupper(str_replace('_', ' ', $batch->status)) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('batches.show', $batch->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i> Detail
                                    </a>

                                    {{-- Tombol Pemicu Modal & Modal Form Penyelesaian Batch --}}
                                    @if($batch->status !== 'completed' && $batch->status !== 'cancelled')
                                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#completeModal-{{ $batch->id }}">
                                            <i class="bi bi-check-circle me-1"></i> Selesaikan
                                        </button>

                                        <div class="modal fade" id="completeModal-{{ $batch->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('batches.update-status', $batch->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="completed">

                                                        <div class="modal-header">
                                                            <h5 class="modal-title fw-bold">Penyelesaian Batch #{{ $batch->batch_number }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body text-start">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Produk Jadi Aktual (Qty)</label>
                                                                <input type="number" step="0.01" name="actual_qty" class="form-control" value="{{ $batch->target_qty }}" required>
                                                            </div>
                                                            <div class="row g-2 mb-3">
                                                                <div class="col-6">
                                                                    <label class="form-label text-muted small">Normal Waste (Sisa Standar)</label>
                                                                    <input type="number" step="0.01" name="normal_waste_qty" class="form-control" value="0">
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="form-label text-danger small">Abnormal Waste (Cacat/Spoilage)</label>
                                                                    <input type="number" step="0.01" name="abnormal_waste_qty" class="form-control" value="0">
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label text-muted small">Catatan Limbah / Penyebab Cacat</label>
                                                                <textarea name="waste_notes" class="form-control" rows="2" placeholder="Contoh: Suhu mesin terlalu tinggi di awal batch..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-success">Simpan & Potong Stok</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada data batch produksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($batches->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $batches->links() }}
        </div>
    @endif
</div>
@endsection