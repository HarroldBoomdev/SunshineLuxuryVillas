@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="container mt-4">
        <form action="{{ route('clients.custom_store') }}" method="POST">
            @csrf

    @include('layouts.newButton')
        <!-- Main Content -->
            <div class="row">
                <!-- Left Column: Details -->
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                            <h1>New Client Details</h1>
                            </div>
                            <div class="col-md-6">
                            </div>


                <div class="row">
                    <div class="card p-3">
                        <h4>Details</h4>
                        <div class="row g-3"> <!-- Internal row with spacing -->
                            <div class="col-md-6">
                            @php
                                $inputs = [
                                    ['id' => 'fname', 'name' => 'fname', 'label' => 'First Name *', 'type' => 'text', 'required' => true],
                                    ['id' => 'mobile', 'name' => 'mobile', 'label' => 'Mobile', 'type' => 'tel'],
                                    ['id' => 'email', 'name' => 'email', 'label' => 'Email', 'type' => 'text'],
                                    ['id' => 'refAgentCon', 'name' => 'refAgentCon', 'label' => 'Referral Agent Contact', 'type' => 'number'],
                                ];

                                $labels = ['Luxury', 'Pet Friendly', 'Bank', 'KML', 'Residential Sales'];
                                $languages = ['English', 'Greek', 'British'];
                            @endphp

                            @foreach($inputs as $field)
                                <div class="form-group">
                                    <label for="{{ $field['id'] }}">{{ $field['label'] }}</label>
                                    <input
                                        type="{{ $field['type'] }}"
                                        id="{{ $field['id'] }}"
                                        name="{{ $field['name'] }}"
                                        class="form-control"
                                        {{ $field['required'] ?? false ? 'required' : '' }}>
                                </div>
                            @endforeach

                            <!-- Labels Dropdown -->
                            <div class="form-group">
                                <label for="labelsDropdownButton">Labels</label>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button" id="labelsDropdownButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        Select Labels
                                    </button>
                                    <ul class="dropdown-menu p-3" style="max-height: 200px; overflow-y: auto; width: 100%;" aria-labelledby="labelsDropdownButton">
                                        @foreach($labels as $label)
                                            @php $id = strtolower(str_replace(' ', '', $label)); @endphp
                                            <li>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="{{ $id }}" name="labels[]" value="{{ $label }}">
                                                    <label class="form-check-label" for="{{ $id }}">{{ $label }}</label>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            <!-- Preferred Language -->
                            <div class="form-group">
                                <label for="prefLang">Preferred Language</label>
                                <select id="prefLang" name="prefLang" class="form-control">
                                    @foreach($languages as $lang)
                                        <option value="{{ $lang }}">{{ $lang }}</option>
                                    @endforeach
                                </select>
                            </div>


                            </div>
                            <div class="col-md-6">
                            @php
                                $fields = [
                                    ['id' => 'lname', 'name' => 'lname', 'label' => 'Last Name *', 'type' => 'text'],
                                    ['id' => 'phone', 'name' => 'phone', 'label' => 'Phone', 'type' => 'tel'],
                                    ['id' => 'passportNum', 'name' => 'passportNum', 'label' => 'Passport Number', 'type' => 'number'],
                                ];

                                $dropdowns = [
                                    [
                                        'id' => 'nationality',
                                        'name' => 'nationality',
                                        'label' => 'Nationality',
                                        'options' => ['' => '-'],
                                    ],
                                    [
                                        'id' => 'refAgent',
                                        'name' => 'refAgentt',
                                        'label' => 'Referral Agent *',
                                        'options' => [
                                            '' => '-',
                                            'Thomas Harrison' => 'Thomas Harrison',
                                            // Add more agents here as needed
                                        ],
                                    ]
                                ];
                            @endphp

                            @foreach ($fields as $field)
                                <div class="form-group">
                                    <label for="{{ $field['id'] }}">{{ $field['label'] }}</label>
                                    <input type="{{ $field['type'] }}" id="{{ $field['id'] }}" name="{{ $field['name'] }}" class="form-control">
                                </div>
                            @endforeach

                            @foreach ($dropdowns as $select)
                                <div class="form-group">
                                    <label for="{{ $select['id'] }}">{{ $select['label'] }}</label>
                                    <select id="{{ $select['id'] }}" name="{{ $select['name'] }}" class="form-control">
                                        @foreach ($select['options'] as $value => $text)
                                            <option value="{{ $value }}" {{ $value === '' ? 'disabled selected' : '' }}>{{ $text }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                    <div class="card p-3">
                        <div class="form-group">
                            <label for="labels">Notes</label>
                            <textarea class="form-control notes-area" rows="4" placeholder="Add notes here..."></textarea>
                        </div>
                    </div>
                    <div class="card p-3">
                    <h4>Notifications</h4>
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="emailMatching" name="emailMatching">
                                <label class="form-check-label" for="matchingSystem">Email matching properties automatically</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Buyer & Tenant Profile -->


        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6">
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary mr-2">+ Create</button>
                    </div>
                </div>
                <div class="card p-3">
                <h4>Buyer and Tenant Profile</h4>
                <div class="row g-3">
                    <div class="col-md-6">
                    @php
                        $types = [
                            'Detached House',
                            'Semi-Detached House',
                            'Link-Detached House',
                            'Houses',
                            'House of Character',
                            'Apartment',
                            'Duplex Maisonette',
                            'Farm'
                        ];
                    @endphp

                    <div class="form-group">
                        <label for="typeDropdownButton">Type</label>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button" id="typeDropdownButton" data-bs-toggle="dropdown" aria-expanded="false">
                                Select Types
                            </button>
                            <ul class="dropdown-menu p-3" style="max-height: 200px; overflow-y: auto; width: 100%;" aria-labelledby="typeDropdownButton">
                                @foreach($types as $type)
                                    @php $id = Str::camel(str_replace([' ', '-'], '', $type)); @endphp
                                    <li>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="{{ $id }}" name="labels[]" value="{{ $type }}">
                                            <label class="form-check-label" for="{{ $id }}">{{ $type }}</label>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>




                    <div class="row">
                        @php
                            $roomFields = [
                                'bedroooms' => 'Bedrooms',
                                'bedroooms1' => 'Bathrooms',
                            ];
                        @endphp

                        @foreach ($roomFields as $id => $label)
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="{{ $id }}">{{ $label }}</label>
                                    <select id="{{ $id }}" name="{{ $id }}" class="form-control">
                                        @for ($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        @endforeach
                    </div>


                    @php
                        $poolOptions = [
                            '' => '-',
                            'yes' => 'Yes', 'no' => 'No',
                        ];

                        $directionOptions = [
                            '' => '-',
                            'east' => 'East', 'eastwest' => 'East West', 'eastmeridian' => 'East Meredian',
                            'north' => 'North', 'northeast' => 'North East', 'northwest' => 'North West', 'west' => 'West',
                        ];
                    @endphp

                    <div class="form-group">
                        <label for="pool">Pool</label>
                        <select id="pool" name="pool" class="form-control">
                            @foreach ($poolOptions as $value => $label)
                                <option value="{{ $value }}" {{ $value === '' ? 'disabled selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Only Orientation -->
                    @foreach (['orientation' => 'Orientation'] as $id => $label)
                        <div class="form-group">
                            <label for="{{ $id }}">{{ $label }}</label>
                            <select id="{{ $id }}" name="{{ $id }}" class="form-control">
                                @foreach ($directionOptions as $value => $text)
                                    <option value="{{ $value }}" {{ $value === '' ? 'disabled selected' : '' }}>{{ $text }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach


                    <div class="row">
                        @php
                            $areaFields = [
                                'coveredM' => 'Covered m2',
                                'coveredM1' => 'Plot m2',
                            ];
                        @endphp

                        @foreach ($areaFields as $id => $label)
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="{{ $id }}">{{ $label }}</label>
                                    <input type="number" id="{{ $id }}" name="{{ $id }}" class="form-control">
                                </div>
                            </div>
                        @endforeach
                    </div>


                        @php
                            $constructFields = ['constructstart', 'constructend'];
                        @endphp

                        <div class="row">
                            <h4>Year of Construction</h4>
                            @foreach ($constructFields as $field)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select id="{{ $field }}" name="{{ $field }}" class="form-control">
                                            @for ($i = 1995; $i <= 2025; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @php
                            $floorOptions = range(1, 10);

                            $selectFields = [
                                'ability' => [
                                    'label' => 'Ability to Proceed',
                                    'options' => [
                                        '' => '-',
                                        'Mortgage Required' => 'Mortgage Required',
                                        'Need to sell first' => 'Need to sell first',
                                        'Cash Buyer' => 'Cash Buyer',
                                    ]
                                ],
                                'reasonsForBuying' => [
                                    'label' => 'Reasons for buying',
                                    'options' => [
                                        '' => '-',
                                        'holidayhome' => 'Holiday Home',
                                        'relocation' => 'Relocation',
                                        'investment' => 'Investment',
                                    ]
                                ]
                            ];
                        @endphp

                        <div class="form-group">
                            <label for="floor">Floor</label>
                            <select id="floor" name="floor" class="form-control">
                                @foreach ($floorOptions as $i)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endforeach
                            </select>
                        </div>

                        @foreach ($selectFields as $id => $field)
                            <div class="form-group">
                                <label for="{{ $id }}">{{ $field['label'] }}</label>
                                <select id="{{ $id }}" name="{{ $id }}" class="form-control">
                                    @foreach ($field['options'] as $value => $text)
                                        <option value="{{ $value }}" {{ $value === '' ? 'disabled selected' : '' }}>{{ $text }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach

                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category">Category</label>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button" id="typeDropdownButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    Select Types
                                </button>
                                <ul class="dropdown-menu p-3" style="max-height: 200px; overflow-y: auto; width: 100%;" aria-labelledby="typeDropdownButton">
                                <li>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="resales" name="labels[]" value="Resales">
                                        <label class="form-check-label" for="resales">Resales</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="brandnew" name="labels[]" value="Brandnew">
                                        <label class="form-check-label" for="brandnew">Brandnew</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="both" name="labels[]" value="Both">
                                        <label class="form-check-label" for="both">Both</label>
                                    </div>
                                </li>
                                </ul>
                            </div>
                        </div>
                        @php
                            $areas = ['Cyprus', 'Spain', 'United Kingdom', 'Italy'];

                            $titleDeadOptions = [
                                '' => '-',
                                'yes' => 'Yes',
                                'no' => 'No',
                                'inprogress' => 'In Progress',
                                'land' => 'Land',
                            ];
                        @endphp

                        <div class="form-group">
                            <label for="area">Area</label>
                            <div class="dropdown">
                                <div class="form-control dropdown-toggle" id="areaField" data-bs-toggle="dropdown" aria-expanded="false">
                                    Select Labels
                                </div>
                                <div class="dropdown-menu p-3 w-100">
                                    @foreach ($areas as $area)
                                        @php $id = strtolower(str_replace(' ', '', $area)); @endphp
                                        <div class="form-check">
                                            <input type="checkbox" class="area-checkbox" id="{{ $id }}" name="area[]" value="{{ $area }}">
                                            <label class="form-check-label" for="{{ $id }}">{{ $area }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="titleDead">Title Dead</label>
                            <select id="titleDead" name="titleDead" class="form-control">
                                @foreach ($titleDeadOptions as $value => $label)
                                    <option value="{{ $value }}" {{ $value === '' ? 'disabled selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <h4>Purchase Budget</h4>
                            @php
                                $budgetFields = [
                                    'purchaseMin' => 'Enter min',
                                    'purchaseMax' => 'Enter max',
                                ];
                            @endphp

                            @foreach ($budgetFields as $id => $placeholder)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="number" id="{{ $id }}" name="{{ $id }}" class="form-control" placeholder="{{ $placeholder }}">
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @php
                            $furnishedOptions = [
                                '' => '-',
                                'east' => 'East', 'eastwest' => 'East West', 'eastmeridian' => 'East Meredian',
                                'north' => 'North', 'northeast' => 'North East', 'northwest' => 'North West', 'west' => 'West'
                            ];

                            $parkingOptions = range(1, 5);

                            $timeframeOptions = [
                                '' => '-',
                                'Immediately' => 'Immediately',
                                'Within 6 months' => 'Within 6 months',
                                'Within 1 year' => 'Within 1 year',
                                'Over 1 year' => 'Over 1 year'
                            ];

                            $featureCategories = [
                                'Options' => [
                                    'centralSystem' => 'Central System', 'splitSystem' => 'Split System', 'provision' => 'Provision',
                                    'elevator' => 'Elevator', 'gatedComplex' => 'Gated Complex', 'childrenPlayground' => 'Children Playground',
                                    'gym' => 'Gym'
                                ],
                                'Heating' => [
                                    'central' => 'Central', 'centralIndependent' => 'Central, Independent', 'centralElectric' => 'Central, Electric',
                                    'hsplitSystem' => 'Split System', 'underfloor' => 'Underfloor', 'hprovision' => 'Provision', 'hgym' => 'Gym'
                                ],
                                'Indoor Pool' => [
                                    'private' => 'Private', 'privateOverflow' => 'Private, Overflow', 'privateHealed' => 'Private, Healed',
                                    'privateSaltwater' => 'Private, Salt Water', 'communal' => 'Communal', 'communalOverflow' => 'Communal, Overflow',
                                    'communalHealed' => 'Communal, Healed', 'communalSaltwater' => 'Communal, Salt Water'
                                ]
                            ];
                        @endphp

                        <!-- Furnished -->
                        <div class="form-group">
                            <label for="furnished">Furnished</label>
                            <select id="furnished" name="furnished" class="form-control">
                                @foreach ($furnishedOptions as $value => $label)
                                    <option value="{{ $value }}" {{ $value === '' ? 'disabled selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Parking -->
                        <div class="form-group">
                            <label for="parking">Parking</label>
                            <select id="parking" name="parking" class="form-control">
                                @foreach ($parkingOptions as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Features Dropdown -->
                        <div class="form-group">
                            <label for="featuresDropdownButton">Features</label>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button" id="featuresDropdownButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    Select Labels
                                </button>
                                <ul class="dropdown-menu p-3" style="max-height: 200px; overflow-y: auto; width: 100%;" aria-labelledby="featuresDropdownButton">
                                    @foreach ($featureCategories as $category => $features)
                                        <li><h4>{{ $category }}</h4></li>
                                        @foreach ($features as $id => $label)
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

                        <!-- Time Frame -->
                        <div class="form-group">
                            <label for="timeframe">Time frame</label>
                            <select id="timeframe" name="timeframe" class="form-control">
                                @foreach ($timeframeOptions as $value => $label)
                                    <option value="{{ $value }}" {{ $value === '' ? 'disabled selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Matching System -->
                        <div class="row">
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="matchingSystem" name="matchingSystem">
                                    <label class="form-check-label" for="matchingSystem">Matching system enabled</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">

            </div>


                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    console.log("✅ DOM Loaded!");

    /** 🌎 Populate Nationality Dropdown */
    function populateNationalities() {
        const nationalitySelect = document.getElementById("nationality");

        if (!nationalitySelect) {
            console.error("❌ Nationality select element not found! Ensure <select id='nationality'> exists.");
            return;
        }

        console.log("🌍 Populating nationality list...");

        const nationalities = [
            "Afghan", "Albanian", "Algerian", "Andorran", "Angolan", "Argentine", "Armenian",
            "Australian", "Austrian", "Azerbaijani", "Bahamian", "Bahraini", "Bangladeshi",
            "Barbadian", "Belarusian", "Belgian", "Belizean", "Beninese", "Bhutanese",
            "Bolivian", "Bosnian", "Botswanan", "Brazilian", "British", "Bruneian", "Bulgarian",
            "Burkinabe", "Burmese", "Burundian", "Cambodian", "Cameroonian", "Canadian",
            "Cape Verdean", "Central African", "Chadian", "Chilean", "Chinese", "Colombian",
            "Comoran", "Congolese", "Costa Rican", "Croatian", "Cuban", "Cypriot", "Czech",
            "Danish", "Djiboutian", "Dominican", "Dutch", "Ecuadorian", "Egyptian", "Emirati",
            "Equatorial Guinean", "Eritrean", "Estonian", "Ethiopian", "Fijian", "Filipino",
            "Finnish", "French", "Gabonese", "Gambian", "Georgian", "German", "Ghanaian",
            "Greek", "Grenadian", "Guatemalan", "Guinean", "Guinea-Bissauan", "Guyanese",
            "Haitian", "Honduran", "Hungarian", "Icelandic", "Indian", "Indonesian",
            "Iranian", "Iraqi", "Irish", "Israeli", "Italian", "Jamaican", "Japanese",
            "Jordanian", "Kazakh", "Kenyan", "Kiribati", "Kuwaiti", "Kyrgyz", "Laotian",
            "Latvian", "Lebanese", "Liberian", "Libyan", "Liechtensteiner", "Lithuanian",
            "Luxembourgish", "Malagasy", "Malawian", "Malaysian", "Maldivian", "Malian",
            "Maltese", "Marshallese", "Mauritanian", "Mauritian", "Mexican", "Micronesian",
            "Moldovan", "Monacan", "Mongolian", "Montenegrin", "Moroccan", "Mozambican",
            "Namibian", "Nauruan", "Nepalese", "New Zealander", "Nicaraguan", "Nigerien",
            "Nigerian", "North Korean", "Norwegian", "Omani", "Pakistani", "Palauan",
            "Palestinian", "Panamanian", "Papua New Guinean", "Paraguayan", "Peruvian",
            "Polish", "Portuguese", "Qatari", "Romanian", "Russian", "Rwandan",
            "Saint Lucian", "Salvadoran", "Samoan", "San Marinese", "Sao Tomean", "Saudi",
            "Scottish", "Senegalese", "Serbian", "Seychellois", "Sierra Leonean",
            "Singaporean", "Slovak", "Slovenian", "Solomon Islander", "Somali", "South African",
            "South Korean", "Spanish", "Sri Lankan", "Sudanese", "Surinamer", "Swazi",
            "Swedish", "Swiss", "Syrian", "Taiwanese", "Tajik", "Tanzanian", "Thai",
            "Togolese", "Tongan", "Trinidadian", "Tunisian", "Turkish", "Turkmen", "Tuvaluan",
            "Ugandan", "Ukrainian", "Uruguayan", "Uzbek", "Vanuatuan", "Vatican", "Venezuelan",
            "Vietnamese", "Welsh", "Yemeni", "Zambian", "Zimbabwean"
        ];

        nationalitySelect.innerHTML = `<option value="" disabled selected>Select your nationality</option>`;
        nationalities.forEach(nationality => {
            const option = document.createElement("option");
            option.value = nationality;
            option.textContent = nationality;
            nationalitySelect.appendChild(option);
        });

        console.log("✅ Nationality dropdown populated successfully!");
    }

    /** 📱 Initialize Phone Input */
    function initializePhoneInput() {
        const mobileInput = document.querySelector("#mobile");

        if (!mobileInput) {
            console.error("❌ Mobile input field not found. Skipping phone initialization.");
            return;
        }

        console.log("📱 Initializing intlTelInput...");
        window.intlTelInput(mobileInput, {
            separateDialCode: true,
            initialCountry: "us",
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        });
        console.log("✅ Phone input initialized.");
    }

    /** 🚀 Run Initialization */
    populateNationalities();
    initializePhoneInput();
});
</script>


@endsection
