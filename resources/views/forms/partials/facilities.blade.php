{{-- ========================= VIDEOS / VIRTUAL TOUR ========================= --}}
<div class="card shadow-sm border-0 mt-4 p-3">

    <h5>Videos / Virtual Tour</h5>
    <p>If you are unsure on how to get the YouTube embedded link,
        <a href="#" target="_blank" class="text-warning">click this link for help</a>.
    </p>

    <div class="mb-3">
        <label>YouTube Embedded Link 1:</label>
        <input type="text" name="youtube1" class="form-control">
    </div>

    <div class="mb-3">
        <label>YouTube Embedded Link 2:</label>
        <input type="text" name="youtube2" class="form-control">
    </div>

    <div class="mb-3">
        <button type="button" class="btn btn-warning">Save Changes</button>
    </div>

    <div class="mb-3">
        <label>Virtual Tour Link:</label>
        <input type="text" name="virtual_tour" class="form-control">
    </div>

    <button type="button" class="btn btn-warning">Save Virtual Tour Link</button>

</div>


{{-- ========================= DOCUMENTS UPLOAD ========================= --}}
<div class="card shadow-sm border-0 mt-4 p-3">

    <h5>Documents</h5>

    <div class="mb-3">
        <label>Upload File:</label>
        <input type="file" name="document" class="form-control">
    </div>

    <button type="button" class="btn btn-warning">Upload</button>

</div>


{{-- ========================= FACILITIES SECTION ========================= --}}
<div class="card p-3 mt-4">

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
            <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start"
                    type="button"
                    id="labelsDropdownButton"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">
                Select Labels
            </button>

            <ul class="dropdown-menu p-3"
                style="max-height: 200px; overflow-y: auto; width: 100%;"
                aria-labelledby="labelsDropdownButton">

                @foreach ($facilityCategories as $category => $facilities)
                    <li><h4 class="mt-2">{{ $category }}</h4></li>

                    @foreach ($facilities as $id => $label)
                        <li>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       id="{{ $id }}" name="labels[]" value="{{ $label }}">
                                <label class="form-check-label" for="{{ $id }}">
                                    {{ $label }}
                                </label>
                            </div>
                        </li>
                    @endforeach

                @endforeach
            </ul>
        </div>
    </div>

</div>
