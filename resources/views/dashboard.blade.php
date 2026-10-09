@extends('layouts.app')

@section('title', 'Dashboard Analitik')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Top Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark m-0 tracking-tight">Dashboard Analitik</h3>
            <p class="text-muted small m-0 mt-1">Ringkasan performa efisiensi produksi, limbah, dan kontrol stok bahan baku.</p>
        </div>
        <div>
            <div class="bg-white border border-slate-200 rounded-3 px-3 py-2 shadow-sm d-inline-flex align-items-center gap-2 text-secondary small fw-semibold">
                <i class="bi bi-calendar3 text-primary"></i>
                <span>{{ \Carbon\Carbon::now()->format('d F Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Metric Cards Grid dengan Tint Soft Color --}}
    <div class="row g-3 mb-4">
        
        {{-- Card 1: Stok Kritis (Soft Red) --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #ffffff 0%, #fff1f2 100%); border: 1px solid #fecdd3 !important;">
                <div class="d-flex justify-content-between align-items-start mb-3 gap-2">
                    <span class="text-uppercase text-danger fs-7 fw-bold tracking-wider">Stok Kritis</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center bg-danger text-white shadow-sm flex-shrink-0" style="width: 40px; height: 40px;">
                        <i class="bi bi-exclamation-triangle-fill fs-6"></i>
                    </div>
                </div>
                <div class="mb-2">
                    <h2 class="fw-bold text-danger m-0 fs-1">{{ $criticalStockCount ?? 0 }}</h2>
                </div>
                <div class="d-flex align-items-center gap-2 mt-auto">
                    <span class="badge bg-danger text-white rounded-pill px-2.5 py-1 fs-8 fw-semibold">Warning</span>
                    <span class="text-muted small text-truncate">Perlu restock supplier</span>
                </div>
            </div>
        </div>

        {{-- Card 2: AVG Yield Rate (Soft Green) --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%); border: 1px solid #bbf7d0 !important;">
                <div class="d-flex justify-content-between align-items-start mb-3 gap-2">
                    <span class="text-uppercase text-success fs-7 fw-bold tracking-wider">Avg Yield Rate</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center bg-success text-white shadow-sm flex-shrink-0" style="width: 40px; height: 40px;">
                        <i class="bi bi-graph-up-arrow fs-6"></i>
                    </div>
                </div>
                <div class="mb-2">
                    <h2 class="fw-bold text-success m-0 fs-1">{{ number_format($avgYieldRate ?? 0, 2) }}%</h2>
                </div>
                <div class="mt-auto">
                    <span class="text-muted small">Target Efisiensi: <strong class="text-dark">&ge; 95.00%</strong></span>
                </div>
            </div>
        </div>

        {{-- Card 3: Total Scrap (Soft Amber/Yellow) --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #ffffff 0%, #fffbeb 100%); border: 1px solid #fde68a !important;">
                <div class="d-flex justify-content-between align-items-start mb-3 gap-2">
                    <span class="text-uppercase text-warning fs-7 fw-bold tracking-wider">Total Scrap</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center bg-warning text-dark shadow-sm flex-shrink-0" style="width: 40px; height: 40px;">
                        <i class="bi bi-trash3-fill fs-6"></i>
                    </div>
                </div>
                <div class="mb-2">
                    <h2 class="fw-bold text-warning-emphasis m-0 fs-1">{{ number_format($totalScrap ?? 0, 2) }}</h2>
                </div>
                <div class="mt-auto">
                    <span class="text-muted small">Akumulasi limbah produksi</span>
                </div>
            </div>
        </div>

        {{-- Card 4: Batch Aktif (Soft Blue) --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%); border: 1px solid #bae6fd !important;">
                <div class="d-flex justify-content-between align-items-start mb-3 gap-2">
                    <span class="text-uppercase text-primary fs-7 fw-bold tracking-wider">Batch Aktif</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center bg-primary text-white shadow-sm flex-shrink-0" style="width: 40px; height: 40px;">
                        <i class="bi bi-arrow-repeat fs-6"></i>
                    </div>
                </div>
                <div class="mb-2">
                    <h2 class="fw-bold text-primary m-0 fs-1">{{ $activeBatchesCount ?? 0 }}</h2>
                </div>
                <div class="mt-auto">
                    <span class="text-muted small">Dalam antrean / proses pabrik</span>
                </div>
            </div>
        </div>

    </div>

    {{-- Chart Section --}}
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white" style="border: 1px solid #e2e8f0 !important;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-bold text-dark m-0"><i class="bi bi-graph-up me-2 text-primary"></i>Tren Yield Rate (6 Bulan Terakhir)</h5>
                <p class="text-muted small m-0 mt-1">Evaluasi tren persentase keberhasilan batch produksi.</p>
            </div>
        </div>
        <div style="min-height: 280px;">
            <canvas id="yieldChart"></canvas>
        </div>
    </div>

</div>
@endsection