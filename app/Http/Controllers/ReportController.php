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
use Carbon\Carbon;

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

    public function properties(Request $request)
    {
        // Years in DB for dropdown (based on created_at)
        $yearsFromDb = DB::table('properties')
            ->selectRaw('YEAR(created_at) as year')
            ->whereNotNull('created_at')
            ->distinct()
            ->orderBy('year')
            ->pluck('year')
            ->filter()
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
        $propertyStatsRaw = DB::table('properties')
            ->selectRaw('MONTH(created_at) as month_num, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->groupBy('month_num')
            ->get()
            ->keyBy('month_num');

        $propertyStats = collect($MONTHS)->map(function ($m, $idx) use ($propertyStatsRaw) {
            $monthNum = $idx + 1; // 1..12
            $row = $propertyStatsRaw->get($monthNum);
            return [
                'month' => $m,
                'total' => $row ? (int) $row->total : 0
            ];
        })->values()->all();

        // ✅ KPIs
        $totalListings = (int) DB::table('properties')->count();

        $avgValue = (float) DB::table('properties')
            ->whereNotNull('price')
            ->where('price', '>', 0)
            ->avg('price');

        $listedThisMonth = (int) DB::table('properties')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | ✅ DUMMY DATA (for now) — to satisfy report requirements
        |--------------------------------------------------------------------------
        | You can build the UI now, then we replace these with real queries later.
        */

        // 1) Listings per district
        $listingsPerDistrict = [
            'Paphos'    => 420,
            'Limassol'  => 310,
            'Larnaca'   => 220,
            'Famagusta' => 116,
        ];

        // 2) Avg price per district
        $avgPricePerDistrict = [
            'Paphos'    => 285000,
            'Limassol'  => 410000,
            'Larnaca'   => 240000,
            'Famagusta' => 198000,
        ];

        // 3) Listings per property type
        $listingsPerType = [
            'Villa'      => 380,
            'Apartment'  => 520,
            'Townhouse'  => 110,
            'Plot'       => 56,
        ];

        // 4) Property type × district (matrix)
        $typeByDistrict = [
            'Paphos' => [
                'Villa'     => 180,
                'Apartment' => 190,
                'Townhouse' => 35,
                'Plot'      => 15,
            ],
            'Limassol' => [
                'Villa'     => 120,
                'Apartment' => 160,
                'Townhouse' => 20,
                'Plot'      => 10,
            ],
            'Larnaca' => [
                'Villa'     => 60,
                'Apartment' => 130,
                'Townhouse' => 40,
                'Plot'      =>  -10 + 20, // (keeps it obvious "dummy"; replace later)
            ],
            'Famagusta' => [
                'Villa'     => 20,
                'Apartment' => 40,
                'Townhouse' => 15,
                'Plot'      => 41,
            ],
        ];

        // 5) Listings per portal
        $listingsPerPortal = [
            'SLV'        => 520,
            'Kyero'      => 310,
            'Ultrait'    => 180,
            'Zoopla'     => 56,
            'Rightmove'  => 0,
        ];

        return view('report.partials.properties', compact(
            'reportYears','selectedYear',
            'propertyStats',
            'totalListings','avgValue','listedThisMonth',
            'listingsPerDistrict','avgPricePerDistrict',
            'listingsPerType','typeByDistrict',
            'listingsPerPortal'
        ));
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

        // KPI totals (normalize source names so it won’t show zero)
        $kpiSlv = 0;
        $kpiApits = 0;
        $kpiZoopla = 0;
        $kpiRightmove = 0;

        foreach ($leadsBySource as $source => $total) {
            $src = strtolower(trim($source));

            if (str_contains($src, 'slv')) {
                $kpiSlv += (int) $total;
            }

            if (str_contains($src, 'apit')) {
                $kpiApits += (int) $total;
            }

            if (str_contains($src, 'zoopla')) {
                $kpiZoopla += (int) $total;
            }

            if (str_contains($src, 'right')) {
                $kpiRightmove += (int) $total;
            }
        }

        $totalLeadsKpi = $kpiSlv + $kpiApits + $kpiZoopla + $kpiRightmove;

        return view('report.partials.leads', compact(
            'reportYears','selectedYear',
            'leadStats',
            'salesThisYear','salesThisQuarter','quarterTarget','yearlyTarget',
            'allLeadsTotal','allLeadsAvg',
            'leadsByLocation','avgByLocation','leadsBySource',
            'kpiSlv','kpiApits','kpiZoopla','kpiRightmove','totalLeadsKpi'
        ));


        $comparisonData = $this->buildComparisonData();

        return view('report.partials.leads', compact(
            'reportYears','selectedYear',
            'leadStats',
            'salesThisYear','salesThisQuarter','quarterTarget','yearlyTarget',
            'allLeadsTotal','allLeadsAvg',
            'leadsByLocation','avgByLocation','leadsBySource',
            'comparisonData'
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

        // KPI totals (normalize source names so it won’t show zero)
        $kpiSlv = 0;
        $kpiApits = 0;
        $kpiZoopla = 0;
        $kpiRightmove = 0;

        foreach ($leadsBySource as $source => $total) {
            $src = strtolower(trim($source));

            if (str_contains($src, 'slv')) {
                $kpiSlv += (int) $total;
            }

            if (str_contains($src, 'apit')) {
                $kpiApits += (int) $total;
            }

            if (str_contains($src, 'zoopla')) {
                $kpiZoopla += (int) $total;
            }

            if (str_contains($src, 'right')) {
                $kpiRightmove += (int) $total;
            }
        }

        $totalLeadsKpi = $kpiSlv + $kpiApits + $kpiZoopla + $kpiRightmove;

        return view('report.partials.leads', compact(
            'reportYears','selectedYear',
            'leadStats',
            'salesThisYear','salesThisQuarter','quarterTarget','yearlyTarget',
            'allLeadsTotal','allLeadsAvg',
            'leadsByLocation','avgByLocation','leadsBySource',
            'kpiSlv','kpiApits','kpiZoopla','kpiRightmove','totalLeadsKpi'
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

    public function sales(Request $request)
    {
        /**
         * SALES DASHBOARD (single-year view)
         *
         * What it does NOW:
         * 1) Year dropdown: based on years available in `leads` (since charts are leads-based for now)
         * 2) Charts/Tables below: pulled from `leads` table (monthly, location, source)
         * 3) KPI cards (TOP): pulled from `sales_area_values` table (MONEY by AREA) -> 5 cards
         * 4) Total Sales bar: sum of the 5 KPI money values
         * 5) Sales totals: pulled from `sales_summary` (total_sales/commission/value, etc.)
         * 6) Data Comparison: left as null for later
         */

        // ----------------------------
        // 1) Years for dropdown
        // ----------------------------
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

        // ----------------------------
        // 2) Leads monthly chart (Jan-Dec)
        // ----------------------------
        $MONTHS = [
            'January','February','March','April','May','June',
            'July','August','September','October','November','December'
        ];

        $leadStatsRaw = DB::table('leads')
            ->selectRaw('month, SUM(`count`) AS total')
            ->where('year', $year)
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $leadStats = collect($MONTHS)->map(function ($m) use ($leadStatsRaw) {
            $row = $leadStatsRaw->get($m);
            return ['month' => $m, 'total' => $row ? (int) $row->total : 0];
        })->values()->all();

        // ----------------------------
        // 3) Leads totals
        // ----------------------------
        $allLeadsTotal = (int) DB::table('leads')->where('year', $year)->sum('count');
        $allLeadsAvg   = (int) ceil($allLeadsTotal / 12);

        // ----------------------------
        // 4) KPI cards (MONEY by AREA) - 5 cards
        //    Requires table: sales_area_values (year, area, total_value)
        // ----------------------------
        $areaMoney = DB::table('sales_area_values')
            ->where('year', $year)
            ->pluck('total_value', 'area')
            ->toArray();

        // Always return consistent 5 keys (so blade doesn't break)
        $kpiAreas = [
            'Paphos'    => (float) ($areaMoney['Paphos'] ?? 0),
            'Limassol'  => (float) ($areaMoney['Limassol'] ?? 0),
            'Famagusta' => (float) ($areaMoney['Famagusta'] ?? 0),
            'Pissouri'  => (float) ($areaMoney['Pissouri'] ?? 0),
            'Larnaca'   => (float) ($areaMoney['Larnaca'] ?? 0),
        ];

        // Total Sales money = sum of all 5 areas
        $totalSalesMoney = array_sum($kpiAreas);

        // ----------------------------
        // 5) Leads breakdown tables/pies
        // ----------------------------
        $leadsByLocation = DB::table('leads')
            ->selectRaw('COALESCE(location,"Unknown") as location, SUM(`count`) as total')
            ->where('year', $year)
            ->groupBy('location')
            ->pluck('total', 'location')
            ->toArray();

        $avgByLocation = [];
        foreach ($leadsByLocation as $loc => $tot) {
            $avgByLocation[$loc] = (int) ceil(((int) $tot) / 12);
        }

        $leadsBySource = DB::table('leads')
            ->selectRaw('COALESCE(source,"Unknown") as source, SUM(`count`) as total')
            ->where('year', $year)
            ->groupBy('source')
            ->pluck('total', 'source')
            ->toArray();

        // ----------------------------
        // 6) Sales summary (year totals)
        // ----------------------------
        $summary = SalesSummary::where('year', $year)->first();

        $salesThisYear    = $summary ? (int) $summary->total_sales : 0;
        $yearlyTarget     = $summary ? max((int) $summary->total_sales, 1) : 50;
        $salesThisQuarter = 0; // TODO later
        $quarterTarget    = (int) ceil($yearlyTarget / 4);

        // ----------------------------
        // 7) Data Comparison (later)
        // ----------------------------
        $comparisonData = null;

        return view('report.partials.sales', compact(
            'reportYears',
            'selectedYear',
            'leadStats',
            'salesThisYear',
            'salesThisQuarter',
            'quarterTarget',
            'yearlyTarget',
            'allLeadsTotal',
            'allLeadsAvg',
            'kpiAreas',
            'totalSalesMoney',
            'leadsByLocation',
            'avgByLocation',
            'leadsBySource',
            'comparisonData'
        ));
    }

    public function listings(Request $request)
    {
        /**
         * REQUIRED at top of ReportController.php:
         * use Carbon\Carbon;
         * use Illuminate\Support\Facades\Schema;
         */

        // ---------------------------------------
        // Years for dropdown (from properties.created_at)
        // ---------------------------------------
        $yearsFromDb = DB::table('properties')
            ->whereNotNull('created_at')
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year')
            ->pluck('year')
            ->filter()
            ->toArray();

        $startYear   = 2022;
        $maxYearInDb = !empty($yearsFromDb) ? max($yearsFromDb) : $startYear;
        $endYear     = max($maxYearInDb, now()->year);
        $reportYears = range($startYear, $endYear);

        $defaultYear  = !empty($reportYears) ? max($reportYears) : now()->year;
        $year         = (int) ($request->input('year') ?: $defaultYear);
        $selectedYear = $year;

        $yearStart = Carbon::create($year, 1, 1)->startOfDay();
        $yearEnd   = Carbon::create($year, 12, 31)->endOfDay();

        // KPI: "this month"
        $monthStart = now()->startOfMonth();
        $monthEnd   = now()->endOfMonth();

        // ---------------------------------------
        // Base query for the selected year
        // ---------------------------------------
        $baseYearQuery = DB::table('properties')
            ->whereNotNull('created_at')
            ->whereBetween('created_at', [$yearStart, $yearEnd]);

        // ---------------------------------------
        // Detect columns safely (prevents SQL errors)
        // ---------------------------------------
        $regionColumn   = Schema::hasColumn('properties', 'region')   ? 'region'   : null;
        $locationColumn = Schema::hasColumn('properties', 'location') ? 'location' : null;
        $townColumn     = Schema::hasColumn('properties', 'town')     ? 'town'     : null;

        // ---------------------------------------
        // KPIs
        // ---------------------------------------
        $activeListings = (int) (clone $baseYearQuery)->count();

        $newThisMonth = (int) DB::table('properties')
            ->whereNotNull('created_at')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->count();

        $removedThisMonth = 0;

        // Avg DOM: if published_at exists (else 0)
        $avgDom = (int) round(
            (float) (clone $baseYearQuery)
                ->whereNotNull('published_at')
                ->selectRaw('AVG(DATEDIFF(NOW(), published_at)) as avg_dom')
                ->value('avg_dom')
        );

        $kpis = [
            'active'             => $activeListings,
            'new_this_month'     => $newThisMonth,
            'removed_this_month' => $removedThisMonth,
            'avg_dom'            => $avgDom ?: 0,
        ];

        // ---------------------------------------
        // Charts
        // ---------------------------------------

        // By Property Type
        $byType = (clone $baseYearQuery)
            ->selectRaw('COALESCE(NULLIF(TRIM(property_type),""),"Unknown") as label, COUNT(*) as total')
            ->groupBy('label')
            ->pluck('total', 'label')
            ->toArray();

        // By Status (use listing_type as status-like)
        $byStatus = (clone $baseYearQuery)
            ->selectRaw('COALESCE(NULLIF(TRIM(listing_type),""),"Unknown") as label, COUNT(*) as total')
            ->groupBy('label')
            ->pluck('total', 'label')
            ->toArray();

        /**
         * TEMP PUSH-SAFE:
         * Hardcode the "Listings by Region" pie using a split of the selected year's total listings.
         * (So it ALWAYS renders even if region/location values are messy.)
         */
        $totalListings = $activeListings ?: 3359;

        $byRegion = [
            'Paphos'    => (int) round($totalListings * 0.45),
            'Limassol'  => (int) round($totalListings * 0.25),
            'Famagusta' => (int) round($totalListings * 0.15),
            'Larnaca'   => (int) round($totalListings * 0.10),
            'Pissouri'  => 0,
        ];
        $byRegion['Pissouri'] += ($totalListings - array_sum($byRegion)); // fix rounding

        /**
         * Listings by Location (your 5 areas)
         * Uses best available: region -> town -> location (only if columns exist)
         * If it still ends up 0 for all, fallback to the same hardcoded split.
         */
        $allowedLocations = ['Paphos', 'Limassol', 'Famagusta', 'Pissouri', 'Larnaca'];

        // Build dynamic COALESCE safely (no missing column references)
        $coalesceParts = [];
        if ($regionColumn)   $coalesceParts[] = "NULLIF(TRIM($regionColumn),'')";
        if ($townColumn)     $coalesceParts[] = "NULLIF(TRIM($townColumn),'')";
        if ($locationColumn) $coalesceParts[] = "NULLIF(TRIM($locationColumn),'')";
        $coalesceSql = !empty($coalesceParts)
            ? ('COALESCE(' . implode(',', $coalesceParts) . ",'Unknown')")
            : "'Unknown'";

        $byLocationRaw = (clone $baseYearQuery)
            ->selectRaw("$coalesceSql as label, COUNT(*) as total")
            ->groupBy('label')
            ->pluck('total', 'label')
            ->toArray();

        // Normalize to only the 5 locations
        $byLocation = [];
        foreach ($allowedLocations as $loc) {
            $byLocation[$loc] = 0;
        }

        foreach ($byLocationRaw as $label => $total) {
            $labelNorm = strtolower(trim((string) $label));

            // exact match
            foreach ($allowedLocations as $loc) {
                if ($labelNorm === strtolower($loc)) {
                    $byLocation[$loc] += (int) $total;
                    continue 2;
                }
            }

            // soft matches
            if (str_contains($labelNorm, 'paphos'))    { $byLocation['Paphos']    += (int) $total; continue; }
            if (str_contains($labelNorm, 'limassol'))  { $byLocation['Limassol']  += (int) $total; continue; }
            if (str_contains($labelNorm, 'famagusta')) { $byLocation['Famagusta'] += (int) $total; continue; }
            if (str_contains($labelNorm, 'pissouri'))  { $byLocation['Pissouri']  += (int) $total; continue; }
            if (str_contains($labelNorm, 'larnaca'))   { $byLocation['Larnaca']   += (int) $total; continue; }
        }

        // If everything is 0 (data not normalized), fallback so chart renders
        if (array_sum($byLocation) === 0) {
            $byLocation = $byRegion; // same split, push-safe
        }

        // ---------------------------------------
        // Latest listings table
        // ---------------------------------------
        $latestListings = (clone $baseYearQuery)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function ($p) {
                return [
                    'ref'    => $p->reference ?? ('#' . $p->id),
                    'town'   => $p->town ?? '—',
                    'type'   => $p->property_type ?? '—',
                    'price'  => (float) ($p->price ?? 0),
                    'beds'   => (int) ($p->beds ?? 0),
                    'agent'  => $p->managing_agent ?? '—',
                    'status' => $p->listing_type ?? '—',
                ];
            })
            ->values()
            ->all();

        // ---------------------------------------
        // Summary by Region (count + avg price)
        // TEMP: use the SAME buckets (push-safe) so it always shows something
        // ---------------------------------------
        $regionSummary = [];
        foreach ($byRegion as $r => $count) {
            $regionSummary[] = [
                'region'    => $r,
                'count'     => (int) $count,
                'avg_price' => 0,
            ];
        }

        // ---------------------------------------
        // Portal coverage (push-safe: everything Active = SLV)
        // ---------------------------------------
        $totalProperties = max($activeListings, 1);

        $portalCoverage = [
            ['label' => 'SLV',       'done' => $totalProperties, 'total' => $totalProperties],
            ['label' => 'Rightmove', 'done' => 0,                'total' => $totalProperties],
            ['label' => 'APITS',     'done' => 0,                'total' => $totalProperties],
            ['label' => 'Zoopla',    'done' => 0,                'total' => $totalProperties],
            ['label' => 'HoS',       'done' => 0,                'total' => $totalProperties],
        ];

        // Data Comparison later
        $comparisonData = null;

        return view('report.partials.listings', compact(
            'reportYears',
            'selectedYear',
            'kpis',
            'portalCoverage',
            'totalProperties',
            'byRegion',
            'byType',
            'byStatus',
            'byLocation',
            'latestListings',
            'regionSummary',
            'comparisonData'
        ));
    }




    public function units(Request $request)
    {
        $startYear = 2022;
        $endYear = now()->year;

        $reportYears  = range($startYear, $endYear);
        $selectedYear = (int) ($request->input('year') ?: $endYear);

        // Filters
        $filters = [
            'status' => $request->input('status', 'all'), // all|resale|brand_new
            'region' => $request->input('region', 'all'),
            'town'   => $request->input('town', 'all'),
            'type'   => $request->input('type', 'all'),
        ];

        $statusOptions = [
            'all'       => 'Resale + Brand New',
            'resale'    => 'Resale Only',
            'brand_new' => 'Brand New Only',
        ];

        // Cyprus Regions + Towns (dummy for now)
        $regions = ['Paphos', 'Limassol', 'Larnaca', 'Nicosia', 'Famagusta'];

        $townsByRegion = [
            'Paphos' => [
                'Paphos', 'Kato Paphos', 'Chloraka', 'Tala', 'Peyia', 'Coral Bay',
                'Polis', 'Latchi', 'Kissonerga', 'Geroskipou',
            ],
            'Limassol' => [
                'Limassol', 'Germasogeia', 'Agios Tychonas', 'Ypsonas', 'Pyrgos',
                'Pareklisia', 'Episkopi',
            ],
            'Larnaca' => [
                'Larnaca', 'Oroklini', 'Dhekelia', 'Pervolia', 'Meneou', 'Aradippou',
            ],
            'Nicosia' => [
                'Nicosia', 'Strovolos', 'Lakatamia', 'Aglantzia', 'Engomi',
            ],
            'Famagusta' => [
                'Ayia Napa', 'Protaras', 'Paralimni', 'Deryneia', 'Sotira',
            ],
        ];

        $propertyTypes = [
            'Apartment', 'Bungalow', 'Commercial Property', 'Investment Property',
            'Penthouse', 'Plot', 'Studio', 'Town House', 'Villa',
        ];

        // ---------------------------------------------------------
        // Dummy charts (Cyprus-consistent)
        // ---------------------------------------------------------
        $chartByType = [
            'labels'    => ['Apartment','Villa','Penthouse','Town House','Plot','Commercial Property'],
            'resale'    => [34, 18, 10, 12, 7, 4],
            'brand_new' => [14, 6, 8, 3, 2, 1],
        ];

        $chartByRegion = [
            'labels'    => ['Paphos','Limassol','Larnaca','Nicosia','Famagusta'],
            'resale'    => [28, 22, 14, 10, 12],
            'brand_new' => [12, 9, 6, 4, 5],
        ];

        // Town chart (Cyprus-only). Later: compute based on selected region.
        $chartByTown = [
            'labels'    => ['Chloraka','Tala','Peyia','Kato Paphos','Germasogeia','Agios Tychonas','Oroklini','Ayia Napa'],
            'resale'    => [6, 5, 7, 4, 6, 5, 4, 3],
            'brand_new' => [3, 2, 2, 2, 3, 2, 1, 2],
        ];

        // KPIs derived from chartByType totals
        $resaleTotal = array_sum($chartByType['resale']);
        $brandTotal  = array_sum($chartByType['brand_new']);
        $totalUnits  = $resaleTotal + $brandTotal;

        $resalePct = $totalUnits ? round(($resaleTotal / $totalUnits) * 100) : 0;
        $brandPct  = $totalUnits ? (100 - $resalePct) : 0;

        $kpis = [
            'total'     => $totalUnits,
            'resale'    => $resaleTotal,
            'brand_new' => $brandTotal,
            'split'     => "{$resalePct}% / {$brandPct}%",
        ];

        // Tables (dummy, Cyprus-consistent)
        $typeSummary = [
            ['type'=>'Apartment','resale'=>34,'brand_new'=>14,'total'=>48,'avg_price'=>420000],
            ['type'=>'Villa','resale'=>18,'brand_new'=>6,'total'=>24,'avg_price'=>1150000],
            ['type'=>'Penthouse','resale'=>10,'brand_new'=>8,'total'=>18,'avg_price'=>680000],
            ['type'=>'Town House','resale'=>12,'brand_new'=>3,'total'=>15,'avg_price'=>390000],
            ['type'=>'Plot','resale'=>7,'brand_new'=>2,'total'=>9,'avg_price'=>510000],
            ['type'=>'Commercial Property','resale'=>4,'brand_new'=>1,'total'=>5,'avg_price'=>730000],
        ];

        $areaSummary = [
            ['area'=>'Paphos','resale'=>28,'brand_new'=>12,'total'=>40,'avg_price'=>420000],
            ['area'=>'Limassol','resale'=>22,'brand_new'=>9,'total'=>31,'avg_price'=>550000],
            ['area'=>'Larnaca','resale'=>14,'brand_new'=>6,'total'=>20,'avg_price'=>310000],
            ['area'=>'Nicosia','resale'=>10,'brand_new'=>4,'total'=>14,'avg_price'=>290000],
            ['area'=>'Famagusta','resale'=>12,'brand_new'=>5,'total'=>17,'avg_price'=>360000],
        ];

        return view('report.partials.units', compact(
            'reportYears',
            'selectedYear',
            'filters',
            'statusOptions',
            'regions',
            'townsByRegion',
            'propertyTypes',
            'kpis',
            'chartByType',
            'chartByRegion',
            'chartByTown',
            'typeSummary',
            'areaSummary'
        ));
    }

    public function clients(Request $request)
    {
        $startYear = 2022;
        $endYear   = now()->year;

        $reportYears  = range($startYear, $endYear);
        $selectedYear = (int) ($request->input('year') ?: $endYear);

        // Filters
        $filters = [
            'type'   => $request->input('type', 'all'),   // all|Buyer|Seller|Investor|Developer|Renter
            'status' => $request->input('status', 'all'), // all|Active|Negotiation|Converted|Inactive
            'region' => $request->input('region', 'all'),
            'town'   => $request->input('town', 'all'),
            'source' => $request->input('source', 'all'),
        ];

        // Cyprus Regions + Towns (dummy)
        $regions = ['Paphos', 'Limassol', 'Larnaca', 'Nicosia', 'Famagusta'];

        $townsByRegion = [
            'Paphos' => [
                'Paphos', 'Kato Paphos', 'Chloraka', 'Tala', 'Peyia', 'Coral Bay',
                'Polis', 'Latchi', 'Kissonerga', 'Geroskipou',
            ],
            'Limassol' => [
                'Limassol', 'Germasogeia', 'Agios Tychonas', 'Ypsonas', 'Pyrgos',
                'Pareklisia', 'Episkopi',
            ],
            'Larnaca' => [
                'Larnaca', 'Oroklini', 'Dhekelia', 'Pervolia', 'Meneou', 'Aradippou',
            ],
            'Nicosia' => [
                'Nicosia', 'Strovolos', 'Lakatamia', 'Aglantzia', 'Engomi',
            ],
            'Famagusta' => [
                'Ayia Napa', 'Protaras', 'Paralimni', 'Deryneia', 'Sotira',
            ],
        ];

        // Filter dropdown options
        $clientTypes = ['Buyer', 'Seller', 'Investor', 'Developer', 'Renter'];
        $clientStatuses = ['Active', 'Negotiation', 'Converted', 'Inactive'];
        $clientSources = ['Website', 'Referral', 'Kyero', 'Idealista', 'Walk-in', 'Existing Client'];

        // -----------------------------------------
        // Dummy Charts (Cyprus-only, consistent)
        // -----------------------------------------
        // Client Types (doughnut)
        $chartClientTypes = [
            'labels' => ['Buyer', 'Seller', 'Investor', 'Developer', 'Renter'],
            'values' => [180, 120, 55, 32, 25],
        ];

        // Client Status (doughnut)
        $chartClientStatus = [
            'labels' => ['Active', 'Negotiation', 'Converted', 'Inactive'],
            'values' => [210, 85, 60, 57],
        ];

        // Clients by Region (stacked bar)
        $chartClientsByRegion = [
            'labels' => ['Paphos', 'Limassol', 'Larnaca', 'Nicosia', 'Famagusta'],
            'datasets' => [
                [
                    'label' => 'Buyers',
                    'data'  => [60, 55, 30, 22, 18],
                ],
                [
                    'label' => 'Sellers',
                    'data'  => [40, 35, 22, 18, 12],
                ],
                [
                    'label' => 'Investors',
                    'data'  => [15, 18, 10, 8, 6],
                ],
            ],
        ];

        // Client Sources (doughnut)
        $chartClientSources = [
            'labels' => ['Website', 'Referral', 'Kyero', 'Idealista', 'Walk-in', 'Existing Client'],
            'values' => [160, 90, 55, 45, 35, 27],
        ];

        // -----------------------------------------
        // KPIs (dummy, but tied to dummy charts)
        // -----------------------------------------
        $activeClients = (int) ($chartClientStatus['values'][0] ?? 0);

        // Buyers vs Sellers from type chart (index based)
        $buyers  = (int) ($chartClientTypes['values'][0] ?? 0);
        $sellers = (int) ($chartClientTypes['values'][1] ?? 0);

        // Repeat clients (dummy)
        $repeatClients = 68;

        // Clients with open deals (dummy)
        $openDealsClients = 94;

        $kpis = [
            'active_clients'     => $activeClients,
            'buyers'             => $buyers,
            'sellers'            => $sellers,
            'repeat_clients'     => $repeatClients,
            'open_deals_clients' => $openDealsClients,
        ];

        // -----------------------------------------
        // Table (dummy)
        // -----------------------------------------
        $clientsTable = [
            [
                'name' => 'Andreas Georgiou',
                'type' => 'Buyer',
                'region' => 'Paphos',
                'town' => 'Chloraka',
                'status' => 'Active',
                'deals' => 2,
                'source' => 'Website',
                'last_activity' => '3 days ago',
            ],
            [
                'name' => 'Maria Loizou',
                'type' => 'Seller',
                'region' => 'Limassol',
                'town' => 'Germasogeia',
                'status' => 'Negotiation',
                'deals' => 1,
                'source' => 'Referral',
                'last_activity' => 'Today',
            ],
            [
                'name' => 'Christos Demetriou',
                'type' => 'Investor',
                'region' => 'Larnaca',
                'town' => 'Oroklini',
                'status' => 'Active',
                'deals' => 3,
                'source' => 'Kyero',
                'last_activity' => 'Yesterday',
            ],
            [
                'name' => 'Eleni Papadopoulou',
                'type' => 'Buyer',
                'region' => 'Nicosia',
                'town' => 'Strovolos',
                'status' => 'Inactive',
                'deals' => 0,
                'source' => 'Walk-in',
                'last_activity' => '28 days ago',
            ],
            [
                'name' => 'Dimitris Kyriakou',
                'type' => 'Developer',
                'region' => 'Famagusta',
                'town' => 'Protaras',
                'status' => 'Converted',
                'deals' => 1,
                'source' => 'Existing Client',
                'last_activity' => '7 days ago',
            ],
        ];

        // NOTE: Later we will filter these by $filters (DB query). For now, keep simple.

        return view('report.partials.clients', compact(
            'reportYears',
            'selectedYear',
            'filters',
            'regions',
            'townsByRegion',
            'clientTypes',
            'clientStatuses',
            'clientSources',
            'kpis',
            'chartClientTypes',
            'chartClientStatus',
            'chartClientsByRegion',
            'chartClientSources',
            'clientsTable'
        ));
    }


    public function potentialBuyers(Request $request)
    {
        $startYear = 2022;
        $endYear   = now()->year;

        $reportYears  = range($startYear, $endYear);
        $selectedYear = (int) ($request->input('year') ?: $endYear);

        // -------------------------
        // Filters
        // -------------------------
        $filters = [
            'region' => $request->input('region', 'all'),
            'town'   => $request->input('town', 'all'),
            'type'   => $request->input('type', 'all'),
            'budget' => $request->input('budget', 'all'),
            'status' => $request->input('status', 'all'),
        ];

        // -------------------------
        // Cyprus Regions + Towns
        // -------------------------
        $regions = ['Paphos', 'Limassol', 'Larnaca', 'Nicosia', 'Famagusta'];

        $townsByRegion = [
            'Paphos' => [
                'Paphos','Kato Paphos','Chloraka','Tala','Peyia',
                'Coral Bay','Polis','Latchi','Kissonerga','Geroskipou',
            ],
            'Limassol' => [
                'Limassol','Germasogeia','Agios Tychonas','Ypsonas',
                'Pyrgos','Pareklisia','Episkopi',
            ],
            'Larnaca' => [
                'Larnaca','Oroklini','Dhekelia','Pervolia',
                'Meneou','Aradippou',
            ],
            'Nicosia' => [
                'Nicosia','Strovolos','Lakatamia','Aglantzia','Engomi',
            ],
            'Famagusta' => [
                'Ayia Napa','Protaras','Paralimni','Deryneia','Sotira',
            ],
        ];

        $propertyTypes  = ['Apartment','Villa','Penthouse','Town House','Plot'];
        $buyerStatuses  = ['Active','Warm','Cold','Viewing Scheduled'];

        // -------------------------
        // KPIs (dummy but logical)
        // -------------------------
        $kpis = [
            'active_buyers' => 138,
            'with_budget'   => 112,
            'with_matches'  => 74,
            'high_intent'   => 46,
        ];

        // -------------------------
        // Charts
        // -------------------------

        // Buyer readiness funnel
        $chartBuyerFunnel = [
            'labels' => [
                'Buyer Created',
                'Budget Set',
                'Preferences Set',
                'Properties Matched',
                'Viewing Scheduled',
            ],
            'values' => [180, 140, 120, 74, 46],
        ];

        // Budget distribution
        $chartBuyerBudget = [
            'labels' => [
                'Up to €250k',
                '€250k – €400k',
                '€400k – €600k',
                '€600k – €1M',
                '€1M+',
            ],
            'values' => [28, 46, 38, 18, 8],
        ];

        // -------------------------
        // Buyers Table (core feature)
        // -------------------------
        $buyersTable = [
            [
                'name' => 'Andreas Georgiou',
                'budget_min' => 400000,
                'budget_max' => 550000,
                'region' => 'Paphos',
                'type' => 'Villa',
                'matches' => 3,
                'status' => 'Active',
                'suggested_url' => '/properties?region=Paphos&type=Villa&min=400000&max=550000',
            ],
            [
                'name' => 'Maria Loizou',
                'budget_min' => 250000,
                'budget_max' => 380000,
                'region' => 'Limassol',
                'type' => 'Apartment',
                'matches' => 5,
                'status' => 'Viewing Scheduled',
                'suggested_url' => '/properties?region=Limassol&type=Apartment&min=250000&max=380000',
            ],
            [
                'name' => 'Christos Demetriou',
                'budget_min' => 600000,
                'budget_max' => 850000,
                'region' => 'Larnaca',
                'type' => 'Villa',
                'matches' => 2,
                'status' => 'Warm',
                'suggested_url' => '/properties?region=Larnaca&type=Villa&min=600000&max=850000',
            ],
            [
                'name' => 'Eleni Papadopoulou',
                'budget_min' => 180000,
                'budget_max' => 240000,
                'region' => 'Nicosia',
                'type' => 'Apartment',
                'matches' => 1,
                'status' => 'Cold',
                'suggested_url' => '/properties?region=Nicosia&type=Apartment&min=180000&max=240000',
            ],
            [
                'name' => 'Dimitris Kyriakou',
                'budget_min' => 950000,
                'budget_max' => 1300000,
                'region' => 'Famagusta',
                'type' => 'Villa',
                'matches' => 4,
                'status' => 'Active',
                'suggested_url' => '/properties?region=Famagusta&type=Villa&min=950000&max=1300000',
            ],
        ];

        // -------------------------
        // Return view
        // -------------------------
        return view('report.partials.potential-buyers', compact(
            'reportYears',
            'selectedYear',
            'filters',
            'regions',
            'townsByRegion',
            'propertyTypes',
            'buyerStatuses',
            'kpis',
            'chartBuyerFunnel',
            'chartBuyerBudget',
            'buyersTable'
        ));
    }

    public function propertyInterest(Request $request)
    {
        $startYear = 2022;
        $endYear = now()->year;

        $reportYears = range($startYear, $endYear);
        $selectedYear = (int) ($request->input('year') ?: $endYear);

        // KPIs
        $kpis = [
            'most_viewed' => 'Villa A5 (142 views)',
            'most_saved'  => 'Oceanview Apartment B3',
            'inquiries'   => 76,
            'viewings'    => 22,
        ];

        // Funnel
        $chartFunnel = [
            'labels' => ['Viewed', 'Saved', 'Inquiry', 'Viewing', 'Offer'],
            'values' => [520, 310, 148, 76, 22],
        ];

        // Charts
        $chartByRegion = [
            'labels' => ['Paphos', 'Limassol', 'Larnaca', 'Nicosia', 'Famagusta'],
            'values' => [180, 140, 90, 60, 50],
        ];

        $chartByType = [
            'labels' => ['Apartment', 'Villa', 'Town House', 'Plot'],
            'values' => [210, 180, 90, 40],
        ];

        $chartByPrice = [
            'labels' => ['≤ €250k', '€250k–€400k', '€400k–€600k', '€600k–€1M', '€1M+'],
            'values' => [88, 140, 120, 70, 42],
        ];

        // Hot Properties
        $hotProperties = [
            [
                'name' => 'Villa A5',
                'type' => 'Villa',
                'region' => 'Paphos',
                'views' => 142,
                'saves' => 38,
                'inquiries' => 12,
                'viewings' => 6,
                'score' => 92,
            ],
            [
                'name' => 'Oceanview Apartment B3',
                'type' => 'Apartment',
                'region' => 'Limassol',
                'views' => 120,
                'saves' => 44,
                'inquiries' => 15,
                'viewings' => 9,
                'score' => 96,
            ],
        ];

        return view('report.partials.property-interest', compact(
            'reportYears',
            'selectedYear',
            'kpis',
            'chartFunnel',
            'chartByRegion',
            'chartByType',
            'chartByPrice',
            'hotProperties'
        ));
    }

    public function developers(Request $request)
    {
        $startYear = 2022;
        $endYear   = now()->year;

        $reportYears  = range($startYear, $endYear);
        $selectedYear = (int) ($request->input('year') ?: $endYear);

        // Dummy but Cyprus-only
        $developersTable = [
            [
                'name' => 'BlueBay Developments',
                'projects' => 6,
                'active_listings' => 18,
                'leads' => 42,
                'avg_price' => 420000,
                'regions' => ['Paphos', 'Limassol'],
                'status' => 'Active',
                'url' => '/properties?developer=BlueBay%20Developments',
            ],
            [
                'name' => 'Sunrise Coastal Homes',
                'projects' => 4,
                'active_listings' => 9,
                'leads' => 21,
                'avg_price' => 310000,
                'regions' => ['Larnaca'],
                'status' => 'Low Stock',
                'url' => '/properties?developer=Sunrise%20Coastal%20Homes',
            ],
            [
                'name' => 'Aegean Urban Living',
                'projects' => 8,
                'active_listings' => 22,
                'leads' => 55,
                'avg_price' => 520000,
                'regions' => ['Limassol', 'Nicosia'],
                'status' => 'Active',
                'url' => '/properties?developer=Aegean%20Urban%20Living',
            ],
            [
                'name' => 'Mediterranean Plots Group',
                'projects' => 3,
                'active_listings' => 5,
                'leads' => 6,
                'avg_price' => 260000,
                'regions' => ['Famagusta'],
                'status' => 'Stale',
                'url' => '/properties?developer=Mediterranean%20Plots%20Group',
            ],
        ];

        // KPIs
        $activeDevelopers = collect($developersTable)->where('active_listings', '>', 0)->count();
        $activeListings   = collect($developersTable)->sum('active_listings');

        $topByLeads = collect($developersTable)->sortByDesc('leads')->first();
        $topByRevenue = collect($developersTable)->map(function ($d) {
            $d['revenue'] = $d['active_listings'] * $d['avg_price'] * 0.18; // dummy "sell-through"
            return $d;
        })->sortByDesc('revenue')->first();

        $kpis = [
            'active_developers' => $activeDevelopers,
            'active_listings' => $activeListings,
            'top_by_leads' => $topByLeads['name'] ?? '—',
            'top_by_leads_count' => $topByLeads['leads'] ?? 0,
            'top_by_revenue' => $topByRevenue['name'] ?? '—',
            'top_by_revenue_amount' => (int) ($topByRevenue['revenue'] ?? 0),
        ];

        // Charts
        $chartListingsByDeveloper = [
            'labels' => collect($developersTable)->pluck('name')->values(),
            'values' => collect($developersTable)->pluck('active_listings')->values(),
        ];

        $chartLeadsByDeveloper = [
            'labels' => collect($developersTable)->pluck('name')->values(),
            'values' => collect($developersTable)->pluck('leads')->values(),
        ];

        return view('report.partials.developers', compact(
            'reportYears',
            'selectedYear',
            'kpis',
            'developersTable',
            'chartListingsByDeveloper',
            'chartLeadsByDeveloper'
        ));
    }

    public function agents(Request $request)
    {
        $startYear = 2022;
        $endYear   = now()->year;

        $reportYears  = range($startYear, $endYear);
        $selectedYear = (int) ($request->input('year') ?: $endYear);

        // Dummy table data (replace with DB later)
        $agentsTable = [
            [
                'name' => 'Harrold Martinez',
                'leads' => 42,
                'deals' => 9,
                'revenue' => 185000,
                'status' => 'Active',
                'url' => '/report?agent=harrold',
            ],
            [
                'name' => 'Cheryl Hann',
                'leads' => 35,
                'deals' => 7,
                'revenue' => 142000,
                'status' => 'Active',
                'url' => '/report?agent=cheryl',
            ],
            [
                'name' => 'Iryna Siryk',
                'leads' => 26,
                'deals' => 4,
                'revenue' => 98000,
                'status' => 'Low Activity',
                'url' => '/report?agent=iryna',
            ],
            [
                'name' => 'Paul Hann',
                'leads' => 18,
                'deals' => 3,
                'revenue' => 76000,
                'status' => 'Low Activity',
                'url' => '/report?agent=paul',
            ],
        ];

        // Enrich computed fields
        $agentsTable = collect($agentsTable)->map(function ($a) {
            $leads = (int) $a['leads'];
            $deals = (int) $a['deals'];

            $conversion = $leads > 0 ? round(($deals / $leads) * 100, 1) : 0;
            $avgDeal = $deals > 0 ? (int) round($a['revenue'] / $deals) : 0;

            $a['conversion'] = $conversion;
            $a['avg_deal'] = $avgDeal;

            return $a;
        })->values()->all();

        // KPIs
        $activeAgents = collect($agentsTable)->where('status', 'Active')->count();

        $topCloser = collect($agentsTable)->sortByDesc('deals')->first();
        $topLeads  = collect($agentsTable)->sortByDesc('leads')->first();

        $revenueYtd = (int) collect($agentsTable)->sum('revenue');

        $kpis = [
            'active_agents' => $activeAgents,
            'top_closer' => $topCloser['name'] ?? '—',
            'top_closer_deals' => $topCloser['deals'] ?? 0,
            'top_leads' => $topLeads['name'] ?? '—',
            'top_leads_count' => $topLeads['leads'] ?? 0,
            'revenue_ytd' => $revenueYtd,
        ];

        // Charts
        $chartDealsByAgent = [
            'labels' => collect($agentsTable)->pluck('name')->values(),
            'values' => collect($agentsTable)->pluck('deals')->values(),
        ];

        $chartLeadsByAgent = [
            'labels' => collect($agentsTable)->pluck('name')->values(),
            'values' => collect($agentsTable)->pluck('leads')->values(),
        ];

        return view('report.partials.agents', compact(
            'reportYears',
            'selectedYear',
            'kpis',
            'agentsTable',
            'chartDealsByAgent',
            'chartLeadsByAgent'
        ));
    }


    public function deals(Request $request)
    {
        $startYear = 2022;
        $endYear   = now()->year;

        $reportYears  = range($startYear, $endYear);
        $selectedYear = (int) ($request->input('year') ?: $endYear);

        // Dummy pipeline deals (later: deals table + stages + timestamps)
        $dealsTable = [
            [
                'deal' => 'Villa Viewing – Chloraka',
                'client' => 'Maria Loizou',
                'agent' => 'Cheryl Hann',
                'stage' => 'Viewing Scheduled',
                'value' => 380000,
                'created_at' => '2025-12-01',
                'age_days' => 15,
                'next_action' => 'Confirm viewing time',
                'status' => 'Active',
            ],
            [
                'deal' => 'Apartment Offer – Paphos',
                'client' => 'Andreas Georgiou',
                'agent' => 'Harrold Martinez',
                'stage' => 'Offer Made',
                'value' => 520000,
                'created_at' => '2025-11-20',
                'age_days' => 26,
                'next_action' => 'Follow up on offer',
                'status' => 'Stale',
            ],
            [
                'deal' => 'Penthouse Negotiation – Limassol',
                'client' => 'Dimitris Kyriakou',
                'agent' => 'Paul Hann',
                'stage' => 'Negotiation',
                'value' => 950000,
                'created_at' => '2025-11-10',
                'age_days' => 36,
                'next_action' => 'Send counter proposal',
                'status' => 'At Risk',
            ],
            [
                'deal' => 'Plot Inquiry – Larnaca',
                'client' => 'Christos Demetriou',
                'agent' => 'Iryna Siryk',
                'stage' => 'Qualified',
                'value' => 610000,
                'created_at' => '2025-12-05',
                'age_days' => 11,
                'next_action' => 'Share 3 matching plots',
                'status' => 'Active',
            ],
            [
                'deal' => 'Townhouse Docs – Nicosia',
                'client' => 'Eleni Papadopoulou',
                'agent' => 'Harrold Martinez',
                'stage' => 'Document Collection',
                'value' => 240000,
                'created_at' => '2025-11-28',
                'age_days' => 18,
                'next_action' => 'Request missing ID copy',
                'status' => 'Active',
            ],
        ];

        // KPIs
        $activeDeals = collect($dealsTable)->count();
        $pipelineValue = (int) collect($dealsTable)->sum('value');
        $avgAge = (int) round(collect($dealsTable)->avg('age_days'));

        // dummy closed this month
        $closedThisMonth = 6;

        $kpis = [
            'active_deals' => $activeDeals,
            'closed_this_month' => $closedThisMonth,
            'pipeline_value' => $pipelineValue,
            'avg_age_days' => $avgAge,
        ];

        // Monthly goal progress (dummy)
        $goal = 12;
        $percent = $goal > 0 ? (int) round(($closedThisMonth / $goal) * 100) : 0;

        $pipelineProgress = [
            'goal' => $goal,
            'percent' => min($percent, 100),
            'label' => "{$closedThisMonth} / {$goal} closed",
        ];

        // Charts
        $stageCounts = collect($dealsTable)->groupBy('stage')->map->count();
        $chartStages = [
            'labels' => $stageCounts->keys()->values(),
            'values' => $stageCounts->values()->values(),
        ];

        $sortedByAge = collect($dealsTable)->sortByDesc('age_days')->values();
        $chartAging = [
            'labels' => $sortedByAge->pluck('deal')->values(),
            'values' => $sortedByAge->pluck('age_days')->values(),
        ];

        return view('report.partials.deals', compact(
            'reportYears',
            'selectedYear',
            'kpis',
            'pipelineProgress',
            'chartStages',
            'chartAging',
            'dealsTable'
        ));
    }

    public function diary(Request $request)
    {
        // month format: YYYY-MM (e.g. 2025-05)
        $selectedMonth = $request->input('month', now()->format('Y-m'));

        // Build month dropdown (last 12 months)
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $dt = now()->copy()->subMonths($i);
            $months[] = [
                'value' => $dt->format('Y-m'),
                'label' => $dt->format('F Y'),
            ];
        }

        $selectedMonthLabel = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('F Y');

        // Dummy activities (later: query diary/events table)
        // NOTE: These are Cyprus-themed names/places to match your other reports.
        $activities = [
            ['date'=> $selectedMonth.'-03','time'=>'10:00','type'=>'Viewing','client'=>'Maria Loizou','property'=>'Apartment – Paphos','agent'=>'Cheryl','status'=>'Confirmed'],
            ['date'=> $selectedMonth.'-03','time'=>'15:00','type'=>'Take On','client'=>'Andreas Georgiou','property'=>'Villa – Chloraka','agent'=>'Harrold','status'=>'Pending'],
            ['date'=> $selectedMonth.'-07','time'=>'11:30','type'=>'Misc','client'=>'Christos Demetriou','property'=>'Plot – Larnaca','agent'=>'Iryna','status'=>'Done'],
            ['date'=> $selectedMonth.'-12','time'=>'09:00','type'=>'Viewing','client'=>'Eleni Papadopoulou','property'=>'Town House – Nicosia','agent'=>'Paul','status'=>'Confirmed'],
            ['date'=> $selectedMonth.'-18','time'=>'14:00','type'=>'Viewing','client'=>'Dimitris Kyriakou','property'=>'Villa – Protaras','agent'=>'Harrold','status'=>'Pending'],
            ['date'=> $selectedMonth.'-22','time'=>'16:00','type'=>'Misc','client'=>'Sofia Ioannou','property'=>'Penthouse – Limassol','agent'=>'Cheryl','status'=>'Cancelled'],
        ];

        // KPIs (dummy)
        $kpis = [
            'today' => 3,
            'this_week' => 9,
            'upcoming_7' => 5,
            'overdue' => 2,
        ];

        // Calendar grid (simple month view)
        $monthStart = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
        $monthEnd   = $monthStart->copy()->endOfMonth();

        // Monday-start grid
        $gridStart = $monthStart->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
        $gridEnd   = $monthEnd->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);

        $countsByDate = collect($activities)->groupBy('date')->map->count();

        $calendarDays = [];
        $cursor = $gridStart->copy();
        while ($cursor->lte($gridEnd)) {
            $dateStr = $cursor->format('Y-m-d');
            $calendarDays[] = [
                'date' => $dateStr,
                'day' => (int) $cursor->format('d'),
                'short' => $cursor->format('M'),
                'inMonth' => $cursor->format('Y-m') === $selectedMonth,
                'isToday' => $cursor->isToday(),
                'count' => (int) ($countsByDate[$dateStr] ?? 0),
            ];
            $cursor->addDay();
        }

        return view('report.partials.diary', compact(
            'months',
            'selectedMonth',
            'selectedMonthLabel',
            'kpis',
            'calendarDays',
            'activities'
        ));
    }

    public function banks(Request $request)
    {
        $startYear = 2022;
        $endYear   = now()->year;

        $reportYears  = range($startYear, $endYear);
        $selectedYear = (int) ($request->input('year') ?: $endYear);

        // Filters (dummy for now, kept same pattern as other reports)
        $filters = [
            'bank'   => $request->input('bank', 'all'),
            'status' => $request->input('status', 'all'), // all|active|not_active
        ];

        $statusOptions = [
            'all' => 'All',
            'active' => 'Active',
            'not_active' => 'Not Active',
        ];

        // Dummy bank list (match your dropdown examples)
        $banks = ['Remu','Altamira','Altia','Gordian','Alpha Bank','Astro Bank'];

        // Dummy summary rows (table)
        $banksTable = [
            [
                'bank' => 'Remu',
                'total' => 35,
                'active' => 31,
                'website_live' => 18,
                'avg_price' => 412000,
                'min_price' => 98000,
                'max_price' => 2100000,
                'top_town' => 'Paphos',
                'top_type' => 'Apartment',
                'url' => '/properties?bank=Remu',
            ],
            [
                'bank' => 'Altamira',
                'total' => 22,
                'active' => 19,
                'website_live' => 10,
                'avg_price' => 365000,
                'min_price' => 85000,
                'max_price' => 1450000,
                'top_town' => 'Limassol',
                'top_type' => 'Villa',
                'url' => '/properties?bank=Altamira',
            ],
            [
                'bank' => 'Altia',
                'total' => 14,
                'active' => 12,
                'website_live' => 6,
                'avg_price' => 298000,
                'min_price' => 75000,
                'max_price' => 980000,
                'top_town' => 'Larnaca',
                'top_type' => 'Apartment',
                'url' => '/properties?bank=Altia',
            ],
            [
                'bank' => 'Gordian',
                'total' => 18,
                'active' => 16,
                'website_live' => 9,
                'avg_price' => 455000,
                'min_price' => 110000,
                'max_price' => 1750000,
                'top_town' => 'Nicosia',
                'top_type' => 'Plot',
                'url' => '/properties?bank=Gordian',
            ],
            [
                'bank' => 'Alpha Bank',
                'total' => 9,
                'active' => 7,
                'website_live' => 3,
                'avg_price' => 520000,
                'min_price' => 140000,
                'max_price' => 2200000,
                'top_town' => 'Famagusta',
                'top_type' => 'Villa',
                'url' => '/properties?bank=Alpha+Bank',
            ],
            [
                'bank' => 'Astro Bank',
                'total' => 6,
                'active' => 5,
                'website_live' => 2,
                'avg_price' => 260000,
                'min_price' => 95000,
                'max_price' => 650000,
                'top_town' => 'Paphos',
                'top_type' => 'Town House',
                'url' => '/properties?bank=Astro+Bank',
            ],
        ];

        // KPIs derived from table
        $totalProps = array_sum(array_column($banksTable, 'total'));
        $activeProps = array_sum(array_column($banksTable, 'active'));
        $liveProps = array_sum(array_column($banksTable, 'website_live'));

        $topBank = collect($banksTable)->sortByDesc('total')->first();

        $kpis = [
            'total' => $totalProps,
            'active' => $activeProps,
            'live' => $liveProps,
            'top_bank' => $topBank ? ($topBank['bank'].' ('.$topBank['total'].')') : '-',
        ];

        // Charts
        $chartByBank = [
            'labels' => array_column($banksTable, 'bank'),
            'values' => array_column($banksTable, 'total'),
        ];

        $chartAvgPrice = [
            'labels' => array_column($banksTable, 'bank'),
            'values' => array_column($banksTable, 'avg_price'),
        ];

        return view('report.partials.banks', compact(
            'reportYears',
            'selectedYear',
            'filters',
            'statusOptions',
            'banks',
            'kpis',
            'chartByBank',
            'chartAvgPrice',
            'banksTable'
        ));
    }

    public function vendors(Request $request)
    {
        $startYear = 2022;
        $endYear   = now()->year;

        $reportYears  = range($startYear, $endYear);
        $selectedYear = (int) ($request->input('year') ?: $endYear);

        $filters = [
            'vendor' => $request->input('vendor', 'all'),
            'status' => $request->input('status', 'all'), // all|active|not_active
        ];

        $statusOptions = [
            'all' => 'All',
            'active' => 'Active',
            'not_active' => 'Not Active',
        ];

        // Dummy vendors list (replace later from DB)
        $vendors = [
            'Vendor A', 'Vendor B', 'Vendor C', 'Vendor D', 'Vendor E', 'Vendor F',
        ];

        // Dummy table
        $vendorsTable = [
            [
                'vendor' => 'Vendor A',
                'total' => 120,
                'active' => 110,
                'website_live' => 64,
                'live_rate' => 58,
                'avg_price' => 415000,
                'top_type' => 'Apartment',
                'top_region' => 'Paphos',
                'url' => '/properties?vendor=Vendor+A',
            ],
            [
                'vendor' => 'Vendor B',
                'total' => 88,
                'active' => 80,
                'website_live' => 40,
                'live_rate' => 50,
                'avg_price' => 520000,
                'top_type' => 'Villa',
                'top_region' => 'Limassol',
                'url' => '/properties?vendor=Vendor+B',
            ],
            [
                'vendor' => 'Vendor C',
                'total' => 55,
                'active' => 49,
                'website_live' => 21,
                'live_rate' => 43,
                'avg_price' => 310000,
                'top_type' => 'Town House',
                'top_region' => 'Larnaca',
                'url' => '/properties?vendor=Vendor+C',
            ],
            [
                'vendor' => 'Vendor D',
                'total' => 42,
                'active' => 39,
                'website_live' => 25,
                'live_rate' => 64,
                'avg_price' => 680000,
                'top_type' => 'Penthouse',
                'top_region' => 'Nicosia',
                'url' => '/properties?vendor=Vendor+D',
            ],
            [
                'vendor' => 'Vendor E',
                'total' => 33,
                'active' => 28,
                'website_live' => 9,
                'live_rate' => 32,
                'avg_price' => 260000,
                'top_type' => 'Plot',
                'top_region' => 'Famagusta',
                'url' => '/properties?vendor=Vendor+E',
            ],
        ];

        $vendorsTotal = count($vendorsTable);
        $vendorsActive = collect($vendorsTable)->filter(fn($r) => ($r['website_live'] ?? 0) > 0)->count();
        $propsTotal = array_sum(array_column($vendorsTable, 'total'));
        $avgPerVendor = $vendorsTotal ? round($propsTotal / $vendorsTotal) : 0;

        $kpis = [
            'vendors_total' => $vendorsTotal,
            'vendors_active' => $vendorsActive,
            'properties_total' => $propsTotal,
            'avg_per_vendor' => $avgPerVendor,
        ];

        // Charts
        $chartTopVendors = [
            'labels' => array_column($vendorsTable, 'vendor'),
            'values' => array_column($vendorsTable, 'total'),
        ];

        $chartVendorShare = $chartTopVendors; // same data, doughnut representation

        return view('report.partials.vendors', compact(
            'reportYears',
            'selectedYear',
            'filters',
            'statusOptions',
            'vendors',
            'kpis',
            'chartTopVendors',
            'chartVendorShare',
            'vendorsTable'
        ));
    }

    public function inbox(Request $request)
    {
        $categories = [
            'property-details' => 'Property Details',
            'investor-club'    => 'Investor Club',
            'sell-with-us'     => 'Sell With Us',
            'contact-us'       => 'Contact Us',
            'affiliate-page'   => 'Affiliate Page',
            'callback'         => 'Request a Callback',
            'subscribe'        => 'Subscribe',
        ];

        $selectedCategory = $request->query('category', 'property-details');
        if (!array_key_exists($selectedCategory, $categories)) {
            $selectedCategory = 'property-details';
        }

        // Dummy inbox rows (later: pull from DB)
        $all = [
            'property-details' => [
                ['reference' => 'HOR-1179', 'name' => 'Harrold Van Martinez', 'email' => 'harroldvanmartinez@gmail.com', 'submitted' => '2025-09-01 07:41'],
                ['reference' => 'IS32',     'name' => 'Harrold Van Martinez', 'email' => 'harroldvanmartinez@gmail.com', 'submitted' => '2025-08-29 06:19'],
            ],
            'investor-club' => [
                ['reference' => 'IC-0221',  'name' => 'Maria Loizou', 'email' => 'maria@example.com', 'submitted' => '2025-09-02 12:10'],
            ],
            'sell-with-us' => [],
            'contact-us' => [
                ['reference' => 'CU-1091',  'name' => 'Andreas Georgiou', 'email' => 'andreas@example.com', 'submitted' => '2025-09-03 09:30'],
            ],
            'affiliate-page' => [],
            'callback' => [
                ['reference' => 'CB-7731',  'name' => 'Eleni Papadopoulou', 'email' => 'eleni@example.com', 'submitted' => '2025-09-04 15:05'],
            ],
            'subscribe' => [
                ['reference' => 'SUB-2001', 'name' => 'Newsletter Signup', 'email' => 'lead@example.com', 'submitted' => '2025-09-05 18:22'],
            ],
        ];

        $filtered = $all[$selectedCategory] ?? [];

        $kpis = [
            'total'    => collect($all)->flatten(1)->count(),
            'category' => count($filtered),
            'last_7'   => 3, // dummy (later: compute from dates)
            'unread'   => 1, // dummy (later: compute from DB unread flag)
        ];

        return view('report.partials.inbox', compact(
            'categories',
            'selectedCategory',
            'filtered',
            'kpis'
        ));
    }

    public function users(Request $request)
    {
        $startYear = 2022;
        $endYear   = now()->year;

        $reportYears  = range($startYear, $endYear);
        $selectedYear = (int) ($request->input('year') ?: $endYear);

        // Filters
        $filters = [
            'user'   => $request->input('user', 'all'),
            'module' => $request->input('module', 'all'),
            'action' => $request->input('action', 'all'),
            'from'   => $request->input('from', ''),
            'to'     => $request->input('to', ''),
        ];

        // Dropdown options
        $modules = [
            'all'      => 'All Modules',
            'listings' => 'Listings',
            'deals'    => 'Deals',
            'clients'  => 'Clients',
            'inbox'    => 'Inbox',
            'auth'     => 'Auth',
        ];

        $actions = [
            'all'    => 'All Actions',
            'create' => 'Create',
            'update' => 'Update',
            'delete' => 'Delete',
            'share'  => 'Share',
            'login'  => 'Login',
        ];

        // Users list (safe fallback)
        // If you have App\Models\User, you can use it.
        $users = [];
        try {
            $dbUsers = \App\Models\User::select('id', 'name')->orderBy('name')->get();
            $users = $dbUsers->map(fn($u) => ['id' => $u->id, 'name' => $u->name])->toArray();
        } catch (\Throwable $e) {
            $users = [
                ['id' => 1, 'name' => 'Harrold Van Martinez'],
                ['id' => 2, 'name' => 'Vita Phillips'],
                ['id' => 3, 'name' => 'Paul Hann'],
            ];
        }

        // Dummy activity rows (replace later with real audit table query)
        $allRows = [
            [
                'created_at' => $selectedYear . '-09-01 10:21',
                'user_id' => 1,
                'user_name' => 'Harrold Van Martinez',
                'module' => 'listings',
                'action' => 'create',
                'message' => 'Created listing and uploaded photos',
                'target' => 'REF: HOR-1179',
            ],
            [
                'created_at' => $selectedYear . '-09-01 10:55',
                'user_id' => 1,
                'user_name' => 'Harrold Van Martinez',
                'module' => 'listings',
                'action' => 'share',
                'message' => 'Shared listing link to WhatsApp',
                'target' => 'REF: HOR-1179',
            ],
            [
                'created_at' => $selectedYear . '-08-29 09:12',
                'user_id' => 15,
                'user_name' => 'Paul Hann',
                'module' => 'deals',
                'action' => 'update',
                'message' => 'Updated deal stage to Negotiation',
                'target' => 'Deal #2041',
            ],
            [
                'created_at' => $selectedYear . '-08-28 14:06',
                'user_id' => 14,
                'user_name' => 'Vita Phillips',
                'module' => 'clients',
                'action' => 'create',
                'message' => 'Added new buyer profile with budget',
                'target' => 'Client: Andreas G.',
            ],
            [
                'created_at' => $selectedYear . '-08-28 15:44',
                'user_id' => 14,
                'user_name' => 'Vita Phillips',
                'module' => 'listings',
                'action' => 'delete',
                'message' => 'Deleted duplicate listing entry',
                'target' => 'REF: TMP-9911',
            ],
            [
                'created_at' => $selectedYear . '-08-27 08:01',
                'user_id' => 1,
                'user_name' => 'Harrold Van Martinez',
                'module' => 'auth',
                'action' => 'login',
                'message' => 'User logged in',
                'target' => '',
            ],
        ];

        // Apply filters to dummy rows
        $rows = array_values(array_filter($allRows, function ($r) use ($filters) {
            if ($filters['user'] !== 'all' && (string)$r['user_id'] !== (string)$filters['user']) return false;
            if ($filters['module'] !== 'all' && $r['module'] !== $filters['module']) return false;
            if ($filters['action'] !== 'all' && $r['action'] !== $filters['action']) return false;

            if (!empty($filters['from']) && strtotime($r['created_at']) < strtotime($filters['from'] . ' 00:00:00')) return false;
            if (!empty($filters['to']) && strtotime($r['created_at']) > strtotime($filters['to'] . ' 23:59:59')) return false;

            return true;
        }));

        // KPIs from filtered rows
        $kpis = [
            'total_actions'   => count($rows),
            'listing_added'   => count(array_filter($rows, fn($r) => $r['module'] === 'listings' && $r['action'] === 'create')),
            'listing_deleted' => count(array_filter($rows, fn($r) => $r['module'] === 'listings' && $r['action'] === 'delete')),
            'shares'          => count(array_filter($rows, fn($r) => $r['action'] === 'share')),
        ];

        return view('report.partials.users', compact(
            'reportYears',
            'selectedYear',
            'filters',
            'users',
            'modules',
            'actions',
            'kpis',
            'rows'
        ));
    }









}
