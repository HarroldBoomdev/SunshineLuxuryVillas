{!! <<<BLADE
<style>
  /* keep charts tidy across envs */
  .chart-fixed { width:100%; max-height:420px; }
  @media (min-width:1280px){ .chart-fixed{ max-height:380px; } }

  /* pill buttons (shared style) */
  .qbtn {
    padding: 6px 14px;
    border-radius: 9999px;
    font-size: 0.85rem;
    font-weight: 600;
    border: 1.5px solid #d1d5db;
    background: #fff;
    color: #374151;
    transition: all 0.15s ease;
  }
  .qbtn:hover { background: #f3f4f6; }
  .qbtn.active {
    background: #2563eb;
    border-color: #2563eb;
    color: #fff;
    box-shadow: 0 2px 4px rgba(37,99,235,0.25);
  }
</style>

<div class="py-8">
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="p-6 bg-white rounded-lg shadow-md">

      <!-- Top KPIs -->
      <div class="grid grid-cols-4 gap-4 text-center mb-8">
        <div class="bg-green-500 text-white py-4 rounded-lg shadow-md">
          <h3 class="text-lg font-bold">Total Sales (This Year)</h3>
          <p class="text-2xl font-semibold">{{ $salesThisYear }}</p>
        </div>
        <div class="bg-yellow-500 text-white py-4 rounded-lg shadow-md">
          <h3 class="text-lg font-bold">Total Leads (2022–2024)</h3>
          <p class="text-2xl font-semibold">{{ $leadStats->sum('total') }}</p>
        </div>
        <div class="bg-blue-500 text-white py-4 rounded-lg shadow-md">
          <h3 class="text-lg font-bold">Quarter Target</h3>
          <p class="text-2xl font-semibold">{{ $salesThisQuarter }}/{{ $quarterTarget }}</p>
        </div>
        <div class="bg-pink-500 text-white py-4 rounded-lg shadow-md">
          <h3 class="text-lg font-bold">Year Target Progress</h3>
          <p class="text-2xl font-semibold">{{ round(($salesThisYear / $yearlyTarget) * 100, 1) }}%</p>
        </div>
      </div>

      <!-- Progress Bar -->
      <div class="mb-6">
        <h4 class="text-md font-semibold mb-1">Yearly Sales Target</h4>
        <div class="w-full bg-gray-300 rounded-full h-4">
          <div class="bg-green-600 h-4 rounded-full" style="width: {{ min(100, ($salesThisYear / $yearlyTarget) * 100) }}%"></div>
        </div>
      </div>

      <!-- LEADS by Month (interactive) -->
      <div class="p-4 bg-gray-100 rounded-lg shadow-md">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4 border-b pb-2">
          <h3 id="leadsTitle" class="text-xl font-semibold text-gray-800">
            Leads January – December
          </h3>
          <div id="leadsControls" class="flex flex-wrap gap-2">
            <button class="qbtn active" data-view="all">All</button>
            <button class="qbtn" data-view="q1">Q1</button>
            <button class="qbtn" data-view="q2">Q2</button>
            <button class="qbtn" data-view="q3">Q3</button>
            <button class="qbtn" data-view="q4">Q4</button>
            <button class="qbtn" data-view="compare">Compare</button>
          </div>
        </div>
        <!-- single bar (All/Q1/Q2/Q3/Q4) -->
        <canvas id="leadsBar" class="chart-fixed"></canvas>
        <!-- compare grid (hidden until Compare) -->
        <div id="leadsCompareGrid" class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4" style="display:none;">
          <div class="bg-white rounded-lg p-3"><h4 class="text-sm font-semibold mb-2">Q1 (Jan–Mar)</h4><canvas id="barQ1" class="chart-fixed"></canvas></div>
          <div class="bg-white rounded-lg p-3"><h4 class="text-sm font-semibold mb-2">Q2 (Apr–Jun)</h4><canvas id="barQ2" class="chart-fixed"></canvas></div>
          <div class="bg-white rounded-lg p-3"><h4 class="text-sm font-semibold mb-2">Q3 (Jul–Sep)</h4><canvas id="barQ3" class="chart-fixed"></canvas></div>
          <div class="bg-white rounded-lg p-3"><h4 class="text-sm font-semibold mb-2">Q4 (Oct–Dec)</h4><canvas id="barQ4" class="chart-fixed"></canvas></div>
        </div>
      </div>

      <!-- SALES by Source (Pie) – existing interactive section -->
      <div class="mt-4 p-4 bg-gray-100 rounded-lg shadow-md">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4 border-b pb-2">
          <h3 id="salesTitle" class="text-xl font-semibold text-gray-800">
            Sales January – December
          </h3>
          <div id="quarterControls" class="flex flex-wrap gap-2">
            <button class="qbtn active" data-view="all">All</button>
            <button class="qbtn" data-view="q1">Q1</button>
            <button class="qbtn" data-view="q2">Q2</button>
            <button class="qbtn" data-view="q3">Q3</button>
            <button class="qbtn" data-view="q4">Q4</button>
            <button class="qbtn" data-view="compare">Compare</button>
          </div>
        </div>
        <canvas id="yearPie" class="chart-fixed"></canvas>
        <div id="compareGrid" class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4" style="display:none;">
          <div class="bg-white rounded-lg p-3"><h4 class="text-sm font-semibold mb-2">Q1 (Jan–Mar)</h4><canvas id="pieQ1" class="chart-fixed"></canvas></div>
          <div class="bg-white rounded-lg p-3"><h4 class="text-sm font-semibold mb-2">Q2 (Apr–Jun)</h4><canvas id="pieQ2" class="chart-fixed"></canvas></div>
          <div class="bg-white rounded-lg p-3"><h4 class="text-sm font-semibold mb-2">Q3 (Jul–Sep)</h4><canvas id="pieQ3" class="chart-fixed"></canvas></div>
          <div class="bg-white rounded-lg p-3"><h4 class="text-sm font-semibold mb-2">Q4 (Oct–Dec)</h4><canvas id="pieQ4" class="chart-fixed"></canvas></div>
        </div>
      </div>

      <!-- SALES by Source Comparison (interactive like others) -->
      <div class="mt-4 p-4 bg-gray-100 rounded-lg shadow-md">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4 border-b pb-2">
          <h3 id="compareTitle" class="text-xl font-semibold text-gray-800">
            Sales by Source Comparison
          </h3>
          <div id="compareControls" class="flex flex-wrap gap-2">
            <button class="qbtn active" data-view="all">All</button>
            <button class="qbtn" data-view="q1">Q1</button>
            <button class="qbtn" data-view="q2">Q2</button>
            <button class="qbtn" data-view="q3">Q3</button>
            <button class="qbtn" data-view="q4">Q4</button>
            <button class="qbtn" data-view="compare">Compare</button>
          </div>
        </div>
        <canvas id="sourceCompareChart" class="chart-fixed"></canvas>
      </div>

    </div>
  </div>
</div>

@php
  /* ---------- SALES SOURCE DATA (unchanged) ---------- */
  $sourcesH1 = $sourcesH1 ?? ['Rightmove'=>10,'APITS'=>9,'Zoopla'=>3,'SLV'=>5,'HoS'=>2]; // Jan–Jun
  $sourcesH2 = $sourcesH2 ?? ['Rightmove'=>3,'APITS'=>5,'Zoopla'=>5,'SLV'=>4,'HoS'=>0];   // Jul–Dec

  // Optional precise quarters – pass these for exact numbers:
  // $sourcesQ1, $sourcesQ2, $sourcesQ3, $sourcesQ4
  $halfToQuarter = function($half) {
    $out = [];
    foreach ($half as $k=>$v) { $a = intdiv($v,2); $out[$k] = [$a, $v-$a]; }
    return $out;
  };
  $h1S = $halfToQuarter($sourcesH1);
  $h2S = $halfToQuarter($sourcesH2);

  $sourcesQ1 = $sourcesQ1 ?? array_combine(array_keys($h1S), array_column($h1S,0)); // Jan–Mar
  $sourcesQ2 = $sourcesQ2 ?? array_combine(array_keys($h1S), array_column($h1S,1)); // Apr–Jun
  $sourcesQ3 = $sourcesQ3 ?? array_combine(array_keys($h2S), array_column($h2S,0)); // Jul–Sep
  $sourcesQ4 = $sourcesQ4 ?? array_combine(array_keys($h2S), array_column($h2S,1)); // Oct–Dec
@endphp

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  /* =========================
   * LEADS (uses $leadStats)
   * ========================= */
  const leadStats  = @json($leadStats); // [{month:'January', total:n}, ...]
  const monthsFull = [...new Set(leadStats.map(i => i.month))];
  const leadsFull  = monthsFull.map(m => (leadStats.find(i => i.month === m)?.total) ?? 0);

  const idxByMonth = monthsFull.reduce((acc, m, i) => (acc[m]=i, acc), {});
  function sliceByMonths(monthNames){
    const labels = monthNames.filter(m => m in idxByMonth);
    const data   = labels.map(m => leadsFull[idxByMonth[m]]);
    return { labels, data };
  }
  const Q1M = ['January','February','March'];
  const Q2M = ['April','May','June'];
  const Q3M = ['July','August','September'];
  const Q4M = ['October','November','December'];

  const SL_ALL = { labels: monthsFull, data: leadsFull };
  const SL_Q1  = sliceByMonths(Q1M);
  const SL_Q2  = sliceByMonths(Q2M);
  const SL_Q3  = sliceByMonths(Q3M);
  const SL_Q4  = sliceByMonths(Q4M);

  let leadsBar;
  function makeBar(canvas, labels, data){
    return new Chart(canvas, {
      type: 'bar',
      data: { labels, datasets: [{ label: 'Leads', data, backgroundColor: '#34d399' }] },
      options: {
        responsive: true, maintainAspectRatio: true,
        scales: {
          y: { beginAtZero: true, ticks: { precision: 0 } },
          x: { grid: { display: false } }
        },
        plugins: { legend: { display: true, position: 'top' } }
      }
    });
  }
  function renderLeads(labels, data){
    const el = document.getElementById('leadsBar');
    if (leadsBar) leadsBar.destroy();
    leadsBar = makeBar(el, labels, data);
  }
  // default leads: Jan–Dec
  renderLeads(SL_ALL.labels, SL_ALL.data);

  const leadsCompareCache = {};
  function renderLeadsCompare(){
    document.getElementById('leadsBar').style.display = 'none';
    const grid = document.getElementById('leadsCompareGrid');
    grid.style.display = 'grid';
    [['barQ1',SL_Q1],['barQ2',SL_Q2],['barQ3',SL_Q3],['barQ4',SL_Q4]].forEach(([id,sl])=>{
      if (!leadsCompareCache[id]) leadsCompareCache[id] = makeBar(document.getElementById(id), sl.labels, sl.data);
    });
  }
  function leadsShowSingle(view){
    const title = document.getElementById('leadsTitle');
    const grid  = document.getElementById('leadsCompareGrid');
    grid.style.display = 'none';
    document.getElementById('leadsBar').style.display = 'block';
    if (view==='all'){ renderLeads(SL_ALL.labels, SL_ALL.data); title.textContent='Leads January – December'; }
    if (view==='q1'){  renderLeads(SL_Q1.labels,  SL_Q1.data);  title.textContent='Leads January – March'; }
    if (view==='q2'){  renderLeads(SL_Q2.labels,  SL_Q2.data);  title.textContent='Leads April – June'; }
    if (view==='q3'){  renderLeads(SL_Q3.labels,  SL_Q3.data);  title.textContent='Leads July – September'; }
    if (view==='q4'){  renderLeads(SL_Q4.labels,  SL_Q4.data);  title.textContent='Leads October – December'; }
  }
  const leadsControls = document.getElementById('leadsControls');
  leadsControls.addEventListener('click', (e)=>{
    const btn = e.target.closest('.qbtn'); if (!btn) return;
    leadsControls.querySelectorAll('.qbtn').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    const view = btn.dataset.view;
    if (view==='compare'){ document.getElementById('leadsTitle').textContent='Leads by Quarter'; renderLeadsCompare(); }
    else { leadsShowSingle(view); }
  });

  /* =========================
   * SALES (pie section)
   * ========================= */
  const H1 = @json($sourcesH1);
  const H2 = @json($sourcesH2);
  const Q1S = @json($sourcesQ1);
  const Q2S = @json($sourcesQ2);
  const Q3S = @json($sourcesQ3);
  const Q4S = @json($sourcesQ4);

  function sumDict(a,b){ const out={}; const keys=new Set([...Object.keys(a),...Object.keys(b)]); keys.forEach(k=>out[k]=(a[k]||0)+(b[k]||0)); return out; }
  const ALL = sumDict(H1,H2);

  const pieColors = ['#5AA6F8','#F6F062','#E86AF7','#66E08C','#F45A5A'];
  let yearPie; const pieCache = {};
  function makePie(canvas, srcObj){
    const labels = Object.keys(srcObj);
    const data   = Object.values(srcObj);
    const total  = data.reduce((a,b)=>a+b,0);
    return new Chart(canvas, {
      type: 'pie',
      data: { labels, datasets: [{ data, backgroundColor: pieColors, borderColor:'#fff', borderWidth:2 }] },
      options: { responsive:true, maintainAspectRatio:true,
        plugins:{ legend:{position:'bottom'}, tooltip:{callbacks:{label:(ctx)=>{const v=ctx.raw??0;const p=total?(v/total*100).toFixed(1):0;return `${ctx.label}: ${v} (${p}%)`;}}}}
      }
    });
  }
  function renderYearPie(srcObj){
    const el=document.getElementById('yearPie');
    if (yearPie) yearPie.destroy();
    yearPie = makePie(el, srcObj);
  }
  // default sales pie: Jan–Dec
  renderYearPie(ALL);

  function showSingle(view){
    const grid=document.getElementById('compareGrid');
    grid.style.display='none';
    const title=document.getElementById('salesTitle');
    document.getElementById('yearPie').style.display='block';
    if (view==='all'){ renderYearPie(ALL); title.textContent='Sales January – December'; }
    if (view==='q1'){  renderYearPie(Q1S); title.textContent='Sales January – March'; }
    if (view==='q2'){  renderYearPie(Q2S); title.textContent='Sales April – June'; }
    if (view==='q3'){  renderYearPie(Q3S); title.textContent='Sales July – September'; }
    if (view==='q4'){  renderYearPie(Q4S); title.textContent='Sales October – December'; }
  }
  function showCompare(){
    document.getElementById('yearPie').style.display='none';
    const grid=document.getElementById('compareGrid');
    grid.style.display='grid';
    document.getElementById('salesTitle').textContent='Sales by Quarter';
    [['pieQ1',Q1S],['pieQ2',Q2S],['pieQ3',Q3S],['pieQ4',Q4S]].forEach(([id,src])=>{
      if (!pieCache[id]) pieCache[id]=makePie(document.getElementById(id),src);
    });
  }
  const controls=document.getElementById('quarterControls');
  controls.addEventListener('click',(e)=>{
    const btn=e.target.closest('.qbtn'); if(!btn) return;
    controls.querySelectorAll('.qbtn').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    const view=btn.dataset.view;
    if(view==='compare') showCompare(); else showSingle(view);
  });

  /* ========= Interactive H1/H2/Quarter Comparison bar ========= */
  const SRC_Q1 = Q1S, SRC_Q2 = Q2S, SRC_Q3 = Q3S, SRC_Q4 = Q4S;
  const labelsSources = Object.keys(H1); // assumes all share the same keys

  const palette = [
    'rgba(99,179,237,0.9)',   // blue
    'rgba(244,114,182,0.9)',  // pink
    'rgba(251,191,36,0.9)',   // amber
    'rgba(110,231,183,0.9)'   // teal
  ];

  let compareChart;
  function renderCompareChart(mode){
    const ctx = document.getElementById('sourceCompareChart');
    if (compareChart) compareChart.destroy();

    let datasets, title = 'Sales by Source Comparison';
    if (mode === 'all') {
      // H1 vs H2
      datasets = [
        { label: 'Jan–Jun', data: labelsSources.map(k => H1[k] ?? 0), backgroundColor: palette[0] },
        { label: 'Jul–Dec', data: labelsSources.map(k => H2[k] ?? 0), backgroundColor: palette[1] },
      ];
      title += ' (Jan–Jun vs Jul–Dec)';
    } else if (mode === 'compare') {
      // Q1..Q4 together
      datasets = [
        { label: 'Q1', data: labelsSources.map(k => SRC_Q1[k] ?? 0), backgroundColor: palette[0] },
        { label: 'Q2', data: labelsSources.map(k => SRC_Q2[k] ?? 0), backgroundColor: palette[1] },
        { label: 'Q3', data: labelsSources.map(k => SRC_Q3[k] ?? 0), backgroundColor: palette[2] },
        { label: 'Q4', data: labelsSources.map(k => SRC_Q4[k] ?? 0), backgroundColor: palette[3] },
      ];
      title += ' (Q1–Q4)';
    } else {
      // one quarter only
      const map = { q1: SRC_Q1, q2: SRC_Q2, q3: SRC_Q3, q4: SRC_Q4 };
      const name = { q1:'Q1 (Jan–Mar)', q2:'Q2 (Apr–Jun)', q3:'Q3 (Jul–Sep)', q4:'Q4 (Oct–Dec)' }[mode];
      datasets = [{ label: name, data: labelsSources.map(k => (map[mode][k] ?? 0)), backgroundColor: palette[0] }];
      title += ` – ${name}`;
    }

    compareChart = new Chart(ctx, {
      type: 'bar',
      data: { labels: labelsSources, datasets },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { position: 'bottom' }, title: { display: false, text: title } },
        scales: {
          y: { beginAtZero: true, ticks: { precision: 0, stepSize: 1 } },
          x: { grid: { display: false } }
        }
      }
    });

    document.getElementById('compareTitle').textContent = title;
  }

  // default: H1 vs H2
  renderCompareChart('all');

  const compareControls = document.getElementById('compareControls');
  compareControls.addEventListener('click', (e)=>{
    const btn = e.target.closest('.qbtn'); if (!btn) return;
    compareControls.querySelectorAll('.qbtn').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    renderCompareChart(btn.dataset.view);
  });
</script>
BLADE !!}
