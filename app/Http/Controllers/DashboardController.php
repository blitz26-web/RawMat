<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\ProductionBatch;
use App\Models\ScrapLog;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $lowStockCount = Material::whereColumn('current_stock', '<=', 'safety_stock')->count();

        $avgYieldRateMonth = ProductionBatch::where('status', 'completed')
            ->whereYear('end_date', now()->year)
            ->whereMonth('end_date', now()->month)
            ->avg('yield_rate') ?? 0.00;

        $totalScrapQtyMonth = ScrapLog::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('scrap_qty');

        $activeBatchesCount = ProductionBatch::whereIn('status', ['draft', 'in_progress'])->count();

        $topScrapMaterials = ScrapLog::select('material_id', DB::raw('SUM(scrap_qty) as total_scrap'))
            ->with('material:id,name,unit')
            ->whereYear('scrap_logs.created_at', now()->year)
            ->whereMonth('scrap_logs.created_at', now()->month)
            ->groupBy('material_id')
            ->orderByDesc('total_scrap')
            ->limit(5)
            ->get();

        $scrapChartLabels = $topScrapMaterials->map(fn($item) => $item->material->name ?? 'Unknown');
        $scrapChartData   = $topScrapMaterials->pluck('total_scrap');

        $monthlyYieldData = ProductionBatch::select(
                DB::raw("DATE_FORMAT(end_date, '%Y-%m') as month_key"),
                DB::raw("AVG(yield_rate) as avg_yield")
            )
            ->where('status', 'completed')
            ->where('end_date', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('month_key')
            ->orderBy('month_key', 'asc')
            ->pluck('avg_yield', 'month_key');

        $yieldChartLabels = [];
        $yieldChartData   = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $key   = $month->format('Y-m');
            
            $yieldChartLabels[] = $month->translatedFormat('M Y');
            $yieldChartData[]   = isset($monthlyYieldData[$key]) ? round($monthlyYieldData[$key], 2) : 0;
        }

        $criticalMaterials = Material::whereColumn('current_stock', '<=', 'safety_stock')
            ->orderBy('current_stock', 'asc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'lowStockCount',
            'avgYieldRateMonth',
            'totalScrapQtyMonth',
            'activeBatchesCount',
            'scrapChartLabels',
            'scrapChartData',
            'yieldChartLabels',
            'yieldChartData',
            'criticalMaterials'
        ));
    }
}