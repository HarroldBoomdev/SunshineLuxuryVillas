<div class="card p-3">
  @php
    $facilityCategories = [
      'Options' => [
        'centralSystem' => 'Central System',
        'splitSystem' => 'Split System',
        'provision' => 'Provision',
        'elevator' => 'Elevator',
        'gatedComplex' => 'Gated Complex',
        'childrenPlayground' => 'Children Playground',
        'gym' => 'Gym'
      ],
      'Heating' => [
        'central' => 'Central',
        'centralIndependent' => 'Central, Independent',
        'centralElectric' => 'Central, Electric',
        'hsplitSystem' => 'Split System',
        'underfloor' => 'Underfloor',
        'hprovision' => 'Provision',
        'hgym' => 'Gym'
      ],
      'Indoor Pool' => [
        'private' => 'Private',
        'privateOverflow' => 'Private, Overflow',
        'privateHealed' => 'Private, Healed',
        'privateSaltwater' => 'Private, Salt Water',
        'communal' => 'Communal',
        'communalOverflow' => 'Communal, Overflow',
        'communalHealed' => 'Communal, Healed',
        'communalSaltwater' => 'Communal, Salt Water'
      ]
    ];
  @endphp

  <div class="form-group">
    <label for="labelsDropdownButton">Facilities</label>
    <div class="dropdown">
      <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button" id="labelsDropdownButton" data-bs-toggle="dropdown" aria-expanded="false">
        Select Labels
      </button>
      <ul class="dropdown-menu p-3" style="max-height: 200px; overflow-y: auto; width: 100%;" aria-labelledby="labelsDropdownButton">
        @foreach ($facilityCategories as $category => $facilities)
          <li><h4>{{ $category }}</h4></li>
          @foreach ($facilities as $id => $label)
            <li>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="{{ $id }}" name="labels[]" value="{{ $label }}">
                <label class="form-check-label" for="{{ $id }}">{{ $label }}</label>
              </div>
            </li>
          @endforeach
        @endforeach
      </ul>
    </div>
  </div>
</div>
