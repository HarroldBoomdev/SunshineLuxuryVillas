@push('styles')
<style>
  #featuredModal { --bs-modal-width: 98vw; }
  #featuredModal .fp-line {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
  }
  #featuredModal .fp-left-item .flex-fill,
  #featuredModal .fp-right-item .flex-fill { min-width: 0; }
  #featuredModal .fp-badge,
  #featuredModal .fp-remove { flex: 0 0 auto; }
</style>
@endpush

<!-- Featured Properties Modal -->
<div class="modal fade mt-1" id="featuredModal" tabindex="-1" aria-labelledby="featuredModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Manage Featured Properties (max 12)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div class="row g-3">

          <!-- LEFT: property list -->
          <div class="col-12 col-xl-7 col-lg-7 col-md-7">
            <div class="d-flex mb-2 gap-2">
              <input id="fp-search" type="text" class="form-control" placeholder="Search by reference or title">
              <button id="fp-clear" class="btn btn-outline-secondary" type="button">Clear</button>
            </div>

            <div class="border rounded">
              <ul id="fp-left-list" class="list-group list-group-flush" style="max-height:460px;overflow:auto;">
                @foreach($pickerProps as $p)
                  <li class="list-group-item d-flex align-items-start gap-3 fp-left-item"
                      data-id="{{ $p->id }}"
                      data-ref="{{ $p->reference }}"
                      data-title="{{ $p->title ?? 'Untitled' }}"
                      data-location="{{ $p->location ?? 'N/A' }}">
                    <div class="form-check mt-1">
                      <input class="form-check-input fp-left-cb" type="checkbox" value="{{ $p->id }}">
                    </div>
                    <div class="flex-fill">
                      <div class="fw-semibold fp-line">
                        <span class="text-muted">#</span>
                        <span class="fp-ref">{{ $p->reference }}</span>
                        — <span class="fp-title">{{ $p->title ?? 'Untitled' }}</span>
                      </div>
                      <div class="small text-muted d-none d-xl-block">{{ $p->location ?? 'N/A' }}</div>
                    </div>
                    <span class="badge bg-info-subtle text-info-emphasis d-none fp-already">Already featured</span>
                  </li>
                @endforeach
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
              <ul id="fp-right-list" class="list-group list-group-flush" style="max-height:520px;overflow:auto;"></ul>
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
(() => {
  const limit = 12;
  const saveUrl = "/api/featured-properties";
  const token = document.querySelector('meta[name="csrf-token"]')?.content;

  const $ = (id) => document.getElementById(id);

  const modalEl   = $('featuredModal');
  const searchEl  = $('fp-search');
  const clearBtn  = $('fp-clear');
  const addBtn    = $('fp-add-btn');
  const saveBtn   = $('fp-save-btn');
  const leftUl    = $('fp-left-list');
  const rightUl   = $('fp-right-list');

  if (!modalEl || !searchEl || !clearBtn || !addBtn || !saveBtn || !leftUl || !rightUl) return;

  const countFeatured = () => rightUl.querySelectorAll('li.fp-right-item').length;

  const setFeaturedCount = () => {
    $('fp-featured-count').textContent = countFeatured();
  };

  const setSelectedCount = () => {
    $('fp-selected-count').textContent = leftUl.querySelectorAll('.fp-left-cb:checked').length;
    enforceSelectionCap();
  };

  function markLeftFeaturedByRef(ref) {
    const row = leftUl.querySelector(`.fp-left-item[data-ref="${ref}"]`);
    if (!row) return;
    row.classList.add('is-featured');
    row.querySelector('.fp-already')?.classList.remove('d-none');
    const cb = row.querySelector('.fp-left-cb');
    cb.checked = false;
    cb.disabled = true;
  }

  function unmarkLeftFeaturedByRef(ref) {
    const row = leftUl.querySelector(`.fp-left-item[data-ref="${ref}"]`);
    if (!row) return;
    row.classList.remove('is-featured');
    row.querySelector('.fp-already')?.classList.add('d-none');
    const cb = row.querySelector('.fp-left-cb');
    cb.disabled = (limit - countFeatured()) <= 0;
  }

  function enforceSelectionCap() {
    const cap = Math.max(0, limit - countFeatured());
    const selected = leftUl.querySelectorAll('.fp-left-cb:checked').length;

    leftUl.querySelectorAll('.fp-left-cb').forEach(cb => {
      const featured = cb.closest('.fp-left-item')?.classList.contains('is-featured');
      if (featured) { cb.disabled = true; return; }

      if (cap === 0) cb.disabled = !cb.checked;
      else cb.disabled = false;
    });

    addBtn.disabled = (selected === 0) || (cap === 0);
  }

  function createRightLi({ ref, title = 'Untitled', location = 'N/A' }) {
    const li = document.createElement('li');
    li.className = 'list-group-item d-flex align-items-start justify-content-between gap-3 fp-right-item';
    li.dataset.ref = ref;
    li.innerHTML = `
      <div class="flex-fill">
        <div class="fw-semibold fp-line">
          <span class="text-muted">#</span>${ref} — <span class="fp-title">${title}</span>
        </div>
        <div class="small text-muted d-none d-xl-block">${location}</div>
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

  // ---------- AJAX search loader ----------
  async function loadLeftAjax(term = '') {
    try {
      const res = await fetch(`/admin/properties/picker?q=${encodeURIComponent(term)}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
      });

      const data = await res.json();
      const items = data.items || [];

      leftUl.innerHTML = '';

      if (!items.length) {
        leftUl.innerHTML = '<li class="list-group-item text-muted">No results found.</li>';
        enforceSelectionCap();
        setSelectedCount();
        return;
      }

      items.forEach(p => {
        const li = document.createElement('li');
        li.className = 'list-group-item d-flex align-items-start gap-3 fp-left-item';
        li.dataset.id = p.id;
        li.dataset.ref = p.reference || '';
        li.dataset.title = p.title || 'Untitled';
        li.dataset.location = p.location || 'N/A';

        li.innerHTML = `
          <div class="form-check mt-1">
            <input class="form-check-input fp-left-cb" type="checkbox" value="${p.id}">
          </div>
          <div class="flex-fill">
            <div class="fw-semibold fp-line">
              <span class="text-muted">#</span>${p.reference || ''} — ${p.title || 'Untitled'}
            </div>
            <div class="small text-muted d-none d-xl-block">${p.location || 'N/A'}</div>
          </div>
          <span class="badge bg-info-subtle text-info-emphasis d-none fp-already">Already featured</span>
        `;

        leftUl.appendChild(li);
      });

      enforceSelectionCap();
      setSelectedCount();
    } catch (err) {
      console.error('loadLeftAjax failed:', err);
    }
  }

  // ---------- events ----------
  modalEl.addEventListener("shown.bs.modal", () => {
    setFeaturedCount();
    enforceSelectionCap();
    searchEl.focus();
    // Optional: load default suggestions
    // loadLeftAjax('');
  });

  clearBtn.addEventListener('click', () => {
    searchEl.value = '';
    loadLeftAjax('');
    searchEl.focus();
  });

  let searchTimer = null;
  searchEl.addEventListener('input', () => {
    clearTimeout(searchTimer);
    const term = searchEl.value.trim();
    searchTimer = setTimeout(() => {
      if (term.length >= 3) loadLeftAjax(term);
      else if (term.length === 0) loadLeftAjax('');
    }, 300);
  });

  leftUl.addEventListener('change', (e) => {
    if (!e.target.classList.contains('fp-left-cb')) return;
    setSelectedCount();
  });

  addBtn.addEventListener('click', () => {
    const available = Math.max(0, limit - countFeatured());
    const picks = Array.from(leftUl.querySelectorAll('.fp-left-cb:checked')).slice(0, available);

    picks.forEach(cb => {
      const row = cb.closest('.fp-left-item');
      const data = { ref: row.dataset.ref, title: row.dataset.title, location: row.dataset.location };

      if (rightUl.querySelector(`.fp-right-item[data-ref="${data.ref}"]`)) return;

      rightUl.appendChild(createRightLi(data));
      markLeftFeaturedByRef(data.ref);
      cb.checked = false;
    });

    setFeaturedCount();
    enforceSelectionCap();
    setSelectedCount();
  });

  saveBtn.addEventListener('click', async () => {
    const refs = Array.from(rightUl.querySelectorAll('.fp-right-item')).map(li => li.dataset.ref);

    if (!refs.length) return alert('Please select at least 1 featured property (max 12).');

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

