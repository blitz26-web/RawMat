@extends('layouts.app')

@section('title', 'Dashboard - RawMat & Waste Control')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold m-0"><i class="bi bi-speedometer2 me-2"></i>Dashboard Analitik</h3>
        <p class="text-muted small m-0">Ringkasan performa efisiensi produksi, limbah, dan kontrol stok bahan baku.</p>
    </div>
    <span class="badge bg-white text-dark border p-2 shadow-sm">
        <i class="bi bi-calendar3 me-1"></i> {{ now()->translatedFormat('d F Y') }}
    </span>
</div>

<!-- 1. Metric Stat Cards -->
<div class="row g-3 mb-4">
    <!-- Card Stok Kritis -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold">STOK KRITIS</div>
                        <div class="fs-3 fw-bold text-danger mt-1">{{ number_format($lowStockCount) }}</div>
                    </div>
                    <div class="bg-danger-subtle text-danger rounded-circle p-3">
                        <i class="bi bi-exclamation-octagon fs-3"></i>
                    </div>
                </div>
                <div class="mt-2 small text-muted">
                    <span class="badge bg-danger text-white">Warning</span> Perlu restock supplier
                </div>
            </div>
        </div>
    </div>

    <!-- Card Rata-rata Yield Rate -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold">AVG YIELD RATE (BULAN INI)</div>
                        <div class="fs-3 fw-bold text-success mt-1">{{ number_format($avgYieldRateMonth, 2) }}%</div>
                    </div>
                    <div class="bg-success-subtle text-success rounded-circle p-3">
                        <i class="bi bi-graph-up-arrow fs-3"></i>
                    </div>
                </div>
                <div class="mt-2 small text-muted">
                    Target Efisiensi: <strong>>= 95.00%</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Total Limbah/Scrap -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold">TOTAL SCRAP (BULAN INI)</div>
                        <div class="fs-3 fw-bold text-warning mt-1">{{ number_format($totalScrapQtyMonth, 2) }}</div>
                    </div>
                    <div class="bg-warning-subtle text-warning rounded-circle p-3">
                        <i class="bi bi-trash3 fs-3"></i>
                    </div>
                </div>
                <div class="mt-2 small text-muted">
                    Akumulasi limbah produksi
                </div>
            </div>
        </div>
    </div>

    <!-- Card Batch Aktif -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold">BATCH AKTIF / DRAFT</div>
                        <div class="fs-3 fw-bold text-primary mt-1">{{ number_format($activeBatchesCount) }}</div>
                    </div>
                    <div class="bg-primary-subtle text-primary rounded-circle p-3">
                        <i class="bi bi-arrow-repeat fs-3"></i>
                    </div>
                </div>
                <div class="mt-2 small text-muted">
                    Dalam antrean / proses pabrik
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 2. Section Charts (Chart.js) -->
<div class="row g-4 mb-4">
    <!-- Line Chart: Tren Yield Rate -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-bold py-3 border-0">
                <i class="bi bi-activity me-1 text-primary"></i> Tren Yield Rate (6 Bulan Terakhir)
            </div>
            <div class="card-body">
                <canvas id="yieldRateChart" style="max-height: 280px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Bar Chart: Top Scrap Material -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-bold py-3 border-0">
                <i class="bi bi-bar-chart-fill me-1 text-warning"></i> Top 5 Bahan Baku Jadi Scrap (Bulan Ini)
            </div>
            <div class="card-body">
                <canvas id="scrapMaterialChart" style="max-height: 280px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- 3. Tabel Quick Action: Bahan Baku Kritis -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-0">
        <span class="fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i> Perhatian: Stok Bahan Baku Kritis (Low Stock)</span>
        <a href="#" class="btn btn-sm btn-outline-secondary">Lihat Semua Material</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th>
                        <th>Nama Bahan Baku</th>
                        <th>Stok Saat Ini</th>
                        <th>Safety Stock</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($criticalMaterials as $mat)
                        <tr>
                            <td><code>{{ $mat->code }}</code></td>
                            <td class="fw-bold">{{ $mat->name }}</td>
                            <td>
                                <span class="text-danger fw-bold">
                                    {{ number_format($mat->current_stock, 2) }} {{ $mat->unit }}
                                </span>
                            </td>
                            <td>{{ number_format($mat->safety_stock, 2) }} {{ $mat->unit }}</td>
                            <td>
                                <span class="badge {{ $mat->stock_badge_class }}">
                                    {{ $mat->stock_status_label }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-danger shadow-sm">
                                    <i class="bi bi-cart-plus me-1"></i> Order Stock (PO)
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-shield-check text-success fs-4 d-block mb-1"></i>
                                Semua stok bahan baku dalam kondisi aman di atas batas Safety Stock.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- CDN Chart.js v4 -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- 1. Line Chart: Tren Yield Rate ---
        const yieldCtx = document.getElementById('yieldRateChart').getContext('2d');
        new Chart(yieldCtx, {
            type: 'line',
            data: {
                labels: @json($yieldChartLabels),
                datasets: [{
                    label: 'Yield Rate (%)',
                    data: @json($yieldChartData),
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 50,
                        max: 100,
                        ticks: {
                            callback: function(value) { return value + '%'; }
                        }
                    }
                }
            }
        });

        // --- 2. Bar Chart: Top Scrap Material ---
        const scrapCtx = document.getElementById('scrapMaterialChart').getContext('2d');
        new Chart(scrapCtx, {
            type: 'bar',
            data: {
                labels: @json($scrapChartLabels),
                datasets: [{
                    label: 'Total Scrap Qty',
                    data: @json($scrapChartData),
                    backgroundColor: '#ffc107',
                    borderColor: '#ffc107',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endpush