<div class="card p-3">
  <div class="form-group">
    <!-- Language Select -->
    <div class="mb-3 d-flex justify-content-end">
      <select id="languageSelect" class="form-select w-auto">
        @php
          $languages = ['english' => 'English', 'spanish' => 'Spanish', 'french' => 'French', 'german' => 'German'];
        @endphp
        @foreach ($languages as $key => $language)
          <option value="{{ $key }}" {{ $key == 'english' ? 'selected' : '' }}>{{ $language }}</option>
        @endforeach
      </select>
    </div>

    <!-- AI Title -->
    <div class="mb-3">
      <label for="titleInput" class="form-label">AI-Generated Title</label>
      <div class="d-flex align-items-center">
        <input
          type="text"
          id="titleInput"
          name="title"
          class="form-control me-2"
          placeholder="Auto-filled title after generation"
        />
        <button type="button" class="btn btn-generate" id="generateTitleBtn">
          <i class="fas fa-robot me-1"></i> Generate Title
        </button>
        <i class="fas fa-question-circle info-icon ms-2" data-bs-toggle="tooltip" title="e.g., Apartment in Paliometocho. Based on inputs above."></i>
      </div>
    </div>

    <!-- AI Description -->
    <div class="mb-3">
      <label for="propertyDescriptionInput" class="form-label">AI-Generated Property Description</label>
      <div class="d-flex align-items-center">
        <textarea id="propertyDescriptionInput" name="property_description" class="form-control me-2" rows="4" placeholder="AI will generate this once enough info is provided"></textarea>
        <button type="button" class="btn btn-generate" id="generateDescBtn">
          <i class="fas fa-robot me-1"></i> Generate Description
        </button>
        <i class="fas fa-question-circle info-icon ms-2" data-bs-toggle="tooltip" title="e.g., Spacious modern villa with garden & covered parking."></i>
      </div>
    </div>
  </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
  const titleInput = document.getElementById('titleInput');
  const descInput = document.getElementById('propertyDescriptionInput');
  const generateTitleBtn = document.getElementById('generateTitleBtn');
  const generateDescBtn = document.getElementById('generateDescBtn');

  let userEditedTitle = false;
  let userEditedDesc = false;

  titleInput.addEventListener('input', () => userEditedTitle = true);
  descInput.addEventListener('input', () => userEditedDesc = true);

  function getField(id) {
    const el = document.getElementById(id);
    return el ? (el.value ?? '').toString().trim() : '';
  }

  // Prefer form selects when present
  function getTown() {
    const townSel = document.getElementById('town');
    if (townSel && townSel.value.trim()) return townSel.value.trim();
    return extractTownFromMap(); // fallback
  }

  // Prefer Region select; else, parse from map address
  function getRegion() {
    const regionSel = document.getElementById('region');
    if (regionSel && regionSel.value.trim()) return regionSel.value.trim();

    const addr = getField('map_address').toLowerCase();
    if (!addr) return '';

    // canonical regions
    const REGIONS = ['Paphos','Limassol','Nicosia','Larnaca','Famagusta','Kyrenia'];
    for (const r of REGIONS) {
      const rLower = r.toLowerCase();
      if (addr.includes(rLower + ' district') || addr.includes(rLower)) {
        return r;
      }
    }
    return '';
  }

  // Fallback town extraction from map address (heuristic)
  function extractTownFromMap() {
    const address = getField('map_address');
    if (!address) return '';
    const parts = address.split(',').map(p => p.trim());

    // pick first non-empty, non-numeric that isn't a region/district/country word
    const blacklist = ['district','cyprus','province'];
    for (let i = 0; i < parts.length; i++) {
      const part = parts[i];
      if (!part) continue;
      if (/^\d+$/.test(part)) continue;
      const lc = part.toLowerCase();
      if (blacklist.some(w => lc.includes(w))) continue;
      return part;
    }
    return '';
  }

  function getOrdinal(n) {
    const s = ["th", "st", "nd", "rd"];
    const v = parseInt(n, 10) % 100;
    return s[(v - 20) % 10] || s[v] || s[0];
  }

  function generateTitle() {
    const type   = getField('property_type') || getField('proptype') || getField('PropertyType') || ''; // a few safety fallbacks
    const town   = getTown();
    const region = getRegion();

    if (!type) return;

    let generated = '';
    if (town && region)       generated = `${type} in ${town}, ${region}`;
    else if (town)            generated = `${type} in ${town}`;
    else if (region)          generated = `${type} in ${region}`;
    else                      generated = `${type}`;

    if (!userEditedTitle && generated) titleInput.value = generated;
  }

  function generateDescription() {
  const type = getField('property_type');
  const bedrooms = getField('bedrooms');
  const bathrooms = getField('bathrooms');
  const town = getTown();
  const region = getRegion();
  const covered = getField('covered');
  const garden = getField('garden');
  const pool = getField('pool');
  const furnished = getField('furnished');
  const views = getField('views') || getField('view');
  const status = getField('status');
  const vat = getField('vat');

  // --- Overview paragraph ---
  let desc = `Discover this exceptional ${type || 'property'} located in ${town ? town + ', ' : ''}${region || 'Cyprus'}, offering an ideal blend of comfort and Mediterranean charm.`;

  // --- Details paragraph ---
  const details = [];
  if (bedrooms) details.push(`${bedrooms} spacious bedrooms`);
  if (bathrooms) details.push(`${bathrooms} modern bathrooms`);
  if (covered) details.push(`a covered area of approximately ${covered} m²`);
  if (garden) details.push(`a private garden`);
  if (pool) details.push(`a swimming pool`);
  if (views) details.push(`breathtaking ${views.toLowerCase()} views`);
  if (details.length) desc += ` This home features ${details.join(', ')}.`;

  // --- Lifestyle paragraph ---
  desc += ` Situated in a peaceful and desirable neighborhood, the property provides easy access to local amenities, beaches, and transport links.`;
  if (region.toLowerCase().includes('paphos')) {
    desc += ` Within a short drive, you'll find the renowned Coral Bay and the scenic Akamas Peninsula, known for its preserved natural beauty.`;
  } else if (region.toLowerCase().includes('limassol')) {
    desc += ` Enjoy proximity to Limassol’s vibrant marina, beaches, and upscale dining venues.`;
  } else if (region.toLowerCase().includes('nicosia')) {
    desc += ` Conveniently located near schools, shops, and major city attractions in Nicosia.`;
  } else if (region.toLowerCase().includes('larnaca')) {
    desc += ` Minutes away from Larnaca’s promenade and blue-flag beaches, offering an ideal coastal lifestyle.`;
  }

  // --- Optional remarks ---
  if (status && status.toLowerCase().includes('resale')) {
    desc += `\n\n**NO VAT – this is a resale property!**`;
  }
  desc += `\n\nThis property combines luxury, functionality, and the timeless appeal of Mediterranean living.`;

  // --- Specifications section ---
  let specs = [];
  if (type) specs.push(`${type}`);
  if (bedrooms) specs.push(`${bedrooms}-Bedroom`);
  if (bathrooms) specs.push(`${bathrooms}-Bathroom`);
  if (views) specs.push(`${views} Views`);
  if (pool) specs.push(`Private Pool`);
  if (garden) specs.push(`Private Garden`);
  if (furnished && furnished !== '-') specs.push(`Furnished: ${furnished}`);
  if (covered) specs.push(`Covered Area: ${covered} m²`);
  if (vat && vat !== '-') specs.push(`VAT: ${vat}`);
  if (region) specs.push(region);

  if (specs.length) {
    desc += `\n\n**Specifications:**\n` + specs.map(s => `• ${s}`).join('\n');
  }

  // --- Write to textarea if user didn’t manually edit ---
  if (!userEditedDesc) descInput.value = desc.trim();
}


  // Manual triggers
  generateTitleBtn.addEventListener('click', () => { userEditedTitle = false; generateTitle(); });
  generateDescBtn.addEventListener('click', () => { userEditedDesc  = false; generateDescription(); });

  // Auto-generate on load
  generateTitle();
  generateDescription();
});
</script>




