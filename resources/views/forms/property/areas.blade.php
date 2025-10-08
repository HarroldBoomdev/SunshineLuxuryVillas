<div class="card p-3">
  <h4>Areas</h4>

  @php
      use Illuminate\Support\Str;

      // Fallback helper so this partial works in both create & edit
      $p = $property ?? null;
      if (!isset($get) || !is_callable($get)) {
          $get = function (string $field, $default = '') use ($p) {
              $ov = old($field);
              if (!is_null($ov)) return $ov;

              if ($p) {
                  if (isset($p->{$field})) return $p->{$field};
                  $snake = Str::snake($field);
                  if (isset($p->{$snake})) return $p->{$snake};
              }
              return $default;
          };
      }

      $areaLeft = [
          ['id' => 'covered',           'name' => 'covered',           'label' => 'Covered m²'],
          ['id' => 'attic',             'name' => 'attic',             'label' => 'Attic m²'],
          ['id' => 'coveredVeranda',    'name' => 'coveredVeranda',    'label' => 'Covered Veranda m²'],
          ['id' => 'coveredParking',    'name' => 'coveredParking',    'label' => 'Covered Parking m²'],
          ['id' => 'courtyard',         'name' => 'courtyard',         'label' => 'Courtyard m²'],
      ];

      $areaRight = [
          ['id' => 'plot',              'name' => 'plot',              'label' => 'Plot m²'],
          ['id' => 'roofGarden',        'name' => 'roofGarden',        'label' => 'Roof Garden m²'],
          ['id' => 'uncoveredVeranda',  'name' => 'uncoveredVeranda',  'label' => 'Uncovered Veranda m²'],
          ['id' => 'basement',          'name' => 'basement',          'label' => 'Basement m²'],
          ['id' => 'garden',            'name' => 'garden',            'label' => 'Garden m²'],
      ];
  @endphp

  <div class="row g-3">
    <div class="col-md-6">
      @foreach ($areaLeft as $field)
        <div class="form-group">
          <label for="{{ $field['id'] }}">{{ $field['label'] }}</label>
          <input
            type="number" step="0.01"
            id="{{ $field['id'] }}"
            name="{{ $field['name'] }}"
            class="form-control"
            value="{{ $get($field['name']) }}"
          >
        </div>
      @endforeach
    </div>

    <div class="col-md-6">
      @foreach ($areaRight as $field)
        <div class="form-group">
          <label for="{{ $field['id'] }}">{{ $field['label'] }}</label>
          <input
            type="number" step="0.01"
            id="{{ $field['id'] }}"
            name="{{ $field['name'] }}"
            class="form-control"
            value="{{ $get($field['name']) }}"
          >
        </div>
      @endforeach
    </div>
  </div>
</div>
