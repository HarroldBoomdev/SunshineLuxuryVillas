@extends('layouts.app')

@section('content')

@push('styles')
<style>
@media print {
    body * {
        visibility: hidden !important;
    }

    #report-content, #report-content * {
        visibility: visible !important;
    }

    #report-content {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
}
</style>
@endpush

<div class="flex h-full">
    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-gray-200 p-4">
        <h2 class="text-lg font-semibold mb-4">Reports</h2>
        <ul class="space-y-2 text-sm">
            <li><a href="#" class="block text-gray-700 hover:text-blue-600 report-link" data-type="properties">Properties</a></li>
            <ul class="pl-4 space-y-1">
                <li>
                    <a href="#"
                        class="text-gray-600 hover:text-blue-600 report-link"
                        data-type="historical">
                        Historical Listings
                    </a>
                </li>

                <li><a href="#" class="text-gray-600 hover:text-blue-600 report-link" data-type="notes">Notes</a></li>
                <li><a href="#" class="text-gray-600 hover:text-blue-600 report-link" data-type="total_value">Total Value</a></li>
                <li><a href="#" class="text-gray-600 hover:text-blue-600 report-link" data-type="valuations">Valuations</a></li>
                <li><a href="#" class="block text-gray-700 hover:text-blue-600 report-link" data-type="units">Units</a></li>
            </ul>
            <li><a href="#" class="block text-gray-700 hover:text-blue-600 report-link" data-type="clients">Clients</a></li>
            <li><a href="#" class="block text-gray-700 hover:text-blue-600 report-link" data-type="leads">Labels/Lead sources</a></li>
            <li><a href="#" class="block text-gray-700 hover:text-blue-600 report-link" data-type="buyers">Potential buyers</a></li>
            <li><a href="#" class="block text-gray-700 hover:text-blue-600 report-link" data-type="interest">Property interest</a></li>
            <li><a href="#" class="block text-gray-700 hover:text-blue-600 report-link" data-type="developers">Developers</a></li>
            <li><a href="#" class="block text-gray-700 hover:text-blue-600 report-link" data-type="agents">Agents</a></li>
            <li><a href="#" class="block text-gray-700 hover:text-blue-600 report-link" data-type="deals">Deals</a></li>
            <li><a href="#" class="block text-gray-700 hover:text-blue-600 report-link" data-type="open_deals">Open Deals Total Value</a></li>
            <li><a href="#" class="block text-gray-700 hover:text-blue-600 report-link" data-type="diary">Diary</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6 bg-gray-50 min-h-screen">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Reports</h1>
            <div class="space-x-2">
                <button onclick="printSelectedReport()" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Print Preview</button>
                <a id="downloadCsvBtn" href="/reports/export/csv/properties" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Download CSV</a>
                <a id="downloadPdfBtn" href="/reports/export/pdf/properties" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Download PDF</a>
            </div>
        </div>

        <!-- Dynamic Report Section -->
        <div id="report-content">
            <div class="text-center text-gray-500">Please select a report from the sidebar.</div>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let currentReportType = 'properties'; // default

    const links = document.querySelectorAll('.report-link');
    const container = document.getElementById('report-content');
    const csvBtn = document.getElementById('downloadCsvBtn');
    const pdfBtn = document.getElementById('downloadPdfBtn');

    function updateDownloadLinks() {
        csvBtn.href = `/reports/export/csv/${currentReportType}`;
        pdfBtn.href = `/reports/export/pdf/${currentReportType}`;
    }

    links.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const type = this.dataset.type;

            currentReportType = type;
            updateDownloadLinks();

            fetch(`/report/partials/${type}`)
                .then(res => {
                    if (!res.ok) throw new Error("View not found");
                    return res.text();
                })
                .then(html => {
                    container.innerHTML = html;

                    if (type === 'properties') {
                        const ctx = document.getElementById('chart-properties')?.getContext('2d');
                        if (ctx) {
                            new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: ['Jan', 'Feb', 'Mar', 'Apr'],
                                    datasets: [{
                                        label: 'Listings',
                                        data: [120, 150, 180, 210],
                                        backgroundColor: 'rgba(59, 130, 246, 0.7)'
                                    }]
                                },
                                options: {
                                    plugins: { legend: { display: false } },
                                    scales: {
                                        x: { ticks: { color: '#333' } },
                                        y: { ticks: { color: '#333' } }
                                    }
                                }
                            });
                        }
                    }
                })
                .catch(() => {
                    container.innerHTML = `<div class="text-red-600 p-4">No report view available for "<strong>${type}</strong>".</div>`;
                });
        });
    });

    function printSelectedReport() {
        const reportContent = document.getElementById('report-content');
        const charts = reportContent.querySelectorAll('canvas');

        const promises = Array.from(charts).map(canvas => {
            return new Promise(resolve => {
                const img = new Image();
                img.src = canvas.toDataURL("image/png");
                img.style.maxWidth = '100%';
                img.style.display = 'block';
                img.className = 'chart-image';
                canvas.parentNode.insertBefore(img, canvas);
                canvas.style.display = 'none';
                resolve();
            });
        });

        Promise.all(promises).then(() => {
            const originalContent = document.body.innerHTML;
            const printSection = reportContent.innerHTML;

            document.body.innerHTML = `
                <html>
                <head>
                    <title>Print Preview</title>
                    <style>
                        body { font-family: sans-serif; padding: 20px; }
                        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                        th, td { border: 1px solid #ccc; padding: 8px; }
                        .chart-image { margin-top: 20px; }
                    </style>
                </head>
                <body>${printSection}</body>
                </html>
            `;

            window.print();
            document.body.innerHTML = originalContent;
            location.reload();
        });
    }

    updateDownloadLinks();
</script>
@endpush
