@extends('layouts.app')

@section('title', 'Daftar Batch Produksi')

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark m-0 tracking-tight">Production Batches</h3>
            <p class="text-muted small m-0 mt-1">Kelola antrean, status alur, dan evaluasi hasil batch produksi manufaktur.</p>
        </div>
        <div>
            <a href="{{ route('batches.create') }}" class="btn btn-primary px-3 py-2 fw-semibold rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                <i class="bi bi-plus-lg"></i>
                <span>Buat Batch Baru</span>
            </a>
        </div>
    </div>

    {{-- Filter & Search Bar --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('batches.index') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0 ps-0" placeholder="Cari No Batch atau Nama Produk..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select bg-light">
                        <option value="">-- Semua Status --</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-secondary w-100 fw-medium">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('batches.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light-subtle text-uppercase text-muted fs-7 fw-bold border-bottom">
                    <tr>
                        <th class="ps-4">No. Batch</th>
                        <th>Produk</th>
                        <th class="text-end">Target Qty</th>
                        <th class="text-end">Actual Qty</th>
                        <th class="text-center">Yield Rate</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y fs-6">
                    @forelse ($batches as $batch)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">
                                <span class="font-monospace text-primary">#{{ $batch->batch_number }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $batch->product_name }}</div>
                            </td>
                            <td class="text-end fw-medium">{{ number_format($batch->target_qty, 2) }}</td>
                            <td class="text-end fw-medium">
                                {{ $batch->actual_qty ? number_format($batch->actual_qty, 2) : '-' }}
                            </td>
                            <td class="text-center">
                                @if(!is_null($batch->yield_rate))
                                    @php
                                        $yieldClass = $batch->yield_rate >= 95 ? 'badge-soft-success' : ($batch->yield_rate >= 80 ? 'badge-soft-warning' : 'badge-soft-danger');
                                    @endphp
                                    <span class="badge {{ $yieldClass }} rounded-pill px-2.5 py-1 fw-semibold">
                                        {{ number_format($batch->yield_rate, 2) }}%
                                    </span>
                                @else
                                    <span class="text-muted small">&mdash;</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    $statusClasses = [
                                        'draft' => 'badge-soft-secondary',
                                        'in_progress' => 'badge-soft-primary',
                                        'completed' => 'badge-soft-success',
                                        'cancelled' => 'badge-soft-danger',
                                    ];
                                    $currentClass = $statusClasses[$batch->status] ?? 'badge-soft-secondary';
                                @endphp
                                <span class="badge {{ $currentClass }} rounded-pill px-3 py-1 fw-semibold text-uppercase fs-8">
                                    {{ str_replace('_', ' ', $batch->status) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-1 align-items-center">
                                    {{-- Detail Button --}}
                                    <a href="{{ route('batches.show', $batch->id) }}" class="btn btn-sm btn-light border text-secondary rounded-2 px-2.5" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    {{-- Complete Modal Trigger --}}
                                    @if($batch->status !== 'completed' && $batch->status !== 'cancelled')
                                        <button type="button" class="btn btn-sm btn-success rounded-2 px-2.5" data-bs-toggle="modal" data-bs-target="#completeModal-{{ $batch->id }}" title="Selesaikan Batch">
                                            <i class="bi bi-check-lg"></i>
                                        </button>

                                        {{-- Modern Complete Modal --}}
                                        <div class="modal fade" id="completeModal-{{ $batch->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow rounded-4 text-start">
                                                    <form action="{{ route('batches.update-status', $batch->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="completed">

                                                        <div class="modal-header border-bottom-0 pb-0">
                                                            <div>
                                                                <h5 class="modal-title fw-bold text-dark">Selesaikan Batch #{{ $batch->batch_number }}</h5>
                                                                <p class="text-muted small mb-0">Catat kuantitas aktual produk dan limbah produksi.</p>
                                                            </div>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body py-3">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold text-dark fs-7">Produk Jadi Aktual (Qty)</label>
                                                                <input type="number" step="0.01" name="actual_qty" class="form-control form-control-lg fs-6" value="{{ $batch->target_qty }}" required>
                                                            </div>
                                                            <div class="row g-2 mb-3">
                                                                <div class="col-6">
                                                                    <label class="form-label text-muted fs-7">Normal Waste (Sisa)</label>
                                                                    <input type="number" step="0.01" name="normal_waste_qty" class="form-control" value="0">
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="form-label text-danger fs-7">Abnormal Waste (Cacat)</label>
                                                                    <input type="number" step="0.01" name="abnormal_waste_qty" class="form-control" value="0">
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label text-muted fs-7">Catatan / Keterangan Limbah</label>
                                                                <textarea name="waste_notes" class="form-control" rows="2" placeholder="Opsi: Penyebab terjadi limbah/cacat..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-top-0 pt-0">
                                                            <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-success rounded-3 px-3">
                                                                <i class="bi bi-check2-circle me-1"></i> Simpan & Potong Stok
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Delete Button --}}
                                    <form action="{{ route('batches.destroy', $batch->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus batch produksi ini? {{ $batch->status === 'completed' ? 'Stok bahan baku akan dikembalikan otomatis.' : '' }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger rounded-2 px-2.5" title="Hapus Batch">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                    <p class="m-0 fw-medium">Belum ada data batch produksi.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($batches->hasPages())
            <div class="card-footer bg-white border-top-0 py-3">
                {{ $batches->links() }} 
            </div>
        @endif
    </div>

</div>
@endsection