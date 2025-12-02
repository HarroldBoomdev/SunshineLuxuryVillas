@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <!-- Header + Progress -->
        <div class="card shadow-sm border-0 mb-3">
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
        showStep(1);
    });
    </script>
@endsection
