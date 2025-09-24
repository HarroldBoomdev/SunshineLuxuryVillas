<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;
use App\Models\Lead;

class ReportExportController extends Controller
{
    public function export($type)
    {
        // Check if the view exists
        if (!view()->exists("reports.pdf.$type")) {
            abort(404, "PDF view for '$type' not found.");
        }

        $pdf = PDF::loadView("reports.pdf.$type", [
            // Pass data here if needed (dummy for now)
            'title' => ucfirst($type) . ' Report'
        ]);

        return $pdf->download("{$type}_report.pdf");
    }

    public function exportCsv($type)
    {
        $filename = "{$type}_report_" . now()->format('Ymd_His') . ".csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $rows = $this->getDataFor($type);

        return new StreamedResponse(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            if (!empty($rows)) {
                fputcsv($handle, array_keys($rows[0]));
                foreach ($rows as $row) {
                    fputcsv($handle, $row);
                }
            }
            fclose($handle);
        }, 200, $headers);
    }

    public function exportPdf($type)
    {
        $data = $this->getDataFor($type);

        if (!View::exists("reports.partials.$type")) {
            return abort(404, 'View not found.');
        }

        $pdf = Pdf::loadView("reports.partials.$type", compact('data'));
        return $pdf->download("{$type}_report_" . now()->format('Ymd_His') . ".pdf");
    }

    private function getDataFor($type)
    {
        // Replace this with real query logic per report type
            return match ($type) {
            'properties' => [
                ['Month' => 'Jan', 'Listings' => 120],
                ['Month' => 'Feb', 'Listings' => 150],
                ['Month' => 'Mar', 'Listings' => 180],
                ['Month' => 'Apr', 'Listings' => 210],
            ],
            'clients' => [
                ['Name' => 'John Doe', 'Email' => 'john@example.com'],
                ['Name' => 'Jane Smith', 'Email' => 'jane@example.com'],
            ],
            'listings' => [
                ['Region' => 'Limassol', 'Type' => 'Villa', 'Status' => 'Available'],
                ['Region' => 'Paphos', 'Type' => 'Apartment', 'Status' => 'Sold'],
                ['Region' => 'Larnaca', 'Type' => 'Townhouse', 'Status' => 'Available'],
            ],
            'leads' => Lead::all()->map(function ($lead) {
                return [
                    'Year'     => $lead->year,
                    'Month'    => $lead->month,
                    'Location' => $lead->location ?? 'N/A',
                    'Source'   => $lead->source ?? 'N/A',
                    'Count'    => $lead->count,
                ];
            })->toArray(),
            default => [],
        };
    }
}
