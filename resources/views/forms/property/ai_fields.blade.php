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
    return el ? el.value.trim() : '';
  }

  function extractTownFromMap() {
    const address = getField('map_address');
    if (!address) return '';

    // Try to find a match like: Town, Region, Cyprus
    const parts = address.split(',').map(p => p.trim());

    // Heuristic: get 1st non-numeric part before "District" or "Cyprus"
    for (let i = 0; i < parts.length; i++) {
      const part = parts[i];
      if (
        part &&
        !/^\d+$/.test(part) &&
        !part.toLowerCase().includes('district') &&
        !part.toLowerCase().includes('cyprus') &&
        !part.toLowerCase().includes('province')
      ) {
        return part;
      }
    }

    return '';
  }

  function getOrdinal(n) {
    const s = ["th", "st", "nd", "rd"];
    const v = parseInt(n) % 100;
    return s[(v - 20) % 10] || s[v] || s[0];
  }

  function generateTitle() {
    const type = getField('property_type');
    const town = extractTownFromMap();

    if (!type || !town) return;

    const generated = `${type} in ${town}`;
    if (!userEditedTitle) titleInput.value = generated;
  }

  function generateDescription() {
    const town = extractTownFromMap();
    const bedrooms = getField('bedrooms');
    const bathrooms = getField('bathrooms');
    const garden = getField('garden');
    const covered = getField('covered');
    const floor = getField('floor');

    let desc = `This ${bedrooms}-bedroom, ${bathrooms}-bathroom property in ${town} offers`;

    if (garden) desc += ` a ${garden}m² garden,`;
    if (covered) desc += ` ${covered}m² of covered area,`;
    if (floor) desc += ` located on the ${floor}${getOrdinal(floor)} floor,`;

    desc += ` and is ideal for comfortable living.`;

    if (!userEditedDesc) descInput.value = desc;
  }

  ['property_type', 'bedrooms', 'bathrooms', 'garden', 'covered', 'floor', 'map_address'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('change', () => {
      generateTitle();
      generateDescription();
    });
  });

  // Manual triggers
  generateTitleBtn.addEventListener('click', () => {
    userEditedTitle = false;
    generateTitle();
  });

  generateDescBtn.addEventListener('click', () => {
    userEditedDesc = false;
    generateDescription();
  });

  // Auto-generate on load
  generateTitle();
  generateDescription();
});
</script>



