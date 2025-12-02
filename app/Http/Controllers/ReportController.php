<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\PropertiesModel;
use App\Models\User;
use App\Models\SalesSummary;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    public function index()
    {
        // === Summary counts ===
        $totalProperties = PropertiesModel::count();
        $totalAgents     = User::role('Agent')->count();
        $monthlyRevenue  = 200000; // TODO: replace with real query if needed

        // === Chart data for the last 4 months ===
        $performanceData = PropertiesModel::select(
                DB::raw("COUNT(*) as property_count"),
                DB::raw("DATE_FORMAT(created_at, '%b') as month"),
                DB::raw("MIN(created_at) as min_created_at")
            )
            ->where('created_at', '>=', now()->subMonths(4))
            ->groupBy('month')
            ->orderBy('min_created_at')
            ->get();

        $labels         = $performanceData->pluck('month')->toArray();
        $propertyCounts = $performanceData->pluck('property_count')->toArray();

        // Demo only
        $clientCounts = array_map(fn($v) => max(0, $v - 20), $propertyCounts);

        // === Distinct years from leads table (for Leads dropdown) ===
        $yearsFromDb = DB::table('leads')
            ->distinct()
            ->orderBy('year')
            ->pluck('year')
            ->toArray();

        $startYear   = 2022;
        $maxYearInDb = !empty($yearsFromDb) ? max($yearsFromDb) : $startYear;
        $endYear     = max($maxYearInDb, now()->year);

        $reportYears = range($startYear, $endYear);

        if (empty($reportYears)) {
            $reportYears = [now()->year];
        }

        return view('report.index', compact(
            'totalProperties',
            'totalAgents',
            'monthlyRevenue',
            'labels',
            'propertyCounts',
            'clientCounts',
            'reportYears'
        ));
    }

    public function exportCsv()
    {
        $csvData = [
            ['Title', 'Value'],
            ['Total Properties', PropertiesModel::count()],
            ['Total Agents', User::role('Agent')->count()],
            ['Revenue', 200000], // Replace with dynamic value if needed
        ];

        $filename = 'report_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        ];

        $callback = function () use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        $data = [
            'totalProperties' => PropertiesModel::count(),
            'totalAgents'     => User::role('Agent')->count(),
            'monthlyRevenue'  => '$200,000',
        ];

        $pdf = Pdf::loadView('report.pdf', $data);
        return $pdf->download('report_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    /**
     * LEADS DASHBOARD (partial loaded via AJAX)
     */
    public function leads(Request $request)
    {
        // Years in DB for dropdown
        $yearsFromDb = DB::table('leads')
            ->distinct()
            ->orderBy('year')
            ->pluck('year')
            ->toArray();

        $startYear   = 2022;
        $maxYearInDb = !empty($yearsFromDb) ? max($yearsFromDb) : $startYear;
        $endYear     = max($maxYearInDb, now()->year);
        $reportYears = range($startYear, $endYear);

        $defaultYear  = !empty($reportYears) ? max($reportYears) : now()->year;
        $year         = (int) ($request->input('year') ?: $defaultYear);
        $selectedYear = $year;

        // Fixed month order
        $MONTHS = [
            'January','February','March','April','May','June',
            'July','August','September','October','November','December'
        ];

        // Monthly totals for selected year
        $leadStatsRaw = DB::table('leads')
            ->selectRaw('month, SUM(`count`) AS total')
            ->where('year', $year)
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $leadStats = collect($MONTHS)->map(function ($m) use ($leadStatsRaw) {
            $row = $leadStatsRaw->get($m);
            return ['month' => $m, 'total' => $row ? (int)$row->total : 0];
        })->values()->all();

        // Leads-based totals
        $allLeadsTotal = (int) DB::table('leads')
            ->where('year', $year)
            ->sum('count');

        $allLeadsAvg = (int) ceil($allLeadsTotal / 12);

        // Sales summary (if available)
        $summary = SalesSummary::where('year', $year)->first();
        $salesThisYear    = $summary ? (int) $summary->total_sales : 0;
        $yearlyTarget     = $summary ? max((int)$summary->total_sales, 1) : 50;
        $salesThisQuarter = 0; // TODO: real quarterly numbers
        $quarterTarget    = (int) ceil($yearlyTarget / 4);

        // Location breakdown
        $leadsByLocation = DB::table('leads')
            ->selectRaw('COALESCE(location,"Unknown") as location, SUM(`count`) as total')
            ->where('year', $year)
            ->groupBy('location')
            ->pluck('total', 'location')
            ->toArray();

        $avgByLocation = [];
        foreach ($leadsByLocation as $loc => $tot) {
            $avgByLocation[$loc] = (int) ceil(((int)$tot) / 12);
        }

        // Source breakdown
        $leadsBySource = DB::table('leads')
            ->selectRaw('COALESCE(source,"Unknown") as source, SUM(`count`) as total')
            ->where('year', $year)
            ->groupBy('source')
            ->pluck('total', 'source')
            ->toArray();

        return view('report.partials.leads', compact(
            'reportYears','selectedYear',
            'leadStats',
            'salesThisYear','salesThisQuarter','quarterTarget','yearlyTarget',
            'allLeadsTotal','allLeadsAvg',
            'leadsByLocation','avgByLocation','leadsBySource'
        ));

        $comparisonData = $this->buildComparisonData();

        return view('report.partials.leads', compact(
            'reportYears','selectedYear',
            'leadStats',
            'salesThisYear','salesThisQuarter','quarterTarget','yearlyTarget',
            'allLeadsTotal','allLeadsAvg',
            'leadsByLocation','avgByLocation','leadsBySource',
            'comparisonData'   // <- add this
        ));

    }

    /**
     * Same LEADS dashboard, but full-page route: /reports/{year}
     */
    public function showYear(int $year)
    {
        $yearsFromDb = DB::table('leads')
            ->distinct()
            ->orderBy('year')
            ->pluck('year')
            ->toArray();

        $startYear   = 2022;
        $maxYearInDb = !empty($yearsFromDb) ? max($yearsFromDb) : $startYear;
        $endYear     = max($maxYearInDb, now()->year);
        $reportYears = range($startYear, $endYear);

        if (empty($reportYears)) {
            $reportYears = [$year];
        }

        $selectedYear = $year;

        // Months
        $MONTHS = [
            'January','February','March','April','May','June',
            'July','August','September','October','November','December'
        ];

        // Monthly leads
        $leadStatsRaw = DB::table('leads')
            ->selectRaw('month, SUM(`count`) AS total')
            ->where('year', $year)
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $leadStats = collect($MONTHS)->map(function ($m) use ($leadStatsRaw) {
            $row = $leadStatsRaw->get($m);
            return ['month' => $m, 'total' => $row ? (int)$row->total : 0];
        })->values()->all();

        // Totals
        $allLeadsTotal = (int) DB::table('leads')
            ->where('year', $year)
            ->sum('count');

        $allLeadsAvg = (int) ceil($allLeadsTotal / 12);

        // Location
        $leadsByLocation = DB::table('leads')
            ->selectRaw('COALESCE(location,"Unknown") as location, SUM(`count`) as total')
            ->where('year', $year)
            ->groupBy('location')
            ->pluck('total', 'location')
            ->toArray();

        $avgByLocation = [];
        foreach ($leadsByLocation as $loc => $tot) {
            $avgByLocation[$loc] = (int) ceil(((int)$tot) / 12);
        }

        // Source
        $leadsBySource = DB::table('leads')
            ->selectRaw('COALESCE(source,"Unknown") as source, SUM(`count`) as total')
            ->where('year', $year)
            ->groupBy('source')
            ->pluck('total', 'source')
            ->toArray();

        // Sales summary
        $summary = SalesSummary::where('year', $year)->first();

        $salesThisYear    = $summary ? (int) $summary->total_sales : 0;
        $yearlyTarget     = $summary ? max((int)$summary->total_sales, 1) : 50;
        $salesThisQuarter = 0;
        $quarterTarget    = (int) ceil($yearlyTarget / 4);

        return view('report.partials.leads', compact(
            'reportYears','selectedYear',
            'leadStats',
            'salesThisYear','salesThisQuarter','quarterTarget','yearlyTarget',
            'allLeadsTotal','allLeadsAvg',
            'leadsByLocation','avgByLocation','leadsBySource'
        ));

        $comparisonData = $this->buildComparisonData();

        return view('report.partials.leads', compact(
            'reportYears','selectedYear',
            'leadStats',
            'salesThisYear','salesThisQuarter','quarterTarget','yearlyTarget',
            'allLeadsTotal','allLeadsAvg',
            'leadsByLocation','avgByLocation','leadsBySource',
            'comparisonData'   // <- add this
        ));

    }

    /**
     * JSON: multi-year monthly leads series (for line graph)
     */
    public function leadsTrend(Request $request)
    {
        // ?years[]=2022&years[]=2023 OR ?years=2022,2023
        $requested = $request->input('years', []);

        if (is_string($requested)) {
            $requested = array_filter(array_map('trim', explode(',', $requested)));
        }

        $requestedYears = array_values(array_unique(array_map('intval', (array) $requested)));

        if (empty($requestedYears)) {
            $requestedYears = DB::table('leads')
                ->distinct()
                ->orderBy('year')
                ->pluck('year')
                ->map(fn ($y) => (int) $y)
                ->toArray();
        }

        if (empty($requestedYears)) {
            return response()->json([
                'months' => [],
                'series' => [],
            ]);
        }

        $MONTHS = [
            'January','February','March','April','May','June',
            'July','August','September','October','November','December',
        ];

        $rows = DB::table('leads')
            ->selectRaw('year, month, SUM(`count`) as total')
            ->whereIn('year', $requestedYears)
            ->groupBy('year', 'month')
            ->get();

        $byYearMonth = [];
        foreach ($rows as $row) {
            $y = (int) $row->year;
            $m = $row->month;
            $byYearMonth[$y][$m] = (int) $row->total;
        }

        $series = [];
        foreach ($requestedYears as $y) {
            $series[$y] = [];
            foreach ($MONTHS as $m) {
                $series[$y][] = $byYearMonth[$y][$m] ?? 0;
            }
        }

        return response()->json([
            'months' => $MONTHS,
            'years'  => $requestedYears,
            'series' => $series,
        ]);
    }

    /**
     * JSON: portal comparison (per source) between two years
     */
    public function portalComparison(Request $request)
    {
        $yearsInLeads = DB::table('leads')
            ->select('year')
            ->distinct()
            ->orderBy('year')
            ->pluck('year')
            ->toArray();

        if (count($yearsInLeads) < 2) {
            return response()->json([
                'message' => 'Need at least 2 years of leads data to compare.'
            ], 400);
        }

        $latestYear = (int) end($yearsInLeads);
        $prevYear   = (int) ($yearsInLeads[count($yearsInLeads) - 2]);

        $baseYear    = (int) ($request->input('base_year')    ?? $prevYear);
        $compareYear = (int) ($request->input('compare_year') ?? $latestYear);

        $years = [$baseYear, $compareYear];

        $rows = DB::table('leads')
            ->select('year', 'source', DB::raw('SUM(`count`) as total_leads'))
            ->whereIn('year', $years)
            ->groupBy('year', 'source')
            ->get();

        $totalsByYear = [];
        $byPortalYear = [];

        foreach ($rows as $row) {
            $y      = (int) $row->year;
            $portal = $row->source ?: 'Unknown';
            $total  = (int) $row->total_leads;

            $totalsByYear[$y] = ($totalsByYear[$y] ?? 0) + $total;
            $byPortalYear[$portal][$y] = $total;
        }

        $portals = [];

        foreach ($byPortalYear as $portal => $yearsData) {
            $baseTotal = (int) ($yearsData[$baseYear]    ?? 0);
            $compTotal = (int) ($yearsData[$compareYear] ?? 0);

            $baseYearTotal = $totalsByYear[$baseYear]    ?? 0;
            $compYearTotal = $totalsByYear[$compareYear] ?? 0;

            $basePctOfYear = $baseYearTotal > 0
                ? round($baseTotal / $baseYearTotal * 100, 1)
                : 0.0;

            $compPctOfYear = $compYearTotal > 0
                ? round($compTotal / $compYearTotal * 100, 1)
                : 0.0;

            $deltaPct = $baseTotal > 0
                ? round(($compTotal - $baseTotal) / $baseTotal * 100, 1)
                : null;

            $portals[] = [
                'portal'              => $portal,
                'base_year'           => $baseYear,
                'base_total'          => $baseTotal,
                'base_pct_of_year'    => $basePctOfYear,
                'compare_year'        => $compareYear,
                'compare_total'       => $compTotal,
                'compare_pct_of_year' => $compPctOfYear,
                'delta_pct'           => $deltaPct,
            ];
        }

        usort($portals, fn ($a, $b) => $b['compare_total'] <=> $a['compare_total']);

        return response()->json([
            'base_year'    => $baseYear,
            'compare_year' => $compareYear,
            'totals'       => [
                $baseYear    => $totalsByYear[$baseYear]    ?? 0,
                $compareYear => $totalsByYear[$compareYear] ?? 0,
            ],
            'portals'      => $portals,
        ]);
    }

    /**
     * HTML partial: Data Comparison block
     * (used when you fetch /report/partials/data-comparison into the overlay container)
     */
    public function dataComparisonPartial()
    {
        $years = DB::table('leads')
            ->select('year')
            ->distinct()
            ->orderBy('year')
            ->pluck('year')
            ->toArray();

        if (empty($years)) {
            $seriesLeads = [];
            $yoyRows     = [];

            return view('report.partials.data_comparison', compact(
                'years', 'seriesLeads', 'yoyRows'
            ));
        }

        $MONTHS = [
            'January','February','March','April','May','June',
            'July','August','September','October','November','December'
        ];

        $seriesLeads = [];
        $yoyRows     = [];
        $prevTotal   = null;

        foreach ($years as $year) {
            $raw = DB::table('leads')
                ->selectRaw('month, SUM(`count`) as total')
                ->where('year', $year)
                ->groupBy('month')
                ->get()
                ->keyBy('month');

            $monthlyValues = [];
            $total         = 0;

            foreach ($MONTHS as $m) {
                $val = 0;
                if ($raw->has($m)) {
                    $val = (int) $raw->get($m)->total;
                }
                $monthlyValues[] = $val;
                $total          += $val;
            }

            $avg = (int) ceil($total / 12);

            $seriesLeads[] = [
                'year'   => $year,
                'months' => $MONTHS,
                'values' => $monthlyValues,
                'total'  => $total,
                'avg'    => $avg,
            ];

            $diff = null;
            $pct  = null;

            if (!is_null($prevTotal)) {
                $diff = $total - $prevTotal;
                if ($prevTotal > 0) {
                    $pct = round(($diff / $prevTotal) * 100, 1);
                }
            }

            $yoyRows[] = [
                'year'  => $year,
                'total' => $total,
                'avg'   => $avg,
                'diff'  => $diff,
                'pct'   => $pct,
            ];

            $prevTotal = $total;
        }

        return view('report.partials.data_comparison', compact(
            'years',
            'seriesLeads',
            'yoyRows'
        ));
    }

    // In App\Http\Controllers\ReportController

    protected function buildComparisonData()
    {
        // All years we have in leads table
        $years = DB::table('leads')
            ->select('year')
            ->distinct()
            ->orderBy('year')
            ->pluck('year')
            ->toArray();

        if (empty($years)) {
            return [
                'years'       => [],
                'leadsByYear' => [],
                'metrics'     => [],
            ];
        }

        $MONTHS = [
            'January','February','March','April','May','June',
            'July','August','September','October','November','December',
        ];

        // Mapping for metrics => DB fields
        $locationMap = [
            'paphos'    => 'Paphos',
            'limassol'  => 'Limassol',
            'larnaca'   => 'Larnaca',
            'famagusta' => 'Famagusta',
        ];

        $sourceMap = [
            'zoopla'    => 'Zoopla',
            'rightmove' => 'Rightmove',
            'apits'     => 'APITS',
            'slv'       => 'SLV',
            'hos'       => 'HoS',
            'facebook'  => 'Facebook',
        ];

        $leadsByYear = [];
        $metrics     = [];

        foreach ($years as $year) {
            $year = (int) $year;

            // ===== Total leads per month (base series) =====
            $rowsTotal = DB::table('leads')
                ->selectRaw('month, SUM(`count`) as total')
                ->where('year', $year)
                ->groupBy('month')
                ->get()
                ->keyBy('month');

            $totalSeries = [];
            foreach ($MONTHS as $m) {
                $totalSeries[] = isset($rowsTotal[$m]) ? (int)$rowsTotal[$m]->total : 0;
            }

            $leadsByYear[$year]      = $totalSeries;
            $metrics[$year]['leads'] = $totalSeries;

            // ===== Locations =====
            foreach ($locationMap as $key => $locName) {
                $rowsLoc = DB::table('leads')
                    ->selectRaw('month, SUM(`count`) as total')
                    ->where('year', $year)
                    ->where('location', $locName)
                    ->groupBy('month')
                    ->get()
                    ->keyBy('month');

                $seriesLoc = [];
                foreach ($MONTHS as $m) {
                    $seriesLoc[] = isset($rowsLoc[$m]) ? (int)$rowsLoc[$m]->total : 0;
                }
                $metrics[$year][$key] = $seriesLoc;
            }

            // ===== Sources (portals) =====
            foreach ($sourceMap as $key => $srcName) {
                $rowsSrc = DB::table('leads')
                    ->selectRaw('month, SUM(`count`) as total')
                    ->where('year', $year)
                    ->where('source', $srcName)
                    ->groupBy('month')
                    ->get()
                    ->keyBy('month');

                $seriesSrc = [];
                foreach ($MONTHS as $m) {
                    $seriesSrc[] = isset($rowsSrc[$m]) ? (int)$rowsSrc[$m]->total : 0;
                }
                $metrics[$year][$key] = $seriesSrc;
            }
        }

        // IMPORTANT: preserve year as a string key so JS can do allMetrics["2023"]
        $leadsByYearOut = [];
        foreach ($leadsByYear as $y => $series) {
            $leadsByYearOut[(string)$y] = $series;
        }

        $metricsOut = [];
        foreach ($metrics as $y => $mset) {
            $metricsOut[(string)$y] = $mset;
        }

        return [
            'years'       => array_map('intval', $years),
            'leadsByYear' => $leadsByYearOut,
            'metrics'     => $metricsOut,
        ];
    }


}
