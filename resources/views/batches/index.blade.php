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
                                <a href="{{ route('batches.show', $batch->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i> Detail
                                </a>
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