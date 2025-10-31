@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <!-- Header + Progress -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                <h5 class="mb-0">Add Property</h5>
                <small class="text-muted">Step <span id="stepNow">1</span> / <span id="stepTotal">10</span></small>
                </div>
                <div class="flex-grow-1 mx-4">
                <div class="progress" style="height:8px">
                    <div id="wizardProgress" class="progress-bar" style="width: 25%"></div>
                </div>
                </div>
            </div>
            </div>


        <section class="wizard-step" data-step="1">
            @include('forms.partials.reference')
        </section>

        <section class="wizard-step d-none" data-step="2">
            @include('forms.partials.location')
        </section>

        <section class="wizard-step d-none" data-step="3">
            @include('forms.partials.photos')
        </section>

        <section class="wizard-step d-none" data-step="4">
            @include('forms.partials.floorplans')
        </section>

        <section class="wizard-step d-none" data-step="5">
            @include('forms.partials.areas')
        </section>

        <section class="wizard-step d-none" data-step="6">
            @include('forms.partials.options')
        </section>

        <section class="wizard-step d-none" data-step="7">
            @include('forms.partials.vendor')
        </section>

        <section class="wizard-step d-none" data-step="8">
            @include('forms.partials.customfields')
        </section>


        <div class="d-flex justify-content-between mt-3 mb-5">
            <button type="button" class="btn btn-light" id="btnPrev" disabled>← Back</button>
            <div>
                <button type="button" class="btn btn-primary" id="btnNext">Next →</button>
            </div>
        </div>

    </div>

<style>
    #wizardControls {
        margin-bottom: 3rem;
    }

</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // Collect steps
  const steps = [...document.querySelectorAll('.wizard-step')];
  if (steps.length === 0) return; // nothing to do

  // Buttons + UI bits
  const btnPrev = document.getElementById('btnPrev');
  const btnNext = document.getElementById('btnNext');
  const progress = document.getElementById('wizardProgress');
  const stepNowEl = document.getElementById('stepNow');
  const stepTotalEl = document.getElementById('stepTotal');
  const form = document.querySelector('form'); // optional, used to submit on last step

  // Init counters
  const total = steps.length;
  let current = 1;

  // Reflect total in header
  if (stepTotalEl) stepTotalEl.textContent = total;

  // Helper to show a step
  function showStep(n) {
    current = n;
    steps.forEach(s => s.classList.toggle('d-none', +s.dataset.step !== n));
    // Buttons
    btnPrev.disabled = n === 1;
    // Next ↔ Submit swap on last step
    if (n === total) {
      btnNext.textContent = 'Submit';
      btnNext.classList.remove('btn-primary');
      btnNext.classList.add('btn-success');
      btnNext.type = form ? 'submit' : 'button';
    } else {
      btnNext.textContent = 'Next →';
      btnNext.classList.remove('btn-success');
      btnNext.classList.add('btn-primary');
      btnNext.type = 'button';
    }
    // Progress + labels
    if (progress) progress.style.width = (n / total * 100) + '%';
    if (stepNowEl) stepNowEl.textContent = n;
  }

  // Optional: simple validation on step 1 (Reference required)
  function validateStep(n) {
    if (n !== 1) return true;
    const ref = document.getElementById('reference');
    if (!ref) return true;
    if (!ref.value.trim()) {
      ref.classList.add('is-invalid');
      ref.focus();
      return false;
    }
    ref.classList.remove('is-invalid');
    return true;
  }

  // Hook up buttons
  btnPrev.addEventListener('click', () => {
    if (current > 1) showStep(current - 1);
  });

  btnNext.addEventListener('click', (e) => {
    if (current < total) {
      if (!validateStep(current)) return;
      showStep(current + 1);
    } else {
      // Last step -> submit (if inside a form and type=submit)
      if (form && btnNext.type === 'submit') {
        // Allow normal form submit
      } else {
        // Not in a form; you could handle custom submit here if needed
        console.warn('No form found to submit.');
      }
    }
  });

  // Start
  showStep(1);
});

// local
document.addEventListener('DOMContentLoaded', () => {
  /* -----------------------------
   * Wizard (2 steps)
   * --------------------------- */
  const steps = [...document.querySelectorAll('.wizard-step')];
  const btnPrev = document.getElementById('btnPrev');
  const btnNext = document.getElementById('btnNext');
  const progress = document.getElementById('wizardProgress');
  const stepNowEl = document.getElementById('stepNow');
  const stepTotalEl = document.getElementById('stepTotal');

  const TOTAL = steps.length || 2;
  let current = 1;

  if (stepTotalEl) stepTotalEl.textContent = TOTAL;

  function showStep(n){
    current = n;
    steps.forEach(s => s.classList.toggle('d-none', +s.dataset.step !== n));
    btnPrev.disabled = n === 1;
    btnNext.textContent = (n === TOTAL) ? 'Submit' : 'Next →';
    btnNext.classList.toggle('btn-success', n === TOTAL);
    btnNext.classList.toggle('btn-primary', n !== TOTAL);
    if (progress) progress.style.width = ((n / TOTAL) * 100) + '%';
    if (stepNowEl) stepNowEl.textContent = n;
  }

  btnPrev?.addEventListener('click', () => showStep(current - 1));
  btnNext?.addEventListener('click', () => {
    if (current < TOTAL) {
      saveDraft();      // save before moving on
      showStep(current + 1);
    } else {
      // last step; submit surrounding form (or handle AJAX here)
      document.getElementById('propertyForm')?.submit();
    }
  });

  showStep(1);

  /* -----------------------------
   * Draft save/restore (localStorage)
   * --------------------------- */
  const STORAGE_KEY = 'propertyDraft:v1';

  // IDs to persist
  const FIELDS = [
    'reference','price','poa',
    'country','region','town','locality',
    'property_type','title_deeds','long_let','leasehold',
    'bedrooms','bathrooms','build','terrace','plot',
    'pool','pool_description'
  ];

  function getEl(id){ return document.getElementById(id); }

  function saveDraft(){
    const data = {};
    FIELDS.forEach(id => {
      const el = getEl(id);
      if (!el) return;
      if (el.type === 'checkbox') data[id] = el.checked;
      else data[id] = el.value;
    });
    localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
  }

  function restoreDraft(){
    const raw = localStorage.getItem(STORAGE_KEY);
    if (!raw) return;
    const data = JSON.parse(raw);

    // 1) put back simple fields immediately
    FIELDS.forEach(id => {
      const el = getEl(id);
      if (!el || !(id in data)) return;
      if (el.type === 'checkbox') el.checked = !!data[id];
      else el.value = data[id] ?? '';
    });

    // 2) trigger dependent UI
    togglePoolDesc();

    // 3) restore cascade Country -> Region -> Town in order
    //    (we rely on your existing AJAX cascade functions)
    const $country = getEl('country');
    const $region  = getEl('region');
    const $town    = getEl('town');

    // helper to wait until options arrive then set value
    function setWhenReady(select, value, timeout = 3000){
      return new Promise(resolve => {
        const start = Date.now();
        const i = setInterval(() => {
          const found = [...select.options].some(o => o.value === value || o.text === value);
          if (found || (Date.now() - start) > timeout){
            if (found) select.value = value;
            clearInterval(i);
            resolve();
          }
        }, 50);
      });
    }

    if (data.country) {
      $country.value = data.country;
      $country.dispatchEvent(new Event('change')); // loads regions
      setWhenReady($region, data.region).then(() => {
        $region.dispatchEvent(new Event('change')); // loads towns
        setWhenReady($town, data.town);
      });
    }
  }

  // auto-save on any change (debounced)
  let saveTimer = null;
  document.querySelectorAll('#propertyForm input, #propertyForm select, #propertyForm textarea')
    .forEach(el => el.addEventListener('input', () => {
      clearTimeout(saveTimer);
      saveTimer = setTimeout(saveDraft, 250);
    }));

  restoreDraft();

  // Optional: clear draft on successful submit (if you keep the same page)
  document.getElementById('propertyForm')?.addEventListener('submit', () => {
    localStorage.removeItem(STORAGE_KEY);
  });

  /* -----------------------------
   * Minor UX bits already in your page
   * --------------------------- */
  // Prevent Enter from submitting
  document.getElementById('propertyForm')
    ?.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') e.preventDefault();
    });

  // Pool description toggle
  const pool = getEl('pool');
  const poolWrap = getEl('poolDescriptionWrapper');
  const poolDesc = getEl('pool_description');
  function togglePoolDesc(){
    if (pool && pool.value === 'Yes'){
      poolWrap.style.display = 'block'; poolDesc.required = true;
    } else {
      poolWrap.style.display = 'none';  poolDesc.required = false; poolDesc.value = '';
    }
  }
  pool?.addEventListener('change', togglePoolDesc);
  togglePoolDesc();
});

</script>

@endsection
