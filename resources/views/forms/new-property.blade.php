@extends('layouts.app')

@section('content')


    <div class="container my-4">
        <!-- Header + Progress -->
        <div class="card shadow-sm border-0 mb-3">
             @include('layouts.newButton')
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0">Add Property</h5>
                    <small class="text-muted">
                        Step <span id="stepNow">1</span> / <span id="stepTotal">10</span>
                    </small>
                </div>
                <div class="flex-grow-1 mx-4">
                    <div class="progress" style="height:8px">
                        <div id="wizardProgress" class="progress-bar" style="width: 10%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top step navigation (clickable) --}}
        @php
            $wizardSteps = [
                1 => 'Reference',
                2 => 'Location',
                3 => 'Photos',
                4 => 'Floor Plan',
                5 => 'Amenities',
                6 => 'Options',
                7 => 'Vendor',
                8 => 'Custom Fields',
                9 => 'V-tour',
                10 => 'AI Title',
            ];
        @endphp

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between align-items-center flex-wrap wizard-steps-nav">
                    @foreach($wizardSteps as $num => $label)
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-secondary mb-2 wizard-step-nav-item"
                            data-step="{{ $num }}"
                        >
                            <span class="fw-bold">{{ $num }}</span>
                            <span class="ms-1 d-none d-md-inline">{{ $label }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Steps --}}
        <form id="propertyForm"
              action="{{ route('properties.store') }}"
              method="POST"
              enctype="multipart/form-data"
              autocomplete="off"
              novalidate>
            @csrf

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Please fix the errors below:</strong>
                    <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                    </ul>
                </div>
            @endif


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

            <section class="wizard-step d-none" data-step="9">
                @include('forms.partials.facilities')
            </section>

            <section class="wizard-step d-none" data-step="10">
                @include('forms.partials.title')
            </section>

            <div class="d-flex justify-content-between mt-3 mb-5">
                <button type="button" class="btn btn-light" id="btnPrev" disabled>← Back</button>
                <button type="button" class="btn btn-primary" id="btnNext">Next →</button>
            </div>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        /* ------------------------------------------------
         * Wizard navigation
         * ------------------------------------------------ */
        const steps       = [...document.querySelectorAll('.wizard-step')];
        const btnPrev     = document.getElementById('btnPrev');
        const btnNext     = document.getElementById('btnNext');
        const progress    = document.getElementById('wizardProgress');
        const stepNowEl   = document.getElementById('stepNow');
        const stepTotalEl = document.getElementById('stepTotal');
        const form        = document.getElementById('propertyForm');
        const stepNavItems = [...document.querySelectorAll('.wizard-step-nav-item')];

        if (!steps.length || !btnNext || !btnPrev) {
            console.warn('Wizard: required elements missing.');
            return;
        }

        const TOTAL = steps.length;
        let current = 1;

        if (stepTotalEl) stepTotalEl.textContent = TOTAL;

        function setActiveNav(step) {
            stepNavItems.forEach(btn => {
                const s = +btn.dataset.step;
                btn.classList.toggle('btn-primary', s === step);
                btn.classList.toggle('btn-outline-secondary', s !== step);
            });
        }

        function showStep(n) {
            current = n;

            steps.forEach(s => {
                s.classList.toggle('d-none', +s.dataset.step !== n);
            });

            btnPrev.disabled = n === 1;

            if (n === TOTAL) {
                btnNext.textContent = 'Submit';
                btnNext.classList.remove('btn-primary');
                btnNext.classList.add('btn-success');
            } else {
                btnNext.textContent = 'Next →';
                btnNext.classList.remove('btn-success');
                btnNext.classList.add('btn-primary');
            }

            if (progress)  progress.style.width = ((n / TOTAL) * 100) + '%';
            if (stepNowEl) stepNowEl.textContent = n;

            setActiveNav(n);
        }

        // minimal validation: require reference on step 1
        function validateStep(n) {

            // -------------------------
            // STEP 1
            // -------------------------
            if (n === 1) {
                let ok = true;

                // Reference required
                const ref = document.getElementById('reference');
                if (ref && !ref.value.trim()) {
                ref.classList.add('is-invalid');
                ref.focus();
                ok = false;
                } else {
                ref?.classList.remove('is-invalid');
                }

                // Pool description required if pool = Yes
                const pool = document.getElementById('pool');
                const poolDesc = document.getElementById('pool_description');

                if (pool?.value === 'Yes') {
                if (!poolDesc?.value.trim()) {
                    poolDesc.classList.add('is-invalid');
                    poolDesc.focus();
                    ok = false;
                } else {
                    poolDesc.classList.remove('is-invalid');
                }
                } else {
                poolDesc?.classList.remove('is-invalid');
                }

                // Floor required if Apartment/Penthouse
                const typeSel = document.getElementById('property_type');
                const floorInp = document.getElementById('floor');
                const v = (typeSel?.value || '').toLowerCase();
                const needsFloor = (v === 'apartment' || v === 'penthouse');

                if (needsFloor) {
                if (!floorInp?.value) {
                    floorInp.classList.add('is-invalid');
                    floorInp.focus();
                    ok = false;
                } else {
                    floorInp.classList.remove('is-invalid');
                }
                } else {
                floorInp?.classList.remove('is-invalid');
                }

                return ok;
            }

            // -------------------------
            // STEP 2
            // -------------------------
            if (n === 2) {
                let ok = true;

                const country = document.getElementById('country');
                const region  = document.getElementById('region');
                const town    = document.getElementById('town');

                const lat  = document.getElementById('latitude');
                const lng  = document.getElementById('longitude');
                const addr = document.getElementById('map_address');

                function invalid(el){
                if (!el) return;
                el.classList.add('is-invalid');
                if (ok) el.focus();
                ok = false;
                }
                function valid(el){
                el?.classList.remove('is-invalid');
                }

                if (!country?.value) invalid(country); else valid(country);
                if (!region?.value)  invalid(region);  else valid(region);
                if (!town?.value)    invalid(town);    else valid(town);

                if (!lat?.value)  invalid(lat);  else valid(lat);
                if (!lng?.value)  invalid(lng);  else valid(lng);
                if (!addr?.value) invalid(addr); else valid(addr);

                return ok;
            }

            // -------------------------
            // OTHER STEPS (for now)
            // -------------------------
            return true;
            }

           // -------------------------
            // STEP 3
            // -------------------------
            if (n === 3) {
            const fileInput = document.getElementById('photos');
            const photosError = document.getElementById('photosError');

            const count = fileInput?.files?.length || 0;

            if (count < 1) {
                photosError?.classList.remove('d-none');

                // Helpful: auto-open the gallery modal so user can add photos
                const modalEl = document.getElementById('galleryModal');
                if (modalEl && window.bootstrap) {
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
                }

                return false;
            }

            photosError?.classList.add('d-none');
            return true;
            }

            // =======================
            // STEP 4 – Floor Plans / Title Deed
            // =======================
            if (n === 4) {
            let ok = true;

            const titleDeeds = document.getElementById('title_deeds');
            const needsDeed  = (titleDeeds?.value === 'Yes');

            const deedInput  = document.getElementById('deedPhotos');
            const deedError  = document.getElementById('titleDeedError');

            if (needsDeed) {
                const count = deedInput?.files?.length || 0;

                if (count === 0) {
                deedError?.classList.remove('d-none');

                // auto-open modal to help user
                const modalEl = document.getElementById('deedGalleryModal');
                if (modalEl && window.bootstrap) {
                    bootstrap.Modal.getOrCreateInstance(modalEl).show();
                }

                ok = false;
                } else {
                deedError?.classList.add('d-none');
                }
            } else {
                deedError?.classList.add('d-none');
            }

            return ok;
            }



        btnPrev.addEventListener('click', () => {
            if (current > 1) showStep(current - 1);
        });

        btnNext.addEventListener('click', () => {
            if (current < TOTAL) {
                if (!validateStep(current)) return;
                showStep(current + 1);
            } else {
                // last step → submit form
                if (form) {
                    form.submit();
                }
            }
        });

        // Click on top step buttons
        stepNavItems.forEach(btn => {
            btn.addEventListener('click', () => {
                const targetStep = +btn.dataset.step;
                if (!validateStep(current)) return;
                showStep(targetStep);
            });
        });

        /* ------------------------------------------------
         * Minor UX bits
         * ------------------------------------------------ */
        // prevent Enter from submitting except in textarea
        form?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
            }
        });

        // pool description show/hide (if you use that in Step 1)
        function getEl(id){ return document.getElementById(id); }
        const pool = getEl('pool');
        const poolWrap = getEl('poolDescriptionWrapper');
        const poolDesc = getEl('pool_description');

        function togglePoolDesc() {
            if (!pool || !poolWrap || !poolDesc) return;
            if (pool.value === 'Yes') {
                poolWrap.style.display = 'block';
                poolDesc.required = true;
            } else {
                poolWrap.style.display = 'none';
                poolDesc.required = false;
                poolDesc.value = '';
            }
        }

        pool?.addEventListener('change', togglePoolDesc);

        // Initial state
        togglePoolDesc();
        // If Laravel returned validation errors, jump to the step that contains the first invalid field
        const firstInvalid = form?.querySelector('.is-invalid');
        if (firstInvalid) {
        const stepSection = firstInvalid.closest('.wizard-step');
        if (stepSection) showStep(+stepSection.dataset.step || 1);
        firstInvalid.focus();
        } else {
        showStep(1);
        }

    });

    document.getElementById('reference')?.addEventListener('input', function () {
    if (this.value.trim()) this.classList.remove('is-invalid');
    });

    document.getElementById('pool_description')?.addEventListener('input', function () {
    if (this.value.trim()) this.classList.remove('is-invalid');
    });

    document.getElementById('pool')?.addEventListener('change', function () {
    document.getElementById('pool_description')?.classList.remove('is-invalid');
    });

    document.getElementById('property_type')?.addEventListener('change', function () {
    document.getElementById('floor')?.classList.remove('is-invalid');
    });

    document.getElementById('floor')?.addEventListener('input', function () {
    if (this.value) this.classList.remove('is-invalid');
    });
    document.getElementById('country')?.addEventListener('change', () => {
    document.getElementById('country')?.classList.remove('is-invalid');
    });
    document.getElementById('region')?.addEventListener('change', () => {
    document.getElementById('region')?.classList.remove('is-invalid');
    });
    document.getElementById('town')?.addEventListener('change', () => {
    document.getElementById('town')?.classList.remove('is-invalid');
    });

    document.getElementById('photos')?.addEventListener('change', function () {
    if ((this.files?.length || 0) > 0) {
        document.getElementById('photosError')?.classList.add('d-none');
    }
    });
    document.getElementById('deedPhotos')?.addEventListener('change', function () {
    if ((this.files?.length || 0) > 0) {
        document.getElementById('titleDeedError')?.classList.add('d-none');
    }
    });



    </script>
@endsection
