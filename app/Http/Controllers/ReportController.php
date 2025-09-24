<?php

namespace App\Http\Controllers;

use App\Models\PropertiesModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    public function index()
    {
        $totalProperties = PropertiesModel::count();
        $totalAgents = User::role('Agent')->count();
        $monthlyRevenue = 200000; // Replace this with a real query if needed

        // Prepare performance chart data (last 4 months)
        $performanceData = PropertiesModel::select(
                DB::raw("COUNT(*) as property_count"),
                DB::raw("DATE_FORMAT(created_at, '%b') as month")
            )
            ->where('created_at', '>=', now()->subMonths(4))
            ->groupBy('month')
            ->orderByRaw("MIN(created_at)")
            ->get();

        $labels = $performanceData->pluck('month')->toArray();
        $propertyCounts = $performanceData->pluck('property_count')->toArray();

        // Simulate clients growth based on properties
        $clientCounts = array_map(fn($v) => $v - 20, $propertyCounts);

        return view('report.index', compact(
            'totalProperties',
            'totalAgents',
            'monthlyRevenue',
            'labels',
            'propertyCounts',
            'clientCounts'
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

    
}
