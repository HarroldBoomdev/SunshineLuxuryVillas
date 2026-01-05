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
                    <a href="#" class="text-gray-600 hover:text-blue-600 report-link" data-type="leads">Leads</a>
                </li>

                <li><a href="#" class="text-gray-600 hover:text-blue-600 report-link" data-type="sales">Sales</a></li>
                <li><a href="#" class="text-gray-600 hover:text-blue-600 report-link" data-type="listings">Listings</a></li>
                <li><a href="#" class="text-gray-600 hover:text-blue-600 report-link" data-type="units">Units</a></li>


            </ul>
            <li><a href="#" class="block text-gray-700 hover:text-blue-600 report-link" data-type="clients">Clients</a></li>
            <li><a href="#" class="block text-gray-700 hover:text-blue-600 report-link" data-type="potential-buyers">Potential buyers</a></li>
            <li><a href="#" class="block text-gray-700 hover:text-blue-600 report-link" data-type="property-interest">Property interest</a></li>
            <li><a href="#" class="block text-gray-700 hover:text-blue-600 report-link" data-type="developers">Developers</a></li>
            <li><a href="#" class="block text-gray-700 hover:text-blue-600 report-link" data-type="agents">Agents</a></li>
            <li><a href="#" class="block text-gray-700 hover:text-blue-600 report-link" data-type="deals">Deals</a></li>
            <li><a href="#" class="block text-gray-700 hover:text-blue-600 report-link" data-type="diary">Diary</a></li>
            <li><a href="#" class="block text-gray-700 hover:text-blue-600 report-link" data-type="banks">Banks</a></li>
            <li><a href="#" class="block text-gray-700 hover:text-blue-600 report-link" data-type="vendors">Vendors</a></li>
            <li><a href="#" class="block text-gray-700 hover:text-blue-600 report-link" data-type="inbox">Inbox</a></li>
            <li><a href="#" class="block text-gray-700 hover:text-blue-600 report-link" data-type="users">User</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6 bg-gray-50 min-h-screen">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800"></h1>
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
  // -------------------------------
  // Active link styling (no slugs)
  // -------------------------------
  const ACTIVE_CLASSES   = ['bg-blue-50','text-blue-700','font-semibold','border-l-4','border-blue-600'];
  const INACTIVE_CLASSES = ['text-gray-700','hover:text-blue-600'];

  function setActiveLink(linkEl){
    document.querySelectorAll('.report-link').forEach(a => {
      a.classList.remove(...ACTIVE_CLASSES);
      INACTIVE_CLASSES.forEach(c => { if (!a.classList.contains(c)) a.classList.add(c); });
      a.setAttribute('aria-current','false');
    });
    linkEl.classList.remove(...INACTIVE_CLASSES);
    linkEl.classList.add(...ACTIVE_CLASSES);
    linkEl.setAttribute('aria-current','true');
    sessionStorage.setItem('activeReportType', linkEl.dataset.type || '');
  }

  // -------------------------------
  // Report loader
  // -------------------------------
  let currentReportType = 'properties';

  const links     = document.querySelectorAll('.report-link');
  const container = document.getElementById('report-content');
  const csvBtn    = document.getElementById('downloadCsvBtn');
  const pdfBtn    = document.getElementById('downloadPdfBtn');

  function updateDownloadLinks() {
    csvBtn.href = `/reports/export/csv/${currentReportType}`;
    pdfBtn.href = `/reports/export/pdf/${currentReportType}`;
  }

  // Helper: wire year dropdown specifically for LEADS
  function initLeadsYearHandler() {
    const sel = document.getElementById('reportYear');
    if (!sel) return;

    sel.addEventListener('change', function () {
      const y = this.value;
      const container = document.getElementById('report-content');
      if (!container) return;

      // Update download links for this year, for LEADS report
      const csvBtn = document.getElementById('downloadCsvBtn');
      const pdfBtn = document.getElementById('downloadPdfBtn');
      if (csvBtn) csvBtn.href = `/reports/export/csv/leads?year=${encodeURIComponent(y)}`;
      if (pdfBtn) pdfBtn.href = `/reports/export/pdf/leads?year=${encodeURIComponent(y)}`;

      // Fetch the LEADS partial for the selected year
      const url = `/report/partials/leads?year=${encodeURIComponent(y)}`;
      console.log('Fetching leads for year →', y, url);

      fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin'
      })
      .then(async res => {
        const html = await res.text();
        if (!res.ok) throw new Error(`${res.status} ${res.statusText}`);

        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        container.replaceChildren(...wrapper.childNodes);

        // Re-run any scripts inside the newly loaded partial
        container.querySelectorAll('script').forEach(old => {
          const s = document.createElement('script');
          [...old.attributes].forEach(a => s.setAttribute(a.name, a.value));
          s.textContent = old.textContent;
          old.replaceWith(s);
        });

        // Re-bind handler for the new <select id="reportYear">
        initLeadsYearHandler();
      })
      .catch(err => {
        console.error(err);
        container.innerHTML = `<div class="text-red-600 p-4">
          Could not load leads data for year <strong>${y}</strong>.<br>
          <small>${err.message}</small>
        </div>`;
      });
    });
  }

  links.forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      const type = this.dataset.type;

      // visual active state
      setActiveLink(this);

      currentReportType = type;
      updateDownloadLinks();

      // load partial
      const url = `/report/partials/${type}`;
      console.log('Fetching →', url);

      fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin'
      })
      .then(async res => {
        const html = await res.text();
        if (!res.ok) throw new Error(`${res.status} ${res.statusText}`);

        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        container.replaceChildren(...wrapper.childNodes);

        // execute inline <script> tags
        container.querySelectorAll('script').forEach(old => {
          const s = document.createElement('script');
          [...old.attributes].forEach(a => s.setAttribute(a.name, a.value));
          s.textContent = old.textContent;
          old.replaceWith(s);
        });
        

        if (type === 'leads') {
          // Wire up the year dropdown whenever the Leads report is loaded
          initLeadsYearHandler();
        }
      })
      .catch(err => {
        container.innerHTML = `<div class="text-red-600 p-4">
          No report view available for "<strong>${type}</strong>". <small>${err.message}</small>
        </div>`;
      });
    });
  });

  // -------------------------------
  // Print helper (unchanged)
  // -------------------------------
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
  window.printSelectedReport = printSelectedReport; // expose to button

  // -------------------------------
  // Initialize
  // -------------------------------
  const saved = sessionStorage.getItem('activeReportType');
  const firstLink = document.querySelector('.report-link');
  const target = saved ? document.querySelector(`.report-link[data-type="${saved}"]`) : null;

  if (target) {
    target.click();
  } else {
    if (firstLink) setActiveLink(firstLink);
    updateDownloadLinks();
  }
</script>

@endpush
