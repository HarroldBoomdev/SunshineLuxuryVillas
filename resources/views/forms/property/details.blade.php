-=
<div class="card p-3">
                    <h4>Details</h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                        @php
                            $fields = [
                                'property_type' => [
                                    'label' => 'Property Type *',
                                    'options' => [
                                        '' => '-', 'Apartment' => 'Apartment', 'Penthouse' => 'Penthouse', 'Bungalow' => 'Bungalow',
                                        'ComProp' => 'Commercial Property', 'Investment' => 'Investment Property',
                                         'Plot' => 'Plot', 'Studio' => 'Studio',
                                        'Townhouse' => 'Townhouse', 'Villa' => 'Villa'
                                    ]
                                ],
                                'floor' => [
                                    'label' => 'Floor',
                                    'options' => [
                                        '' => '-', 'apartment' => 'Apartment', 'penthouse' => 'Penthouse', 'basement' => 'Basement',
                                        'semi-basement' => 'Semi-Basement', 'groundFloor' => 'Ground Floor',
                                        'topFloor' => 'Top Floor',
                                        '1' => '1', '2' => '2'
                                    ]
                                ]
                            ];

                            $numericDropdowns = [
                                'parkingSpaces' => [1, 5],
                                'bedrooms' => [1, 10],
                                'bathrooms' => [1, 5],
                            ];

                            $startYear = 2020;
                            $endYear = (int) date('Y');

                            $yearDropdowns = [
                                'year Construction' => range($startYear, $endYear),
                                'year Renovation'   => range($startYear, $endYear),
                            ];

                            $selectFields = [
                                'furnished' => [
                                    'label' => 'Furnished',
                                    'options' => [
                                        '' => '-', 'fullyFurnished' => 'Fully Furnished',
                                        'partiallyFurnished' => 'Partially Furnished',
                                        'unfurnished' => 'Unfurnished', 'optionalFurnished' => 'Optional Furnished'
                                    ]
                                ]

                            ];
                        @endphp

                        <!-- Property Type -->
                        <div class="form-group">
                            <label for="property_type">{{ $fields['property_type']['label'] }}</label>
                            <select id="property_type" name="property_type" class="form-control">
                                @foreach ($fields['property_type']['options'] as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Floor -->
                        <div class="form-group" id="floors-field" style="display: none;">
                            <label for="floors">Floors</label>
                            <input type="number" id="floors" name="floors" class="form-control" min="1" placeholder="Enter number of floors">
                        </div>


                        {{-- Numeric fields --}}
                        @foreach ($numericDropdowns as $name => [$start, $end])
                            <div class="form-group">
                                <label for="{{ $name }}">{{ ucwords(str_replace('_', ' ', $name)) }}</label>
                                <select id="{{ $name }}" name="{{ $name }}" class="form-control">
                                    @for ($i = $start; $i <= $end; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
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
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach

                        @foreach ($selectFields as $name => $field)
                            <div class="form-group">
                                <label for="{{ $name }}">{{ $field['label'] }}</label>
                                <select id="{{ $name }}" name="{{ $name }}" class="form-control">
                                    @foreach ($field['options'] as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>
                        <div class="col-md-6">
                        @php
                            // Input fields
                            $inputFields = [
                                ['id' => 'reference', 'name' => 'reference', 'label' => 'Reference', 'type' => 'text']
                            ];

                            // Dropdown select fields
                            $dropdownFields = [
                                'status' => [
                                    'label' => 'Status',
                                    'options' => [
                                        '' => '-', 'resale' => 'Resale', 'brandnew' => 'Brand New'
                                    ]
                                ],
                                'basement' => [
                                    'label' => 'Basement',
                                    'options' => ['' => '-', 'yes' => 'Yes', 'no' => 'No']
                                ],
                                'orientation' => [
                                    'label' => 'Orientation',
                                    'options' => [
                                        '' => '-', 'east' => 'East', 'eastwest' => 'East West',
                                        'eastmeridian' => 'East Meridian', 'north' => 'North',
                                        'northeast' => 'North East', 'northwest' => 'North West',
                                        'west' => 'West'
                                    ]
                                ],
                                'energyEfficiency' => [
                                    'label' => 'Energy Efficiency Rating',
                                    'options' => [
                                        '' => '-', 'exempt' => 'Exempt', 'certExcepted' => 'Certificate Excepted', 'A' => 'A',
                                        'B+' => 'B+', 'C' => 'C', 'D' => 'D', 'E' => 'E', 'F' => 'F', 'G' => 'G', 'H' => 'H'
                                    ]
                                ],
                                'vat' => [
                                    'label' => 'VAT',
                                    'options' => [
                                        '' => '-', 'yes' => 'Yes'
                                    ]
                                ]
                            ];

                            $checkboxLabels = [
                                'Luxury', 'Pet Friendly', 'Bank', 'KML', 'Residential Sales',
                                'Repossession', 'High Rental Yield', 'Prestige', 'Exclusive',
                                'Reserved', 'Sold', 'Reduced'
                            ];
                            @endphp

                            {{-- Input fields --}}
                            @foreach ($inputFields as $field)
                            <div class="form-group">
                                <label for="{{ $field['id'] }}">{{ $field['label'] }}</label>
                                <input type="{{ $field['type'] }}" id="{{ $field['id'] }}" name="{{ $field['name'] }}" class="form-control">
                            </div>
                            @endforeach

                            {{-- Labels Checkbox Group --}}
                            <div class="form-group">
                                <label for="labels">Labels</label>
                                <div class="dropdown">
                                    <div class="form-control dropdown-toggle" id="proplabelsField" data-bs-toggle="dropdown" aria-expanded="false" readonly>
                                        <!-- Selected items will appear here -->
                                    </div>
                                    <div class="dropdown-menu p-3" style="width: 100%;">
                                        @foreach ($checkboxLabels as $label)
                                        <div class="form-check">
                                            <input type="checkbox" class="proplabelsField-checkbox" id="{{ Str::slug($label) }}" name="labels[]" value="{{ $label }}">
                                            <label class="form-check-label" for="{{ Str::slug($label) }}">{{ $label }}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- Other Dropdown select fields --}}
                            @foreach ($dropdownFields as $name => $field)
                                @if ($name != 'managing_agent')
                                <div class="form-group">
                                    <label for="{{ $name }}">{{ $field['label'] }}</label>
                                    <select id="{{ $name }}" name="{{ $name }}" class="form-control">
                                        @foreach ($field['options'] as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                            @endforeach



                            <div class="form-group">
                                <label for="communalCharge">Communal Charge</label>
                                <div class="input-group">
                                    <span class="input-group-text">€</span>
                                    <input type="text" id="price" name="price" class="form-control" placeholder="Enter amount in EUR" oninput="this.value = this.value.replace(/[^0-9.]/g, '');">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="price">Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">€</span> <!-- EUR symbol -->
                                    <input
                                        type="text"
                                        id="price"
                                        name="price"
                                        class="form-control"
                                        placeholder="Enter amount in EUR"
                                        oninput="this.value = this.value.replace(/[^0-9.]/g, '');"
                                    >
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
