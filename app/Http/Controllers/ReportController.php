<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;


use App\Models\PropertiesModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    public function index()
    {
        // === Summary counts ===
        $totalProperties = PropertiesModel::count();
        $totalAgents = User::role('Agent')->count();
        $monthlyRevenue = 200000; // Replace this with a real query if needed

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

        $labels = $performanceData->pluck('month')->toArray();
        $propertyCounts = $performanceData->pluck('property_count')->toArray();

        // === Simulated client growth (for demo only) ===
        $clientCounts = array_map(fn($v) => max(0, $v - 20), $propertyCounts);

        // === Distinct years from leads table (for Leads dropdown) ===
        $reportYears = DB::table('leads')
            ->distinct()
            ->orderBy('year')
            ->pluck('year')
            ->toArray();

        if (empty($reportYears)) {
            $reportYears = [now()->year];
        }

        // === Return data to the main report view ===
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
            ['Revenue', 200000] // Replace with dynamic value if needed
        ];

        $filename = 'report_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
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
            'totalAgents' => User::role('Agent')->count(),
            'monthlyRevenue' => '$200,000'
        ];

        $pdf = Pdf::loadView('report.pdf', $data);
        return $pdf->download('report_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    public function leads(Request $request)
    {
        // Years in DB for dropdown
        $reportYears = DB::table('leads')
            ->distinct()
            ->orderBy('year')
            ->pluck('year')
            ->toArray();

        $defaultYear  = !empty($reportYears) ? max($reportYears) : now()->year;
        $year         = (int) ($request->input('year') ?: $defaultYear);
        $selectedYear = $year;

        // Months (fixed order)
        $MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];

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
        })->values()->all(); // plain array so Blade/JS can @json safely

        // KPIs for the selected year
        $salesThisYear = (int) DB::table('leads')->where('year', $year)->sum('count');

        $quarterRanges = [
            1 => ['January','February','March'],
            2 => ['April','May','June'],
            3 => ['July','August','September'],
            4 => ['October','November','December'],
        ];
        $currentQuarter = (int) ceil(now()->format('n') / 3);

        $salesThisQuarter = (int) DB::table('leads')
            ->where('year', $year)
            ->whereIn('month', $quarterRanges[$currentQuarter])
            ->sum('count');

        $quarterTarget = 12;
        $yearlyTarget  = 50;

        // Summary blocks you asked for (All, per location, per source) — all DB-driven
        $allLeadsTotal = $salesThisYear;
        $allLeadsAvg   = (int) ceil($allLeadsTotal / 12);

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

        $leadsBySource = DB::table('leads')
            ->selectRaw('COALESCE(source,"Unknown") as source, SUM(`count`) as total')
            ->where('year', $year)
            ->groupBy('source')
            ->pluck('total', 'source')
            ->toArray();

        // These are kept for compatibility with your existing JS (even if you don’t show those charts yet)
        $Q1=['January','February','March']; $Q2=['April','May','June'];
        $Q3=['July','August','September'];  $Q4=['October','November','December'];
        $H1 = array_merge($Q1,$Q2); $H2 = array_merge($Q3,$Q4);

        $sumBySource = function(array $months) use ($year){
            return DB::table('leads')
                ->selectRaw('COALESCE(source,"Unknown") as source, SUM(`count`) as total')
                ->where('year', $year)
                ->whereIn('month', $months)
                ->groupBy('source')
                ->pluck('total','source')
                ->toArray();
        };

        $sourcesH1 = $sumBySource($H1);
        $sourcesH2 = $sumBySource($H2);
        $sourcesQ1 = $sumBySource($Q1);
        $sourcesQ2 = $sumBySource($Q2);
        $sourcesQ3 = $sumBySource($Q3);
        $sourcesQ4 = $sumBySource($Q4);

        // Normalize arrays to integers (prevents JSON/Blade surprises)
        $toIntMap = fn($a) => collect($a)->map(fn($v)=>(int)$v)->all();
        $sourcesH1 = $toIntMap($sourcesH1);
        $sourcesH2 = $toIntMap($sourcesH2);
        $sourcesQ1 = $toIntMap($sourcesQ1);
        $sourcesQ2 = $toIntMap($sourcesQ2);
        $sourcesQ3 = $toIntMap($sourcesQ3);
        $sourcesQ4 = $toIntMap($sourcesQ4);

        return view('report.partials.leads', compact(
            'reportYears','selectedYear',
            'leadStats',
            'salesThisYear','salesThisQuarter','quarterTarget','yearlyTarget',
            'allLeadsTotal','allLeadsAvg','leadsByLocation','avgByLocation','leadsBySource',
            'sourcesH1','sourcesH2','sourcesQ1','sourcesQ2','sourcesQ3','sourcesQ4'
        ));
    }

}
