<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;

class HistoricalListingsController extends Controller
{
    public function index()
    {
        // Dummy table data
        $listings = collect([
            (object)[
                'reference'      => 'SLV-1001',
                'title'          => '3-Bedroom Villa with Pool',
                'location'       => 'Paphos, Cyprus',
                'agent_name'     => 'John Doe',
                'listed_at'      => '2024-01-15',
                'status'         => 'sold',
                'days_on_market' => 120,
                'final_value'    => 450000,
            ],
            (object)[
                'reference'      => 'SLV-1002',
                'title'          => 'Luxury Apartment',
                'location'       => 'Limassol, Cyprus',
                'agent_name'     => 'Jane Smith',
                'listed_at'      => '2024-03-20',
                'status'         => 'expired',
                'days_on_market' => 90,
                'final_value'    => 0,
            ],
            (object)[
                'reference'      => 'SLV-1003',
                'title'          => 'Beachfront Land Plot',
                'location'       => 'Larnaca, Cyprus',
                'agent_name'     => 'Paul White',
                'listed_at'      => '2023-11-05',
                'status'         => 'withdrawn',
                'days_on_market' => 60,
                'final_value'    => 0,
            ],
        ]);

        // Dummy stats
        $stats = [
            'total'     => 3,
            'sold'      => 1,
            'expired'   => 1,
            'withdrawn' => 1,
            'avg_days'  => 90,
        ];

        // Dummy chart data
        $yearlyCounts = collect([
            ['year' => 2023, 'count' => 12],
            ['year' => 2024, 'count' => 18],
        ]);

        $statusBreakdown = collect([
            ['status' => 'sold', 'count' => 1],
            ['status' => 'expired', 'count' => 1],
            ['status' => 'withdrawn', 'count' => 1],
        ]);

        $valueTrend = collect([
            ['label' => '2024-Q1', 'avg' => 350000],
            ['label' => '2024-Q2', 'avg' => 420000],
        ]);

        // Dummy filters
        $filters = [
            'agents'   => collect([(object)['id'=>1,'name'=>'John Doe'], (object)['id'=>2,'name'=>'Jane Smith']]),
            'regions'  => collect(['Paphos','Limassol','Larnaca']),
            'types'    => collect(['villa','apartment','land']),
            'statuses' => ['sold','expired','withdrawn'],
        ];

        return view('reports.historical_listings', compact(
            'listings','stats','filters','yearlyCounts','statusBreakdown','valueTrend'
        ));
    }

    public function csv()
    {
        $rows = [
            ['Ref','Title','Location','Agent','Listed','Status','Days','FinalValue'],
            ['SLV-1001','3-Bedroom Villa with Pool','Paphos, Cyprus','John Doe','2024-01-15','sold',120,450000],
            ['SLV-1002','Luxury Apartment','Limassol, Cyprus','Jane Smith','2024-03-20','expired',90,0],
            ['SLV-1003','Beachfront Land Plot','Larnaca, Cyprus','Paul White','2023-11-05','withdrawn',60,0],
        ];

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="historical_listings.csv"',
        ];

        $callback = function() use ($rows) {
            $out = fopen('php://output', 'w');
            foreach ($rows as $r) fputcsv($out, $r);
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function pdf()
    {
        // Dummy placeholder: needs barryvdh/laravel-dompdf or snappy
        abort(501, 'PDF export not implemented yet.');
    }
}
