@extends('layouts.app')

@section('title', 'Dashboard Analitik')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-title m-0 tracking-tight">Dashboard Analitik</h3>
            <p class="text-sub small m-0 mt-1">Ringkasan performa efisiensi produksi, limbah, dan kontrol stok bahan baku.</p>
        </div>
        <div>
            <div class="card-main rounded-3 px-3 py-2 shadow-sm d-inline-flex align-items-center gap-2 small fw-semibold">
                <i class="bi bi-calendar3 text-primary"></i>
                <span class="text-title">{{ \Carbon\Carbon::now()->format('d F Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Metric Cards Grid --}}
    <div class="row g-3 mb-4">
        
        {{-- Card 1: Stok Kritis --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-stat-red shadow-sm rounded-4 h-100 p-3">
                <div class="d-flex justify-content-between align-items-start mb-3 gap-2">
                    <span class="text-uppercase text-danger fs-7 fw-bold tracking-wider">Stok Kritis</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center bg-danger text-white shadow-sm flex-shrink-0" style="width: 40px; height: 40px;">
                        <i class="bi bi-exclamation-triangle-fill fs-6"></i>
                    </div>
                </div>
                <div class="mb-2">
                    <h2 class="fw-bold stat-number m-0 fs-1">{{ $criticalStockCount ?? 0 }}</h2>
                </div>
                <div class="d-flex align-items-center gap-2 mt-auto">
                    <span class="badge bg-danger text-white rounded-pill px-2.5 py-1 fs-8 fw-semibold">Warning</span>
                    <span class="text-sub small text-truncate">Perlu restock supplier</span>
                </div>
            </div>
        </div>

        {{-- Card 2: AVG Yield Rate --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-stat-green shadow-sm rounded-4 h-100 p-3">
                <div class="d-flex justify-content-between align-items-start mb-3 gap-2">
                    <span class="text-uppercase text-success fs-7 fw-bold tracking-wider">Avg Yield Rate</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center bg-success text-white shadow-sm flex-shrink-0" style="width: 40px; height: 40px;">
                        <i class="bi bi-graph-up-arrow fs-6"></i>
                    </div>
                </div>
                <div class="mb-2">
                    <h2 class="fw-bold stat-number m-0 fs-1">{{ number_format($avgYieldRate ?? 0, 2) }}%</h2>
                </div>
                <div class="mt-auto">
                    <span class="text-sub small">Target Efisiensi: <strong class="text-title">&ge; 95.00%</strong></span>
                </div>
            </div>
        </div>

        {{-- Card 3: Total Scrap --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-stat-yellow shadow-sm rounded-4 h-100 p-3">
                <div class="d-flex justify-content-between align-items-start mb-3 gap-2">
                    <span class="text-uppercase text-warning fs-7 fw-bold tracking-wider">Total Scrap</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center bg-warning text-dark shadow-sm flex-shrink-0" style="width: 40px; height: 40px;">
                        <i class="bi bi-trash3-fill fs-6"></i>
                    </div>
                </div>
                <div class="mb-2">
                    <h2 class="fw-bold stat-number m-0 fs-1">{{ number_format($totalScrap ?? 0, 2) }}</h2>
                </div>
                <div class="mt-auto">
                    <span class="text-sub small">Akumulasi limbah produksi</span>
                </div>
            </div>
        </div>

        {{-- Card 4: Batch Aktif --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-stat-blue shadow-sm rounded-4 h-100 p-3">
                <div class="d-flex justify-content-between align-items-start mb-3 gap-2">
                    <span class="text-uppercase text-primary fs-7 fw-bold tracking-wider">Batch Aktif</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center bg-primary text-white shadow-sm flex-shrink-0" style="width: 40px; height: 40px;">
                        <i class="bi bi-arrow-repeat fs-6"></i>
                    </div>
                </div>
                <div class="mb-2">
                    <h2 class="fw-bold stat-number m-0 fs-1">{{ $activeBatchesCount ?? 0 }}</h2>
                </div>
                <div class="mt-auto">
                    <span class="text-sub small">Dalam antrean / proses pabrik</span>
                </div>
            </div>
        </div>

    </div>

    {{-- Chart Section --}}
    <div class="card card-main shadow-sm rounded-4 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-bold text-title m-0"><i class="bi bi-graph-up me-2 text-primary"></i>Tren Yield Rate (6 Bulan Terakhir)</h5>
                <p class="text-sub small m-0 mt-1">Evaluasi tren persentase keberhasilan batch produksi.</p>
            </div>
        </div>
        <div style="min-height: 280px;">
            <canvas id="yieldChart"></canvas>
        </div>
    </div>

</div>
@endsection