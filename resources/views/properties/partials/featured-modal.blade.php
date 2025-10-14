@push('styles')
<style>
  /* Make this modal almost full width (Bootstrap 5 variable) */
  #featuredModal{ --bs-modal-width: 98vw; }

  #featuredModal .fp-line{
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
  }
  /* Allow text blocks to shrink so ellipsis works inside flex rows */
  #featuredModal .fp-left-item .flex-fill,
  #featuredModal .fp-right-item .flex-fill{ min-width: 0; }
  /* Badges/buttons shouldn’t force wrapping */
  #featuredModal .fp-badge,
  #featuredModal .fp-remove{ flex: 0 0 auto; }
</style>
@endpush

<!-- Featured Properties Modal -->
<div class="modal fade mt-1" id="featuredModal" tabindex="-1" aria-labelledby="featuredModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="featuredModalLabel">Manage Featured Properties (max 12)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div class="row g-3">
          <!-- LEFT: wider column -->
          <div class="col-12 col-xl-7 col-lg-7 col-md-7">
            <div class="d-flex mb-2 gap-2">
              <input id="fp-search" type="text" class="form-control" placeholder="Search by reference or title">
              <button id="fp-clear" class="btn btn-outline-secondary" type="button">Clear</button>
            </div>

            <div class="border rounded">
              <ul id="fp-left-list" class="list-group list-group-flush" style="max-height:460px;overflow:auto;">
                <!-- AJAX items -->
              </ul>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-2">
              <div class="small text-muted">
                Selected: <span id="fp-selected-count">0</span>
              </div>
              <button id="fp-add-btn" class="btn btn-primary" type="button" disabled>Add to Featured</button>
            </div>
          </div>

          <!-- RIGHT: featured -->
          <div class="col-12 col-xl-5 col-lg-5 col-md-5">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div class="fw-semibold">Featured Properties</div>
              <div class="small text-muted">(<span id="fp-featured-count">0</span>/12)</div>
            </div>

            <div class="border rounded">
              <ul id="fp-right-list" class="list-group list-group-flush" style="max-height:520px;overflow:auto;">
                <!-- seeded + added -->
              </ul>
            </div>

            <div class="small text-muted mt-2">
              Tip: click <strong>×</strong> to remove. When 12 featured are set, left checkboxes will be disabled.
            </div>
          </div>
        </div>
      </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="fp-save-btn">Submit</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    </div>

    </div>
  </div>
</div>

@push('scripts')
<script>
(function () {
  // settings
  const limit = 12;
  const seededRefs = [
    'AMHPRV','NJSLVSC1','JOHAYSLV','LMARSLV','ZAVASLV','TGAALA',
    'TKBDIR-B201','JOTACPSLV','JOMHSLV','VTSVNSLV','ASTRSVSLV'
  ];
  const pickerUrl = "{{ route('properties.picker') }}";
  const saveUrl   = "{{ route('admin.featured.save') }}";

  // helpers
  const $ = (id) => document.getElementById(id);
  const leftList  = () => $('fp-left-list');
  const rightList = () => $('fp-right-list');
  const addBtn    = () => $('fp-add-btn');
  const token     = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  const apiByRef  = (ref) => new URL(`/api/properties/by-ref/${encodeURIComponent(ref)}`, location.origin).toString();

  function countFeatured(){ return rightList().querySelectorAll('li.fp-right-item').length; }
  function setFeaturedCount(){ $('fp-featured-count').textContent = countFeatured(); }
  function setSelectedCount(){
    const n = leftList().querySelectorAll('.fp-left-cb:checked').length;
    $('fp-selected-count').textContent = n;
    enforceSelectionCap();
  }

  function createRightLi({ ref, title = 'Untitled', location = 'N/A' }) {
    const li = document.createElement('li');
    li.className = 'list-group-item d-flex align-items-start justify-content-between gap-3 fp-right-item';
    li.dataset.ref = ref;
    li.innerHTML = `
      <div class="flex-fill">
        <div class="fw-semibold fp-line">
          <span class="text-muted">#</span><span class="fp-ref">${ref}</span>
          — <span class="fp-title">${title}</span>
        </div>
        <div class="small text-muted d-none d-xl-block">
          <span class="fp-location">${location}</span>
        </div>
      </div>
      <button type="button" class="btn btn-sm btn-outline-danger fp-remove">&times;</button>
    `;
    li.querySelector('.fp-remove').addEventListener('click', () => {
      li.remove();
      unmarkLeftFeaturedByRef(ref);
      setFeaturedCount();
      enforceSelectionCap();
      setSelectedCount();
    });
    return li;
  }

  function markLeftFeaturedByRef(ref){
    const row = leftList().querySelector(`.fp-left-item[data-ref="${ref}"]`);
    if (!row) return;
    row.classList.add('is-featured');
    row.querySelector('.fp-already')?.classList.remove('d-none');
    const cb = row.querySelector('.fp-left-cb');
    cb.checked = false;
    cb.disabled = true;
  }

  function unmarkLeftFeaturedByRef(ref){
    const row = leftList().querySelector(`.fp-left-item[data-ref="${ref}"]`);
    if (!row) return;
    row.classList.remove('is-featured');
    row.querySelector('.fp-already')?.classList.add('d-none');
    const cb = row.querySelector('.fp-left-cb');
    cb.disabled = (limit - countFeatured()) <= 0;
  }

  function enforceSelectionCap(){
    const cap = Math.max(0, limit - countFeatured());
    const selected = leftList().querySelectorAll('.fp-left-cb:checked').length;
    leftList().querySelectorAll('.fp-left-cb').forEach(cb => {
      const featured = cb.closest('.fp-left-item').classList.contains('is-featured');
      if (featured) { cb.disabled = true; return; }
      if (cap === 0) cb.disabled = !cb.checked;
      else if (cap === 1) cb.disabled = !cb.checked && selected >= 1;
      else cb.disabled = false;
    });
    addBtn().disabled = (selected === 0) || (cap === 0);
  }

  function renderLeft(items){
    const ul = leftList();
    ul.innerHTML = '';
    items.forEach(it => {
      const li = document.createElement('li');
      li.className = 'list-group-item d-flex align-items-start gap-3 fp-left-item';
      li.dataset.id = it.id;
      li.dataset.ref = it.reference;
      li.dataset.title = it.title ?? 'Untitled';
      li.dataset.location = it.location ?? 'N/A';
      li.innerHTML = `
        <div class="form-check mt-1">
          <input class="form-check-input fp-left-cb" type="checkbox" value="${it.id}">
        </div>
        <div class="flex-fill">
          <div class="fw-semibold fp-line">
            <span class="text-muted">#</span><span class="fp-ref">${it.reference}</span>
            — <span class="fp-title">${it.title ?? 'Untitled'}</span>
          </div>
          <div class="small text-muted d-none d-xl-block">${it.location ?? 'N/A'}</div>
        </div>
        <span class="badge bg-info-subtle text-info-emphasis d-none fp-already">Already featured</span>
      `;
      ul.appendChild(li);

      if (rightList().querySelector(`.fp-right-item[data-ref="${it.reference}"]`)) {
        li.classList.add('is-featured');
        li.querySelector('.fp-already').classList.remove('d-none');
        li.querySelector('.fp-left-cb').disabled = true;
      }
    });
    enforceSelectionCap();
    setSelectedCount();
  }

  async function loadLeftAjax(term){
    const url = new URL(pickerUrl, location.origin);
    if (term) url.searchParams.set('q', term);
    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, credentials: 'same-origin' });
    const data = await res.json();
    renderLeft(data.items || []);
  }

  // hydrate right items by hitting the public API for each ref
  async function hydrateRightFromApi() {
    const items = Array.from(document.querySelectorAll('#fp-right-list .fp-right-item'));
    for (const li of items) {
      const ref = li.dataset.ref;
      if (!ref) continue;
      try {
        const res = await fetch(apiByRef(ref), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
        if (!res.ok) continue;
        const payload = await res.json();
        const row = (payload && (payload.data || payload)) || {};

        li.querySelector('.fp-title')?.replaceChildren(document.createTextNode(row.title || '(no title)'));
        const loc = row.location || [row.town, (row.region || row.province), row.country].filter(Boolean).join(', ');
        li.querySelector('.fp-location')?.replaceChildren(document.createTextNode(loc || 'N/A'));
      } catch (_) {
        /* keep "(not loaded)" */
      }
    }
  }

  // seed right from static refs (first open only)
  function seedRightFromRefs(){
    if (countFeatured() > 0) return;

    // build a quick map of left items for instant fill where possible
    const left = Array.from(leftList().querySelectorAll('.fp-left-item')).map(li => ({
      ref: li.dataset.ref,
      title: li.dataset.title,
      location: li.dataset.location
    }));

    seededRefs.forEach(ref => {
      const match = left.find(p => p.ref === ref);
      const li = match
        ? createRightLi({ ref: match.ref, title: match.title, location: match.location })
        : createRightLi({ ref, title: '(not loaded)', location: '—' });
      rightList().appendChild(li);
      markLeftFeaturedByRef(ref);
    });

    setFeaturedCount();
    enforceSelectionCap();
  }

  // collect refs from right column
  function getRightRefs() {
    return Array.from(rightList().querySelectorAll('.fp-right-item'))
      .map(li => li.dataset.ref)
      .slice(0, limit);
  }

  // events
  document.getElementById('featuredModal').addEventListener('shown.bs.modal', async () => {
    if (leftList().children.length === 0) await loadLeftAjax('');
    seedRightFromRefs();
    await hydrateRightFromApi(); // fill titles/locations for seeded refs
    $('fp-search').focus();
  });

  // search
  let searchTimer = null;
  const searchEl = $('fp-search');
  $('fp-clear').addEventListener('click', () => { searchEl.value = ''; loadLeftAjax(''); });
  searchEl.addEventListener('input', () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => loadLeftAjax(searchEl.value.trim()), 300);
  });

  // selection on left
  leftList().addEventListener('change', (e) => {
    if (!e.target.classList.contains('fp-left-cb')) return;
    const cap = Math.max(0, limit - countFeatured());
    if (cap === 1) {
      const checked = Array.from(leftList().querySelectorAll('.fp-left-cb:checked'));
      if (checked.length > 1) checked.slice(0, -1).forEach(cb => cb.checked = false);
    }
    setSelectedCount();
  });

  // add to featured (right)
  addBtn().addEventListener('click', () => {
    const available = Math.max(0, limit - countFeatured());
    const picks = Array.from(leftList().querySelectorAll('.fp-left-cb:checked')).slice(0, available);

    picks.forEach(cb => {
      const row = cb.closest('.fp-left-item');
      const data = { ref: row.dataset.ref, title: row.dataset.title, location: row.dataset.location };
      if (rightList().querySelector(`.fp-right-item[data-ref="${data.ref}"]`)) return;
      rightList().appendChild(createRightLi(data));
      markLeftFeaturedByRef(data.ref);
      cb.checked = false;
    });

    setFeaturedCount();
    enforceSelectionCap();
    setSelectedCount();

    // hydrate any newly added refs too
    hydrateRightFromApi();
  });

  // save
  $('fp-save-btn').addEventListener('click', async () => {
    const refs = getRightRefs();
    if (refs.length === 0) return alert('Please select at least 1 featured property (max 12).');

    try {
      const res = await fetch(saveUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': token,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({ refs })
      });
      if (!res.ok) throw new Error(await res.text());
      const json = await res.json();
      alert(`Saved! ${json.count} featured properties updated.`);
    } catch (e) {
      console.error(e);
      alert('Save failed, please try again.');
    }
  });
})();
</script>
@endpush
