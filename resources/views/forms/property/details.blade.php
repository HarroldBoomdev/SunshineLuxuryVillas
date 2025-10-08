
<div class="card p-3">
  <h4>Details</h4>
  <div class="row g-3">
    <div class="col-md-6">
      @php
        use Illuminate\Support\Str;

        $p = $property ?? null;

        // Read a value from: old() -> property->field OR snake_case OR camelCase
        $get = function (string $field, $default = '') use ($p) {
            $ov = old($field);
            if (!is_null($ov)) return $ov;

            if (!$p) return $default;

            $candidates = array_unique([
                $field,                  // as-is
                Str::snake($field),      // snake_case
                Str::camel($field),      // camelCase
            ]);

            foreach ($candidates as $key) {
                if (isset($p->{$key}) && $p->{$key} !== '' && $p->{$key} !== null) {
                    return $p->{$key};
                }
            }
            return $default;
        };

        // Robust selected(): case-insensitive AND ignore spaces/underscores/hyphens
        $isSelected = function (string $field, $option) use ($get) {
            $curr = (string)$get($field, '');
            $norm = fn($v) => strtolower(preg_replace('/[\s_\-]+/', '', (string)$v));
            return $norm($curr) === $norm($option);
        };
    @endphp

    @php
        $labelsRaw = function_exists('old') ? old('labels', $get('labels', [])) : $get('labels', []);

        if (is_string($labelsRaw)) {
            $decoded = json_decode($labelsRaw, true);
            $labelsArray = is_array($decoded)
                ? $decoded
                : array_filter(array_map('trim', explode(',', $labelsRaw)));
        } elseif (is_array($labelsRaw)) {
            $labelsArray = $labelsRaw;
        } else {
            $labelsArray = [];
        }

        // This is what the loop will use
        $selectedLabels = array_map('strval', $labelsArray);
    @endphp


      @php
        $fields = [
          'property_type' => [
            'label' => 'Property Type *',
            'options' => [
              '' => '-',
              'Apartment' => 'Apartment',
              'Penthouse' => 'Penthouse',
              'Bungalow' => 'Bungalow',
              'Commercial Property' => 'Commercial Property',
              'Investment Property' => 'Investment Property',
              'Plot' => 'Plot',
              'Studio' => 'Studio',
              'Townhouse' => 'Townhouse',
              'Villa' => 'Villa',
            ],
          ],
          'floor' => [
            'label' => 'Floor',
            'options' => [
              '' => '-',
              'apartment' => 'Apartment',
              'penthouse' => 'Penthouse',
              'basement' => 'Basement',
              'semi-basement' => 'Semi-Basement',
              'groundFloor' => 'Ground Floor',
              'topFloor' => 'Top Floor',
              '1' => '1', '2' => '2',
            ],
          ],
        ];

        $numericDropdowns = [
          'parkingSpaces' => [1, 5],
          'bedrooms'      => [1, 10],
          'bathrooms'     => [1, 5],
        ];

        $startYear = 1900;
        $endYear   = (int) date('Y');

        // ✅ use snake_case keys matching DB fields
        $yearDropdowns = [
          'year_construction' => range($startYear, $endYear),
          'year_renovation'   => range($startYear, $endYear),
        ];

        $selectFields = [
          'furnished' => [
            'label' => 'Furnished',
            'options' => [
              '' => '-',
              'Fully Furnished'      => 'Fully Furnished',
              'Partially Furnished'  => 'Partially Furnished',
              'Unfurnished'          => 'Unfurnished',
              'Optional Furnished'   => 'Optional Furnished',
            ],
          ],
        ];
      @endphp

      <!-- Property Type -->
      <div class="form-group">
        <label for="property_type">{{ $fields['property_type']['label'] }}</label>
        <select id="property_type" name="property_type" class="form-control">
          @foreach ($fields['property_type']['options'] as $value => $label)
            <option value="{{ $value }}" {{ $isSelected('property_type', $value) ? 'selected' : '' }}>
              {{ $label }}
            </option>
          @endforeach
        </select>
      </div>

      <!-- Floors (shown conditionally via JS) -->
      <div class="form-group" id="floors-field" style="display: none;">
        <label for="floors">Floors</label>
        <input type="number" id="floors" name="floors" class="form-control"
               min="1" placeholder="Enter number of floors"
               value="{{ $get('floors') }}">
      </div>

      {{-- Numeric fields --}}
      @foreach ($numericDropdowns as $name => [$start, $end])
        <div class="form-group">
          <label for="{{ $name }}">{{ ucwords(str_replace('_', ' ', Str::snake($name))) }}</label>
          <select id="{{ $name }}" name="{{ $name }}" class="form-control">
            @for ($i = $start; $i <= $end; $i++)
              <option value="{{ $i }}" {{ (string)$get($name) === (string)$i ? 'selected' : '' }}>
                {{ $i }}
              </option>
            @endfor
          </select>
        </div>
      @endforeach

      {{-- Year fields with N/A --}}
      @foreach ($yearDropdowns as $name => $years)
        <div class="form-group">
          <label for="{{ $name }}">{{ ucwords(str_replace('_', ' ', $name)) }}</label>
          <select id="{{ $name }}" name="{{ $name }}" class="form-control">
            <option value="">N/A</option>
            @foreach ($years as $year)
              <option value="{{ $year }}" {{ (string)$get($name) === (string)$year ? 'selected' : '' }}>
                {{ $year }}
              </option>
            @endforeach
          </select>
        </div>
      @endforeach

      @foreach ($selectFields as $name => $field)
        <div class="form-group">
          <label for="{{ $name }}">{{ $field['label'] }}</label>
          <select id="{{ $name }}" name="{{ $name }}" class="form-control">
            @foreach ($field['options'] as $value => $label)
              <option value="{{ $value }}" {{ $isSelected($name, $value) ? 'selected' : '' }}>
                {{ $label }}
              </option>
            @endforeach
          </select>
        </div>
      @endforeach
    </div>

    <div class="col-md-6">
      @php
        // Text inputs
        $inputFields = [
          ['id' => 'reference', 'name' => 'reference', 'label' => 'Reference', 'type' => 'text'],
        ];

        // Dropdown select fields
        $dropdownFields = [
          'status' => [
            'label' => 'Status',
            'options' => [
              '' => '-',
              'Resale'   => 'Resale',
              'Brand New'=> 'Brand New',
            ],
          ],
          'basement' => [
            'label' => 'Basement',
            'options' => [
              '' => '-',
              'Yes' => 'Yes',
              'No'  => 'No',
            ],
          ],
          'orientation' => [
            'label' => 'Orientation',
            'options' => [
              '' => '-',
              'East' => 'East',
              'East West' => 'East West',
              'East Meridian' => 'East Meridian',
              'North' => 'North',
              'North East' => 'North East',
              'North West' => 'North West',
              'West' => 'West',
            ],
          ],
          'energyEfficiency' => [
            'label' => 'Energy Efficiency Rating',
            'options' => [
              '' => '-',
              'Exempt' => 'Exempt',
              'Certificate Excepted' => 'Certificate Excepted',
              'A' => 'A','B+' => 'B+','C' => 'C','D' => 'D','E' => 'E','F' => 'F','G' => 'G','H' => 'H',
            ],
          ],
          'vat' => [
            'label' => 'VAT',
            'options' => [
              '' => '-',
              'Yes' => 'Yes',
              'No'  => 'No',
            ],
          ],
        ];

        $checkboxLabels = [
          'Luxury','Pet Friendly','Bank','KML','Residential Sales',
          'Repossession','High Rental Yield','Prestige','Exclusive',
          'Reserved','Sold','Reduced',
        ];
      @endphp

      {{-- Text inputs --}}
      @foreach ($inputFields as $field)
        <div class="form-group">
          <label for="{{ $field['id'] }}">{{ $field['label'] }}</label>
          <input type="{{ $field['type'] }}"
                 id="{{ $field['id'] }}"
                 name="{{ $field['name'] }}"
                 class="form-control"
                 value="{{ $get($field['name']) }}">
        </div>
      @endforeach

      {{-- Labels Checkbox Group --}}
      <div class="form-group">
        <label for="labels">Labels</label>
        <div class="dropdown">
          <div class="form-control dropdown-toggle" id="proplabelsField"
               data-bs-toggle="dropdown" aria-expanded="false" readonly></div>
          <div class="dropdown-menu p-3" style="width: 100%;">
            @foreach ($checkboxLabels as $label)
              @php $slug = Str::slug($label); @endphp
              <div class="form-check">
                <input type="checkbox" class="proplabelsField-checkbox"
                       id="{{ $slug }}" name="labels[]" value="{{ $label }}"
                       {{ in_array($label, $selectedLabels, true) ? 'checked' : '' }}>
                <label class="form-check-label" for="{{ $slug }}">{{ $label }}</label>
              </div>
            @endforeach
          </div>
        </div>
      </div>

      {{-- Other Dropdown select fields --}}
      @foreach ($dropdownFields as $name => $field)
        <div class="form-group">
          <label for="{{ $name }}">{{ $field['label'] }}</label>
          <select id="{{ $name }}" name="{{ $name }}" class="form-control">
            @foreach ($field['options'] as $value => $label)
              <option value="{{ $value }}" {{ $isSelected($name, $value) ? 'selected' : '' }}>
                {{ $label }}
              </option>
            @endforeach
          </select>
        </div>
      @endforeach

      {{-- Communal Charge --}}
      <div class="form-group">
        <label for="communal_charge">Communal Charge</label>
        <div class="input-group">
          <span class="input-group-text">€</span>
          <input type="text" id="communal_charge" name="communal_charge" class="form-control"
                 value="{{ $get('communal_charge') }}"
                 placeholder="Enter amount in EUR"
                 oninput="this.value = this.value.replace(/[^0-9.]/g, '');">
        </div>
      </div>

      {{-- Price --}}
      <div class="form-group">
        <label for="price">Price</label>
        <div class="input-group">
          <span class="input-group-text">€</span>
          <input type="text" id="price" name="price" class="form-control"
                 value="{{ $get('price') }}"
                 placeholder="Enter amount in EUR"
                 oninput="this.value = this.value.replace(/[^0-9.]/g, '');">
        </div>
      </div>

    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const propertyTypeSelect = document.getElementById('property_type');
  const floorsField = document.getElementById('floors-field');

  function toggleFloorsField() {
    const selected = propertyTypeSelect.value;
    if (selected === 'Apartment' || selected === 'Penthouse') {
      floorsField.style.display = 'block';
    } else {
      floorsField.style.display = 'none';
    }
  }

  propertyTypeSelect.addEventListener('change', toggleFloorsField);
  toggleFloorsField(); // Run on load
});
</script>
