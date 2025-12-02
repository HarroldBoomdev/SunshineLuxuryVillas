<div class="container my-4">
    <!-- BASICS -->
    <div class="card shadow-sm border-0 mb-4">
      <div class="card-header bg-white"><strong>Basics</strong></div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Reference <span class="text-danger">*</span></label>
            <input name="reference" id="reference" class="form-control" required>
            <div class="invalid-feedback">Reference is required.</div>
          </div>

          <div class="col-md-4">
            <label class="form-label">Price</label>
            <div class="input-group">
              <input type="number" name="price" id="price" class="form-control" placeholder="0">
              <span class="input-group-text">€ / £</span>
            </div>
          </div>

          <div class="col-md-2 d-flex align-items-end">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="poa" name="poa">
              <label for="poa" class="form-check-label">POA (hide price)</label>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- PROPERTY DETAILS (Yes/No selects + conditional Floor + pool description) -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white"><strong>Property Details</strong></div>
        <div class="card-body">

        <!-- Row 1 (no nested rows; perfectly aligned) -->
        <div class="row g-3">
            <!-- Property Type -->
            <div class="col-md-3">
                <label class="form-label">Property Type:</label>
                <select class="form-select" name="property_type" id="property_type">
                <option value="">None</option>
                <option>Apartment</option>
                <option>Penthouse</option>
                <option>Bungalow</option>
                <option>Commercial Property</option>
                <option>Investment Property</option>
                <option>Plot</option>
                <option>Studio</option>
                <option>Townhouse</option>
                <option>Villa</option>
                </select>
            </div>

            <!-- Floor (only for Apartment/Penthouse) -->
            <div class="col-md-2" id="floorCol" style="display:none;">
                <label class="form-label">Floor</label>
                <input type="number" min="0" class="form-control" name="floor" id="floor" placeholder="e.g. 5">
            </div>

            <!-- Has Title Deeds -->
            <div class="col-md-2">
                <label class="form-label">Has Title Deeds:</label>
                <select class="form-select" name="title_deeds" id="title_deeds">
                <option value="">Select…</option>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
                </select>
            </div>

            <!-- Long Let -->
            <div class="col-md-2">
                <label class="form-label">Long Let?</label>
                <select class="form-select" name="long_let" id="long_let">
                <option value="">Select…</option>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
                </select>
            </div>

            <!-- Leasehold -->
            <div class="col-md-3">
                <label class="form-label">Leasehold Property?</label>
                <select class="form-select" name="leasehold" id="leasehold">
                <option value="">Select…</option>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
                </select>
            </div>
        </div>


            <!-- Row 2 -->
        <div class="row g-3 mt-2">
            <div class="col-md-3">
                <label class="form-label">Bedrooms:</label>
                <input type="number" class="form-control" name="bedrooms" min="0" value="0">
            </div>

            <div class="col-md-3">
                <label class="form-label">Bathrooms:</label>
                <input type="number" class="form-control" name="bathrooms" min="0" value="0">
            </div>

            <div class="col-md-3">
                <label class="form-label">Build:</label>
                <div class="input-group">
                <input type="number" class="form-control" name="build" min="0" value="0">
                <span class="input-group-text">m²</span>
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label">Terrace:</label>
                <div class="input-group">
                <input type="number" class="form-control" name="terrace" min="0" value="0">
                <span class="input-group-text">m²</span>
                </div>
            </div>
            </div>

            <!-- Row 3 -->
            <div class="row g-3 mt-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Plot:</label>
                <div class="input-group">
                <input type="number" class="form-control" name="plot" min="0" value="0">
                <span class="input-group-text">m²</span>
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label">Pool:</label>
                <select class="form-select" name="pool" id="pool">
                <option value="" selected disabled>Select...</option>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
                </select>
            </div>

            <!-- Hidden until pool = Yes -->
            <div class="col-md-6" id="poolDescriptionWrapper" style="display:none;">
                <label class="form-label">Pool Description:</label>
                <input type="text" class="form-control" name="pool_description" id="pool_description"
                    placeholder="Enter pool details (e.g. Infinity, Heated, Shared)">
            </div>
            </div>

            </div>
        </div>

        {{-- PRICE (extended to match legacy screen) --}}
        <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white"><strong>Price</strong></div>
        <div class="card-body">
            <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Currency</label>
                <select name="currency" id="currency" class="form-select">
                <option value="Default">Default Currency</option>
                <option value="EUR">EUR (€)</option>
                <option value="GBP">GBP (£)</option>
                <option value="USD">USD ($)</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Current Price</label>
                <div class="input-group">
                <input type="number" class="form-control" name="price_current" id="price_current" min="0" step="1">
                <span class="input-group-text">POA</span>
                <div class="input-group-text">
                    <input class="form-check-input mt-0" type="checkbox" id="poa_current">
                </div>
                </div>
                <small class="text-muted">Tick POA to hide price.</small>
            </div>

            <div class="col-md-3">
                <label class="form-label">Original Price</label>
                <input type="number" class="form-control" name="price_original" id="price_original" min="0" step="1">
            </div>

            <div class="col-md-3">
                <label class="form-label">Total Reduction %</label>
                <div class="input-group">
                <input type="number" class="form-control" name="reduction_percent" id="reduction_percent" min="0" max="100" step="0.01">
                <span class="input-group-text">%</span>
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label">Total Reduction (Price)</label>
                <input type="number" class="form-control" name="reduction_value" id="reduction_value" min="0" step="1">
            </div>

            <div class="col-md-3">
                <label class="form-label">Display as Percentage?</label>
                <select name="display_as_percentage" id="display_as_percentage" class="form-select">
                <option value="">No</option>
                <option value="Yes">Yes</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Monthly Rent</label>
                <input type="number" class="form-control" name="monthly_rent" id="monthly_rent" min="0" step="1" value="0">
            </div>
            </div>
        </div>
        </div>

        {{-- SPECIFICS (fields you’re missing) --}}
        <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white"><strong>Specifics</strong></div>
        <div class="card-body">
            <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Listing Type</label>
                <select name="listing_type" id="listing_type" class="form-select">
                <option value="">Select…</option>
                <option>Resale</option>
                <option>New</option>
                <option>Investment Property</option>
                <option>Commercial</option>
                <option>Plot / Land</option>
                <option>Rental</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Plan Zone</label>
                <select name="plan_zone" id="plan_zone" class="form-select">
                <option value="">None</option>
                <option>A</option><option>B</option><option>C</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Sea View</label>
                <select name="sea_view" id="sea_view" class="form-select">
                <option value="">No</option>
                <option value="Yes">Yes</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">For Sale Board?</label>
                <select name="for_sale_board" id="for_sale_board" class="form-select">
                <option value="">No</option>
                <option value="Yes">Yes</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Property Age (years)</label>
                <input type="number" class="form-control" name="property_age" id="property_age" min="0" step="1">
            </div>

            <div class="col-md-9">
                <label class="form-label">Plot Description</label>
                <input type="text" class="form-control" name="plot_description" id="plot_description" placeholder="e.g. Corner plot, flat, slight slope, cul-de-sac">
            </div>
            </div>
        </div>
        </div>
</div>

<style>
.fw-600{font-weight:600;}
.input-group .unit{min-width: 42px; justify-content:center;}

.checkbox-group .input-group-text {
  background: #f8f9fa;
  width: 42px;
  justify-content: center;
}
.checkbox-group .form-control[readonly] {
  background-color: #fff;
  cursor: default;
}
.card .form-label { margin-bottom: .35rem; }

.select-loading{ position:relative; }
.select-loading::after{
  content:""; position:absolute; right:.75rem; top:50%;
  width:16px; height:16px; margin-top:-8px;
  border:2px solid rgba(0,0,0,.15); border-top-color:rgba(0,0,0,.45);
  border-radius:50%; animation:sl-spin .6s linear infinite; pointer-events:none;
}
@keyframes sl-spin{to{transform:rotate(360deg)}}

</style>

<script>

document.addEventListener('DOMContentLoaded', () => {
  // POA on the Price panel
  const poa = document.getElementById('poa_current');
  const priceCur = document.getElementById('price_current');
  poa?.addEventListener('change', () => {
    priceCur.disabled = poa.checked;
    if (poa.checked) priceCur.value = '';
  });

  // Reduction auto-calc between % and value
  const priceOrig = document.getElementById('price_original');
  const priceNow  = document.getElementById('price_current');
  const redPct    = document.getElementById('reduction_percent');
  const redVal    = document.getElementById('reduction_value');

  function toNum(v){ return parseFloat(v || '0') || 0; }

  function recalcFromPct(){
    const OP = toNum(priceOrig.value), CP = toNum(priceNow.value), P = toNum(redPct.value);
    if (OP > 0 && P >= 0) redVal.value = Math.round(OP * (P/100));
  }
  function recalcFromVal(){
    const OP = toNum(priceOrig.value), V = toNum(redVal.value);
    if (OP > 0 && V >= 0) redPct.value = (V / OP * 100).toFixed(2);
  }
  function recalcBoth(){
    // prefer computing % from value if value present; else compute value from %
    if (redVal.value) recalcFromVal(); else recalcFromPct();
  }

  priceOrig?.addEventListener('input', recalcBoth);
  priceNow ?.addEventListener('input', recalcBoth);
  redPct   ?.addEventListener('input', recalcFromPct);
  redVal   ?.addEventListener('input', recalcFromVal);
});


document.addEventListener('DOMContentLoaded', () => {
  // prevent Enter from submitting
  const form = document.getElementById('propertyForm');
  form.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') e.preventDefault();
  });

  // POA toggle
  const poa = document.getElementById('poa');
  const price = document.getElementById('price');
  poa.addEventListener('change', () => {
    price.disabled = poa.checked;
    if (poa.checked) price.value = '';
  });

  // Pool description toggle
  const pool = document.getElementById('pool');
  const poolWrap = document.getElementById('poolDescriptionWrapper');
  const poolDesc = document.getElementById('pool_description');
  function togglePoolDesc(){
    if (pool.value === 'Yes'){ poolWrap.style.display='block'; poolDesc.required=true; }
    else { poolWrap.style.display='none'; poolDesc.required=false; poolDesc.value=''; }
  }
  pool.addEventListener('change', togglePoolDesc); togglePoolDesc();

document.addEventListener('DOMContentLoaded', () => {
  const typeSel  = document.getElementById('property_type');
  const floorCol = document.getElementById('floorCol');
  const floorInp = document.getElementById('floor');

  function toggleFloor(){
    const v = (typeSel.value || '').toLowerCase();
    const show = v === 'apartment' || v === 'penthouse';
    floorCol.style.display = show ? 'block' : 'none';
    floorInp.required = show;
    if (!show) floorInp.value = '';
  }
  typeSel.addEventListener('change', toggleFloor);
  toggleFloor(); // initialize on load
});
</script>

