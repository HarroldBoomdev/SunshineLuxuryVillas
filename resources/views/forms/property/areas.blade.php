<div class="card p-3">
  <h4>Areas</h4>
  <div class="row g-3">
    <div class="col-md-6">
      @php
        $areaLeft = [
          ['id' => 'covered', 'name' => 'covered', 'label' => 'Covered m²'],
          ['id' => 'attic', 'name' => 'attic', 'label' => 'Attic m²'],
          ['id' => 'coveredVeranda', 'name' => 'coveredVeranda', 'label' => 'Covered Veranda m²'],
          ['id' => 'coveredParking', 'name' => 'coveredParking', 'label' => 'Covered Parking m²'],
          ['id' => 'courtyard', 'name' => 'courtyard', 'label' => 'Courtyard m²']
        ];
      @endphp

      @foreach ($areaLeft as $field)
        <div class="form-group">
          <label for="{{ $field['id'] }}">{{ $field['label'] }}</label>
          <input type="text" id="{{ $field['id'] }}" name="{{ $field['name'] }}" class="form-control">
        </div>
      @endforeach
    </div>

    <div class="col-md-6">
      @php
        $areaRight = [
          ['id' => 'plot', 'name' => 'plot', 'label' => 'Plot m²'],
          ['id' => 'roofGarden', 'name' => 'roofGarden', 'label' => 'Roof Garden m²'],
          ['id' => 'uncoveredVeranda', 'name' => 'uncoveredVeranda', 'label' => 'Uncovered Veranda m²'],
          ['id' => 'basement', 'name' => 'basement', 'label' => 'Basement m²'],
          ['id' => 'garden', 'name' => 'garden', 'label' => 'Garden m²']
        ];
      @endphp

      @foreach ($areaRight as $field)
        <div class="form-group">
          <label for="{{ $field['id'] }}">{{ $field['label'] }}</label>
          <input type="text" id="{{ $field['id'] }}" name="{{ $field['name'] }}" class="form-control">
        </div>
      @endforeach
    </div>
  </div>
</div>
