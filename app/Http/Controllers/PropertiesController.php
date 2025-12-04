<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Http\Resources\PropertyResource;
use App\Models\PropertiesModel;
use App\Models\Deal;
use App\Models\DiaryModel;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Exports\PropertiesExport;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;
use Intervention\Image\Facades\Image;
use App\Services\FrontendSyncService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class PropertiesController extends Controller
{


    public function export(Request $request)
    {
        return Excel::download(new PropertiesExport($request), 'properties.xlsx');
    }

    public function apiIndex(Request $request)
    {
        $properties = PropertiesModel::select(
            'id', 'title', 'photos', 'bedrooms', 'bathrooms', 'price'
        )->get();

        // Convert `photos` from JSON string to an actual array
        $properties->transform(function ($property) {
            $property->photos = json_decode($property->photos, true) ?? [];
            return $property;
        });

        return response()->json($properties);
    }

    public function index(Request $request)
    {
        $currentTab = $request->query('tab', 'all'); // all | active | not_active | featured | logs

        // -----------------------
        // 1) BASE PROPERTIES QUERY
        // -----------------------
        $query = PropertiesModel::select(
            'id',
            'title',
            'property_description',
            'property_type',
            'photos',
            'reference',
            'bedrooms',
            'bathrooms',
            'price',
            'country',
            'complex',
            'labels',
            'status',
            'created_at',
            'owner'
        );

        // Only own properties unless user has full view permission
        if (!auth()->user()->hasPermissionTo('property.view')) {
            $query->where('user_id', auth()->id());
        }

        // ---------
        // FILTERS
        // ---------
        if ($request->filled('reference')) {
            $query->where('reference', 'LIKE', "%{$request->reference}%");
        }

        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        if ($request->filled('property_type')) {
            $query->where('property_type', $request->property_type);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', '>=', (int) $request->bedrooms);
        }

        if ($request->filled('bathrooms')) {
            $query->where('bathrooms', '>=', (int) $request->bathrooms);
        }

        if ($request->filled('bank')) {
            $bank = strtolower($request->bank);

            $query->where(function ($subQuery) use ($bank) {
                switch ($bank) {
                    case 'remu':
                        $subQuery->where('reference', 'like', '%R')
                            ->whereRaw("LENGTH(reference) - LENGTH(REPLACE(reference, 'R', '')) = 1");
                        break;
                    case 'altamira':
                        $subQuery->where('reference', 'like', '%A')
                            ->whereRaw("LENGTH(reference) - LENGTH(REPLACE(reference, 'A', '')) = 1");
                        break;
                    case 'altia':
                        $subQuery->where(function ($q) {
                            $q->where('reference', 'like', '%ALT')
                            ->orWhere('reference', 'like', '%AT');
                        });
                        break;
                    case 'gogordian':
                        $subQuery->where('reference', 'like', '%GG');
                        break;
                    case 'alpha bank':
                        $subQuery->where('reference', 'like', '%AB')
                                ->where('reference', 'not like', '%ABPIR');
                        break;
                    case 'astro bank':
                        $subQuery->where('reference', 'like', '%ABPIR');
                        break;
                }
            });
        }

        // -----------------
        // TAB FILTERS
        // -----------------
        switch ($currentTab) {
            case 'active':
                // treat any case of "active" as active
                $query->whereRaw("LOWER(property_status) = 'active'");
                break;

            case 'not_active':
                $query->where(function ($q) {
                    $q->whereNull('property_status')
                    ->orWhere('property_status', '')
                    ->orWhereRaw("LOWER(TRIM(property_status)) = 'none'");
                });
                break;

            case 'featured':
                $refs = Cache::get('featured_refs', []);

                if (!empty($refs)) {
                    $query->whereIn('reference', $refs);
                } else {
                    // show nothing if no featured props saved
                    $query->whereRaw('1 = 0');
                }
                break;


        }

        // -------------
        // AJAX FILTERING
        // -------------
        if ($request->ajax() && $currentTab !== 'logs') {
            $properties = $query->orderByDesc('id')->get()->transform(function ($property) {
                $property->photos = json_decode($property->photos, true) ?? [];
                return $property;
            });

            return response()->json([
                'properties' => view('properties.table', compact('properties'))->render(),
                'total'      => $properties->count(),
            ]);
        }

        // -------------
        // NORMAL VIEW
        // -------------
        $properties = $query->orderByDesc('id')->paginate(20);

        // Logs paginator – ONLY when on Logs tab
        $logs = null;
        if ($currentTab === 'logs') {
            $logs = \App\Models\PropertyActivityLog::with(['property:id,reference,title', 'user:id,name'])
                ->orderByDesc('created_at')
                ->paginate(50);
        }

        // Property types and countries (same as you already had)
        $propertyTypes = [
            "Apartment", "Bungalow", "Commercial Property", "Investment Property",
            "Penthouse", "Plot", "Studio", "Townhouse", "Villa"
        ];

        $countries = [
            "Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Argentina", "Armenia",
            "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados",
            "Belarus", "Belgium", "Belize", "Benin", "Bhutan", "Bolivia", "Bosnia and Herzegovina",
            "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia",
            "Cameroon", "Canada", "Cape Verde", "Central African Republic", "Chad", "Chile",
            "China", "Colombia", "Comoros", "Congo", "Costa Rica", "Croatia", "Cuba", "Cyprus",
            "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "Ecuador",
            "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Eswatini",
            "Ethiopia", "Fiji", "Finland", "France", "Gabon", "Gambia", "Georgia", "Germany",
            "Ghana", "Greece", "Grenada", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana",
            "Haiti", "Honduras", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq",
            "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya",
            "Kiribati", "Korea (North)", "Korea (South)", "Kuwait", "Kyrgyzstan", "Laos", "Latvia",
            "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg",
            "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands",
            "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia",
            "Montenegro", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal",
            "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Macedonia",
            "Norway", "Oman", "Pakistan", "Palau", "Palestine", "Panama", "Papua New Guinea",
            "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Qatar", "Romania",
            "Russia", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines",
            "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia",
            "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands",
            "Somalia", "South Africa", "Spain", "Sri Lanka", "Sudan", "Suriname", "Sweden",
            "Switzerland", "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Timor-Leste",
            "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan",
            "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States",
            "Uruguay", "Uzbekistan", "Vanuatu", "Vatican City", "Venezuela", "Vietnam",
            "Yemen", "Zambia", "Zimbabwe"
        ];

        $pickerProps = PropertiesModel::select('id', 'reference', 'title', 'country')
            ->orderByDesc('id')
            ->limit(500)
            ->get();

        $counts = [
            'all'       => PropertiesModel::count(),
            'active'    => PropertiesModel::whereRaw("LOWER(property_status) = 'active'")->count(),
            'not_active'=> PropertiesModel::whereNull('property_status')
                                ->orWhere('property_status', '')
                                ->orWhereRaw("LOWER(TRIM(property_status)) = 'none'")
                                ->count(),
            'featured'   => count(Cache::get('featured_refs', [])),
        ];


        return view('properties.index', compact(
            'properties',
            'logs',
            'propertyTypes',
            'countries',
            'pickerProps',
            'counts',
            'currentTab'
        ));
    }


    public function edit($id)
    {
        $property = \App\Models\PropertiesModel::findOrFail($id);
        return view('properties.edit', compact('property'))
            ->with('mode', 'edit');     // <-- add this
    }

    public function show($id)
    {
        $property = PropertiesModel::findOrFail($id);

        // Ensure photos is an array (use model accessor if present)
        $photos = is_array($property->photos)
            ? $property->photos
            : (is_string($property->photos) ? (json_decode($property->photos, true) ?: []) : []);

        $deals   = Deal::all();
        $diaries = DiaryModel::all();
        $logs    = AuditLog::where('resource_action', 'like', "%property: {$property->reference}%")
                    ->latest()
                    ->get();

        return view('properties.details', compact('property', 'deals', 'diaries', 'logs', 'photos'));
    }

    public function create(Request $request)
    {
       $regions = [
            'Paphos' => [
                'Paphos Town', 'Geroskipou', 'Pegeia', 'Polis Chrysochous', 'Kissonerga', 'Chloraka', 'Tala', 'Emba',
                'Kouklia', 'Mandria', 'Peyia', 'Kathikas', 'Droushia', 'Latchi', 'Agia Marina Chrysochous', 'Agia Varvara',
                'Acheleia', 'Timi', 'Neo Chorio', 'Milia'
            ],
            'Limassol' => [
                'Limassol Town', 'Agios Athanasios', 'Ypsonas', 'Mesa Geitonia', 'Kato Polemidia', 'Germasogeia', 'Episkopi',
                'Pissouri', 'Agros', 'Platres', 'Moni', 'Kolossi', 'Erimi', 'Parekklisia', 'Sotira', 'Asgata', 'Pentakomo',
                'Akrounta', 'Pano Lefkara'
            ],
            'Nicosia' => [
                'Nicosia Town', 'Strovolos', 'Lakatamia', 'Aglandjia', 'Engomi', 'Latsia', 'Dali', 'Pera Chorio',
                'Kokkinotrimithia', 'Agia Varvara', 'Agioi Trimithias', 'Pera', 'Klirou', 'Agia Marina Xyliatou',
                'Agios Sozomenos', 'Agios Epifanios Oreinis', 'Agios Georgios Kafkallou'
            ],
            'Larnaca' => [
                'Larnaca Town', 'Aradippou', 'Oroklini', 'Kiti', 'Pervolia', 'Mazotos', 'Alethriko', 'Anglisides',
                'Tersefanou', 'Pyla', 'Agia Anna', 'Avdellero', 'Alaminos', 'Agioi Vavatsinias', 'Klavdia', 'Kornos',
                'Lympia', 'Xylotymvou'
            ],
            'Famagusta' => [
                'Paralimni', 'Ayia Napa', 'Deryneia', 'Sotira', 'Frenaros', 'Avgorou', 'Liopetri', 'Achna', 'Acheritou',
                'Xylofagou', 'Xylophagou', 'Vrysoulles', 'Dasaki Achnas', 'Agios Nikolaos'
            ],
            'Kyrenia' => [
                'Kyrenia Town', 'Lapithos', 'Karavas', 'Karmi', 'Bellapais', 'Ozanköy', 'Catalköy', 'Alsancak',
                'Karaoğlanoğlu', 'Zeytinlik', 'Karmi', 'Karakum', 'Doğanköy', 'Esentepe', 'Bahçeli'
            ]
        ];


        if ($request->ajax()) {
            return response()->json([
                'html' => view('forms.new-property', compact('regions'))->render()
            ]);
        }

        return view('forms.new-property', compact('regions'));
    }

    public function store(Request $request)
    {
        // 1) Validate
        $validated = $request->validate([
            // =========================
            // STEP 1 – BASIC PROPERTY
            // =========================
            'title'                 => 'required|string|max:255',
            'property_description'  => 'nullable|string',
            'property_type'         => 'required|string',
            'floors'                => 'nullable|integer',
            'parkingSpaces'         => 'nullable|integer',
            'bedrooms'              => 'nullable|integer',
            'bathrooms'             => 'nullable|integer',

            // Step 1 extras
            'poa'                   => 'nullable|boolean',
            'floor'                 => 'nullable|integer|min:0',
            'title_deeds'           => 'nullable|in:Yes,No',
            'long_let'              => 'nullable|string|max:10',
            'leasehold'             => 'nullable|string|max:10',
            'terrace'               => 'nullable|numeric',
            'pool'                  => 'nullable|string|max:255',
            'pool_description'      => 'nullable|string',

            // Price panel
            'currency'              => 'nullable|string|max:3',
            'price'                 => 'nullable|numeric',    // simple price
            'price_current'         => 'nullable|numeric|min:0',
            'price_original'        => 'nullable|numeric|min:0',
            'poa_current'           => 'nullable|boolean',
            'reduction_percent'     => 'nullable|numeric|min:0|max:100',
            'reduction_value'       => 'nullable|numeric|min:0',
            'display_as_percentage' => 'nullable|in:Yes,No',
            'monthly_rent'          => 'nullable|numeric|min:0',

            // Years
            'year_construction'     => 'nullable|integer',
            'year_renovation'       => 'nullable|integer|gte:year_construction',

            // Misc
            'furnished'             => 'nullable|string',
            'reference'             => 'required_with:photos|string|max:255',
            'status'                => 'nullable|string',
            'orientation'           => 'nullable|string',
            'energyEfficiency'      => 'nullable|string',
            'vat'                   => 'nullable|string',

            // =========================
            // STEP 5 – AREAS (m²)
            // (matches Step 5 Blade: area_covered, area_plot, etc.)
            // =========================
            'area_covered'          => 'nullable|numeric',
            'area_plot'             => 'nullable|numeric',
            'area_roof_garden'      => 'nullable|numeric',
            'area_attic'            => 'nullable|numeric',
            'area_cov_veranda'      => 'nullable|numeric',
            'area_uncov_veranda'    => 'nullable|numeric',
            'area_cov_parking'      => 'nullable|numeric',
            'area_basement'         => 'nullable|numeric',
            'area_courtyard'        => 'nullable|numeric',
            'area_garden'           => 'nullable|numeric',

            // =========================
            // STEP 2 – PROPERTY LOCATION
            // =========================
            'country'               => 'nullable|string|max:255',
            'region'                => 'nullable|string|max:255',
            'town'                  => 'nullable|string|max:255',
            'locality'              => 'nullable|string|max:255',

            'latitude'              => 'nullable|numeric',
            'longitude'             => 'nullable|numeric',
            'map_address'           => 'nullable|string',
            'accuracy'              => 'nullable|string|max:255',

            // Extra owner/location (manual / imports)
            'owner'                 => 'nullable|string',
            'refId'                 => 'nullable|string',
            'address'               => 'nullable|string',

            // =========================
            // ARRAYS / JSON
            // =========================
            'labels'                => 'nullable|array',
            'labels.*'              => 'nullable|string|max:255',
            'image_order'           => 'nullable|array',
            'photos'                => 'nullable|array',

            'display_as'            => 'nullable|array',
            'display_as.*'          => 'nullable|string|max:255',
            'external'              => 'nullable|array',
            'external.*'            => 'nullable|string|max:255',
            'other'                 => 'nullable|array',
            'other.*'               => 'nullable|string|max:255',
            'banner'                => 'nullable|string|max:255',

            // =========================
            // STEP 7 – VENDOR / SOLICITOR / BANK
            // =========================
            // vendor
            'first_name'           => 'nullable|string|max:255',
            'last_name'            => 'nullable|string|max:255',
            'telephone'            => 'nullable|string|max:255',
            'mobile'               => 'nullable|string|max:255',
            'email'                => 'nullable|email|max:255',
            'type'                 => 'nullable|string|max:255',
            'source'               => 'nullable|string|max:255',
            'notes'                => 'nullable|string',

            // solicitor
            'sol_first_name'       => 'nullable|string|max:255',
            'sol_last_name'        => 'nullable|string|max:255',
            'sol_phone_day'        => 'nullable|string|max:255',
            'sol_email'            => 'nullable|email|max:255',
            'sol_address'          => 'nullable|string',

            // bank
            'bank_name'            => 'nullable|string|max:255',
            'bank_sort_code'       => 'nullable|string|max:255',
            'bank_account_name'    => 'nullable|string|max:255',
            'bank_account_number'  => 'nullable|string|max:255',
            'bank_address'         => 'nullable|string',

            // vendor address & map (Step 7 Blade names)
            'building_name'        => 'nullable|string|max:255',
            'address_line1'        => 'nullable|string|max:255',
            'address_line2'        => 'nullable|string|max:255',
            'address_line3'        => 'nullable|string|max:255',
            'postcode'             => 'nullable|string|max:255',
            'geolocation'          => 'nullable|string|max:255',
            'lat'                  => 'nullable|string|max:50',
            'lng'                  => 'nullable|string|max:50',

            // =========================
            // LAND FIELDS
            // =========================
            'regnum'                => 'nullable|string|max:255',
            'plotnum'               => 'nullable|string|max:255',
            'section'               => 'nullable|string|max:255',
            'sheetPlan'             => 'nullable|string|max:255',
            'titleDead'             => 'nullable|in:-,available,in_process,no_title',
            'share'                 => 'nullable|numeric',

            // =========================
            // DISTANCES (km)
            // (amenities, airport, etc. from Step 5 top)
            // =========================
            'amenities'             => 'nullable|numeric',
            'airport'               => 'nullable|numeric',
            'sea'                   => 'nullable|numeric',
            'publicTransport'       => 'nullable|numeric',
            'schools'               => 'nullable|numeric',
            'resort'                => 'nullable|numeric',

            // =========================
            // FILES (IMAGES)
            // =========================
            'titledeed'             => 'nullable',          // will be filled by processFileUploads
            'title_deed'            => 'nullable|array',
            'title_deed.*'          => 'file|image|max:30720',

            'floor_plans'           => 'nullable|array',
            'floor_plans.*'         => 'file|image|max:30720',

            // =========================
            // STEP 9 – VIDEOS / TOUR / DOCS
            // =========================
            'youtube1'              => 'nullable|string|max:255',
            'youtube2'              => 'nullable|string|max:255',
            'virtual_tour'          => 'nullable|string|max:255',
            'document'              => 'nullable|file|max:51200', // ~50MB

            // =========================
            // STEP 10 – STATUS (+ optional AI language)
            // =========================
            'property_status'       => 'nullable|in:Active,', // '' (None) or 'Active'
            'ai_language'           => 'nullable|string|max:50',
        ]);

        // Start with validated data
        $data = $validated;

        // 2) JSON-encode array fields stored as JSON in DB
        foreach (['labels', 'image_order'] as $jsonKey) {
            if (isset($data[$jsonKey]) && is_array($data[$jsonKey])) {
                $data[$jsonKey] = json_encode($data[$jsonKey]);
            }
        }

        // 3) Handle file uploads (photos, floor_plans, titledeed, etc.)
        //    This should fill $data['photos'], $data['floor_plans'], $data['titledeed'] etc.
        $data = $this->processFileUploads($request, $data);

        // 4) Normalize Areas & Distances
        //    (e.g. area_covered → covered_m2, amenities → amenities_km, etc.)
        $data = $this->normalizeAreaAndDistanceKeys($data);

        // 4b) STEP 6 – Pack property options into features JSON
        $featuresPayload = [
            'display_as' => $request->input('display_as', []),
            'external'   => $request->input('external', []),
            'other'      => $request->input('other', []),
            'banner'     => $request->input('banner'),
        ];
        $data['features'] = json_encode($featuresPayload);

        // 4c) STEP 7 – Pack Vendor/Solicitor/Bank/Address into vendor_details JSON IF column exists
        if (Schema::hasColumn('properties', 'vendor_details')) {
            $data['vendor_details'] = json_encode([
                'vendor' => [
                    // (Note: "title" for vendor is optional; you can add it later)
                    'first_name'  => $request->first_name,
                    'last_name'   => $request->last_name,
                    'telephone'   => $request->telephone,
                    'mobile'      => $request->mobile,
                    'email'       => $request->email,
                    'type'        => $request->type,
                    'source'      => $request->source,
                    'notes'       => $request->notes,
                ],
                'solicitor' => [
                    'first_name'  => $request->sol_first_name,
                    'last_name'   => $request->sol_last_name,
                    'phone_day'   => $request->sol_phone_day,
                    'email'       => $request->sol_email,
                    'address'     => $request->sol_address,
                ],
                'bank' => [
                    'name'            => $request->bank_name,
                    'sort_code'       => $request->bank_sort_code,
                    'account_name'    => $request->bank_account_name,
                    'account_number'  => $request->bank_account_number,
                    'address'         => $request->bank_address,
                ],
                'address' => [
                    // matches Step 7 "Vendor Address" field names
                    'building_name' => $request->building_name,
                    'line1'         => $request->address_line1,
                    'line2'         => $request->address_line2,
                    'line3'         => $request->address_line3,
                    'locality'      => $request->locality,
                    'town'          => $request->town,
                    'region'        => $request->region,
                    'postcode'      => $request->postcode,
                    'country'       => $request->country,
                    'geolocation'   => $request->geolocation,
                    'lat'           => $request->lat,
                    'lng'           => $request->lng,
                ],
            ]);
        }

        // 4d) STEP 8 – Custom Fields → custom_fields JSON (if column exists)
        if (Schema::hasColumn('properties', 'custom_fields')) {
            // All prefixes used in your Step 8 Blade
            $customPrefixes = [
                'views_', 'orient_', 'plot_', 'garden_', 'pool_',
                'park_', 'mla_', 'guest_', 'kitchen_', 'floor_',
                'extra_', 'heat_', 'ac_', 'incl_', 'svc_',
                'furn_', 'guesthouse_',
            ];

            $customFields = [];

            foreach ($request->all() as $key => $value) {
                if ($value === null || $value === '') {
                    continue; // skip empty
                }

                foreach ($customPrefixes as $prefix) {
                    if (\Illuminate\Support\Str::startsWith($key, $prefix)) {
                        $customFields[$key] = $value;
                        break;
                    }
                }
            }

            // Always save as JSON object (even if empty)
            $data['custom_fields'] = json_encode($customFields);
        }

        // ==============================
        // STEP 1 – SPECIAL MAPPINGS
        // ==============================

        // Prefer "Current Price" over basic price if present
        if ($request->filled('price_current')) {
            $data['price'] = (float) $request->input('price_current');
        }

        // Title deeds: Yes/No → internal codes
        if ($request->filled('title_deeds')) {
            $td = $request->input('title_deeds');
            $data['titleDead'] = $td === 'Yes'
                ? 'available'
                : ($td === 'No' ? 'no_title' : null);
        }

        // Terrace: save as terrace_m2 if that column exists
        if ($request->filled('terrace') && Schema::hasColumn('properties', 'terrace_m2')) {
            $data['terrace_m2'] = (float) $request->input('terrace');
        }

        // Original price → reducedPrice
        if ($request->filled('price_original')) {
            $data['reducedPrice'] = (float) $request->input('price_original');
        }

        // Display reduction as % ?
        $data['display_reduction_as_percent'] =
            $request->input('display_as_percentage') === 'Yes';

        // Monthly rent
        if ($request->filled('monthly_rent')) {
            $data['monthly_rent'] = $request->input('monthly_rent');
        }

        // ==============================
        // STEP 2 – LOCATION MAPPINGS
        // ==============================

        // Locality (form) → location2 (DB)
        if ($request->filled('locality')) {
            $data['location2'] = $request->input('locality');
        }

        // Map address from reverse geocode → address column
        if ($request->filled('map_address')) {
            $data['address'] = $request->input('map_address');
        }

        // Lat/Lng for property (latitude, longitude) already validated

        // 5) Attach owner
        $data['user_id'] = auth()->id();

        // 6) Property status & is_live mirror
        $data['property_status'] = $request->input('property_status', ''); // '' or 'Active'

        if (Schema::hasColumn('properties', 'is_live')) {
            $data['is_live'] = ($data['property_status'] === 'Active');
        }

        // ==============================
        // STEP 9 – VIDEOS, TOUR, DOCS, FACILITIES
        // ==============================

        // YouTube & Virtual Tour links
        if ($request->filled('youtube1')) {
            $data['youtube1'] = $request->input('youtube1');
        }
        if ($request->filled('youtube2')) {
            $data['youtube2'] = $request->input('youtube2');
        }
        if ($request->filled('virtual_tour')) {
            $data['virtual_tour'] = $request->input('virtual_tour');
        }

        // Facilities (labels[] → labels JSON), only if column exists
        if (Schema::hasColumn('properties', 'labels')) {
            $data['labels'] = json_encode($request->input('labels', []));
        }

        // Handle single document upload (document), if column exists
        if ($request->hasFile('document') && Schema::hasColumn('properties', 'document')) {
            $file     = $request->file('document');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path     = $file->storeAs('documents', $filename, 'public');
            $data['document'] = $path;
        }

        // ==============================
        // FINAL FILTER + SAVE
        // ==============================

        // Filter out any non-existent columns (safety)
        $columns = Schema::getColumnListing((new PropertiesModel)->getTable());
        $data    = array_intersect_key($data, array_flip($columns));

        // Persist
        $property = PropertiesModel::create($data);

        \Log::info('✅ Property saved successfully', ['id' => $property->id]);

        return redirect()
            ->route('properties.index')
            ->with('success', 'Property added successfully.');
    }


    public function validateRequest(Request $request)
    {
        return $request->validate([
            'title' => 'nullable|string|max:255',
            'property_description' => 'nullable|string', // NEW FIELD
            'property_type' => 'nullable|string|max:255',
            'bundle' => 'nullable|string|max:255',
            'keysafe' => 'nullable|string|max:255',
            // 'proptype' => 'required|string|max:255',
            'floor' => 'nullable|string|max:255',
            'parkingSpaces' => 'nullable|integer',
            'kitchens' => 'nullable|integer',
            'bathrooms' => 'nullable|integer',
            'toilets' => 'nullable|integer',
            'furnished' => 'nullable|string|max:255',
            'orientation' => 'nullable|string|max:255',
            'yearRenovation' => 'nullable|integer',
            'movedinReady' => 'nullable|date',
            'valByAgent' => 'nullable|numeric',
            'price' => 'nullable|numeric',
            'vat' => 'nullable|string|max:255',
            'groundRent' => 'nullable|numeric',
            'reference' => 'required|string|max:255|unique:properties,reference',
            'managing_agent' => 'nullable|string|max:255',
            'number' => 'nullable|integer',
            'labels' => 'nullable|array',
            'status' => 'nullable|string|max:255',
            'floors' => 'nullable|integer',
            'livingRooms' => 'nullable|integer',
            'bedrooms' => 'nullable|integer',
            'showers' => 'nullable|integer',
            'basement' => 'nullable|integer',
            'yearConstruction' => 'nullable|integer',
            'energyEfficiency' => 'nullable|string|max:255',
            'communalCharge' => 'nullable|numeric',
            'comChargeFreq' => 'nullable|string|max:255',
            'reducedPrice' => 'nullable|numeric',
            'commission' => 'nullable|numeric',
            'owner' => 'nullable|string|max:255',
            'refId' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'accuracy' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'complex' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'zipcode' => 'nullable|string|max:255',
            'covered' => 'nullable|numeric',
            'attic' => 'nullable|numeric',
            'coveredVeranda' => 'nullable|numeric',
            'coveredParking' => 'nullable|numeric',
            'courtyard' => 'nullable|numeric',
            'roofGarden' => 'nullable|numeric',
            'uncoveredVeranda' => 'nullable|numeric',
            'plot' => 'nullable|numeric',
            'garden' => 'nullable|numeric',
            'regnum' => 'nullable|string|max:255',
            'plotnum' => 'nullable|string|max:255',
            'titleDead' => 'nullable|string|max:255',
            'section' => 'nullable|string|max:255',
            'sheetPlan' => 'nullable|string|max:255',
            'share' => 'nullable|numeric',
            'amenities' => 'nullable|numeric',
            'sea' => 'nullable|numeric',
            'schools' => 'nullable|numeric',
            'airport' => 'nullable|numeric',
            'publicTransport' => 'nullable|numeric',
            'resort' => 'nullable|numeric',
            'facilities' => 'nullable|array',
            'photos' => 'nullable|array',
            'floor_plans' => 'nullable|array',
            'titledeed' => 'nullable|array',
            'kuula_link' => 'nullable|string|max:255',
            'youtube_links' => 'nullable|array',
            'notes' => 'nullable|array',
        ]);
    }

    public function update(Request $request, PropertiesModel $property)
    {
        // 1) Validate (same rules as store)
        $validated = $request->validate([
            // =========================
            // STEP 1 – BASIC PROPERTY
            // =========================
            'title'                 => 'required|string|max:255',
            'property_description'  => 'nullable|string',
            'property_type'         => 'required|string',
            'floors'                => 'nullable|integer',
            'parkingSpaces'         => 'nullable|integer',
            'bedrooms'              => 'nullable|integer',
            'bathrooms'             => 'nullable|integer',

            // Step 1 extras
            'poa'                   => 'nullable|boolean',
            'floor'                 => 'nullable|integer|min:0',
            'title_deeds'           => 'nullable|in:Yes,No',
            'long_let'              => 'nullable|string|max:10',
            'leasehold'             => 'nullable|string|max:10',
            'terrace'               => 'nullable|numeric',
            'pool'                  => 'nullable|string|max:255',
            'pool_description'      => 'nullable|string',

            // Price panel
            'currency'              => 'nullable|string|max:3',
            'price'                 => 'nullable|numeric',
            'price_current'         => 'nullable|numeric|min:0',
            'price_original'        => 'nullable|numeric|min:0',
            'poa_current'           => 'nullable|boolean',
            'reduction_percent'     => 'nullable|numeric|min:0|max:100',
            'reduction_value'       => 'nullable|numeric|min:0',
            'display_as_percentage' => 'nullable|in:Yes,No',
            'monthly_rent'          => 'nullable|numeric|min:0',

            // Years
            'year_construction'     => 'nullable|integer',
            'year_renovation'       => 'nullable|integer|gte:year_construction',

            // Misc
            'furnished'             => 'nullable|string',
            'reference'             => 'required_with:photos|string|max:255',
            'status'                => 'nullable|string',
            'orientation'           => 'nullable|string',
            'energyEfficiency'      => 'nullable|string',
            'vat'                   => 'nullable|string',

            // =========================
            // STEP 5 – AREAS (m²)
            // =========================
            'area_covered'          => 'nullable|numeric',
            'area_plot'             => 'nullable|numeric',
            'area_roof_garden'      => 'nullable|numeric',
            'area_attic'            => 'nullable|numeric',
            'area_cov_veranda'      => 'nullable|numeric',
            'area_uncov_veranda'    => 'nullable|numeric',
            'area_cov_parking'      => 'nullable|numeric',
            'area_basement'         => 'nullable|numeric',
            'area_courtyard'        => 'nullable|numeric',
            'area_garden'           => 'nullable|numeric',

            // =========================
            // STEP 2 – PROPERTY LOCATION
            // =========================
            'country'               => 'nullable|string|max:255',
            'region'                => 'nullable|string|max:255',
            'town'                  => 'nullable|string|max:255',
            'locality'              => 'nullable|string|max:255',

            'latitude'              => 'nullable|numeric',
            'longitude'             => 'nullable|numeric',
            'map_address'           => 'nullable|string',
            'accuracy'              => 'nullable|string|max:255',

            // Extra owner/location (manual / imports)
            'owner'                 => 'nullable|string',
            'refId'                 => 'nullable|string',
            'address'               => 'nullable|string',

            // =========================
            // ARRAYS / JSON
            // =========================
            'labels'                => 'nullable|array',
            'labels.*'              => 'nullable|string|max:255',
            'image_order'           => 'nullable|array',
            'photos'                => 'nullable|array',

            'display_as'            => 'nullable|array',
            'display_as.*'          => 'nullable|string|max:255',
            'external'              => 'nullable|array',
            'external.*'            => 'nullable|string|max:255',
            'other'                 => 'nullable|array',
            'other.*'               => 'nullable|string|max:255',
            'banner'                => 'nullable|string|max:255',

            // =========================
            // STEP 7 – VENDOR / SOLICITOR / BANK
            // =========================
            // vendor
            'first_name'           => 'nullable|string|max:255',
            'last_name'            => 'nullable|string|max:255',
            'telephone'            => 'nullable|string|max:255',
            'mobile'               => 'nullable|string|max:255',
            'email'                => 'nullable|email|max:255',
            'type'                 => 'nullable|string|max:255',
            'source'               => 'nullable|string|max:255',
            'notes'                => 'nullable|string',

            // solicitor
            'sol_first_name'       => 'nullable|string|max:255',
            'sol_last_name'        => 'nullable|string|max:255',
            'sol_phone_day'        => 'nullable|string|max:255',
            'sol_email'            => 'nullable|email|max:255',
            'sol_address'          => 'nullable|string',

            // bank
            'bank_name'            => 'nullable|string|max:255',
            'bank_sort_code'       => 'nullable|string|max:255',
            'bank_account_name'    => 'nullable|string|max:255',
            'bank_account_number'  => 'nullable|string|max:255',
            'bank_address'         => 'nullable|string',

            // vendor address & map (Step 7 Blade names)
            'building_name'        => 'nullable|string|max:255',
            'address_line1'        => 'nullable|string|max:255',
            'address_line2'        => 'nullable|string|max:255',
            'address_line3'        => 'nullable|string|max:255',
            'postcode'             => 'nullable|string|max:255',
            'geolocation'          => 'nullable|string|max:255',
            'lat'                  => 'nullable|string|max:50',
            'lng'                  => 'nullable|string|max:50',

            // =========================
            // LAND FIELDS
            // =========================
            'regnum'                => 'nullable|string|max:255',
            'plotnum'               => 'nullable|string|max:255',
            'section'               => 'nullable|string|max:255',
            'sheetPlan'             => 'nullable|string|max:255',
            'titleDead'             => 'nullable|in:-,available,in_process,no_title',
            'share'                 => 'nullable|numeric',

            // =========================
            // DISTANCES (km)
            // =========================
            'amenities'             => 'nullable|numeric',
            'airport'               => 'nullable|numeric',
            'sea'                   => 'nullable|numeric',
            'publicTransport'       => 'nullable|numeric',
            'schools'               => 'nullable|numeric',
            'resort'                => 'nullable|numeric',

            // =========================
            // FILES (IMAGES)
            // =========================
            'titledeed'             => 'nullable',
            'title_deed'            => 'nullable|array',
            'title_deed.*'          => 'file|image|max:30720',

            'floor_plans'           => 'nullable|array',
            'floor_plans.*'         => 'file|image|max:30720',

            // =========================
            // STEP 9 – VIDEOS / TOUR / DOCS
            // =========================
            'youtube1'              => 'nullable|string|max:255',
            'youtube2'              => 'nullable|string|max:255',
            'virtual_tour'          => 'nullable|string|max:255',
            'document'              => 'nullable|file|max:51200',

            // =========================
            // STEP 10 – STATUS (+ optional AI language)
            // =========================
            'property_status'       => 'nullable|in:Active,',
            'ai_language'           => 'nullable|string|max:50',
        ]);

        // Start with validated data
        $data = $validated;

        // 2) JSON-encode array fields stored as JSON in DB
        foreach (['labels', 'image_order'] as $jsonKey) {
            if (isset($data[$jsonKey]) && is_array($data[$jsonKey])) {
                $data[$jsonKey] = json_encode($data[$jsonKey]);
            }
        }

        // 3) Handle file uploads (photos, floor_plans, titledeed, etc.)
        //    NOTE: helper only accepts (Request, array), so no $property here
        $data = $this->processFileUploads($request, $data);

        // 4) Normalize Areas & Distances (area_* → *_m2, amenities → amenities_km, etc.)
        $data = $this->normalizeAreaAndDistanceKeys($data);

        // 4b) STEP 6 – Pack property options into features JSON
        $featuresPayload = [
            'display_as' => $request->input('display_as', []),
            'external'   => $request->input('external', []),
            'other'      => $request->input('other', []),
            'banner'     => $request->input('banner'),
        ];
        $data['features'] = json_encode($featuresPayload);

        // 4c) STEP 7 – Pack Vendor/Solicitor/Bank/Address into vendor_details JSON IF column exists
        if (Schema::hasColumn('properties', 'vendor_details')) {
            $data['vendor_details'] = json_encode([
                'vendor' => [
                    'first_name'  => $request->first_name,
                    'last_name'   => $request->last_name,
                    'telephone'   => $request->telephone,
                    'mobile'      => $request->mobile,
                    'email'       => $request->email,
                    'type'        => $request->type,
                    'source'      => $request->source,
                    'notes'       => $request->notes,
                ],
                'solicitor' => [
                    'first_name'  => $request->sol_first_name,
                    'last_name'   => $request->sol_last_name,
                    'phone_day'   => $request->sol_phone_day,
                    'email'       => $request->sol_email,
                    'address'     => $request->sol_address,
                ],
                'bank' => [
                    'name'            => $request->bank_name,
                    'sort_code'       => $request->bank_sort_code,
                    'account_name'    => $request->bank_account_name,
                    'account_number'  => $request->bank_account_number,
                    'address'         => $request->bank_address,
                ],
                'address' => [
                    'building_name' => $request->building_name,
                    'line1'         => $request->address_line1,
                    'line2'         => $request->address_line2,
                    'line3'         => $request->address_line3,
                    'locality'      => $request->locality,
                    'town'          => $request->town,
                    'region'        => $request->region,
                    'postcode'      => $request->postcode,
                    'country'       => $request->country,
                    'geolocation'   => $request->geolocation,
                    'lat'           => $request->lat,
                    'lng'           => $request->lng,
                ],
            ]);
        }

        // 4d) STEP 8 – Custom Fields → custom_fields JSON (if column exists)
        if (Schema::hasColumn('properties', 'custom_fields')) {
            $customPrefixes = [
                'views_', 'orient_', 'plot_', 'garden_', 'pool_',
                'park_', 'mla_', 'guest_', 'kitchen_', 'floor_',
                'extra_', 'heat_', 'ac_', 'incl_', 'svc_',
                'furn_', 'guesthouse_',
            ];

            $customFields = [];

            foreach ($request->all() as $key => $value) {
                if ($value === null || $value === '') {
                    continue;
                }

                foreach ($customPrefixes as $prefix) {
                    if (\Illuminate\Support\Str::startsWith($key, $prefix)) {
                        $customFields[$key] = $value;
                        break;
                    }
                }
            }

            $data['custom_fields'] = json_encode($customFields);
        }

        // ==============================
        // STEP 1 – SPECIAL MAPPINGS
        // ==============================

        if ($request->filled('price_current')) {
            $data['price'] = (float) $request->input('price_current');
        }

        if ($request->filled('title_deeds')) {
            $td = $request->input('title_deeds');
            $data['titleDead'] = $td === 'Yes'
                ? 'available'
                : ($td === 'No' ? 'no_title' : null);
        }

        if ($request->filled('terrace') && Schema::hasColumn('properties', 'terrace_m2')) {
            $data['terrace_m2'] = (float) $request->input('terrace');
        }

        if ($request->filled('price_original')) {
            $data['reducedPrice'] = (float) $request->input('price_original');
        }

        $data['display_reduction_as_percent'] =
            $request->input('display_as_percentage') === 'Yes';

        if ($request->filled('monthly_rent')) {
            $data['monthly_rent'] = $request->input('monthly_rent');
        }

        // ==============================
        // STEP 2 – LOCATION MAPPINGS
        // ==============================

        if ($request->filled('locality')) {
            $data['location2'] = $request->input('locality');
        }

        if ($request->filled('map_address')) {
            $data['address'] = $request->input('map_address');
        }

        // NOTE: we do NOT touch user_id here; keep original property owner

        // 6) Property status & is_live mirror
        $data['property_status'] = $request->input('property_status', $property->property_status ?? '');

        if (Schema::hasColumn('properties', 'is_live')) {
            $data['is_live'] = ($data['property_status'] === 'Active');
        }

        // ==============================
        // STEP 9 – VIDEOS, TOUR, DOCS, FACILITIES
        // ==============================

        if ($request->filled('youtube1')) {
            $data['youtube1'] = $request->input('youtube1');
        }
        if ($request->filled('youtube2')) {
            $data['youtube2'] = $request->input('youtube2');
        }
        if ($request->filled('virtual_tour')) {
            $data['virtual_tour'] = $request->input('virtual_tour');
        }

        if (Schema::hasColumn('properties', 'labels')) {
            $data['labels'] = json_encode($request->input('labels', []));
        }

        if ($request->hasFile('document') && Schema::hasColumn('properties', 'document')) {
            $file     = $request->file('document');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path     = $file->storeAs('documents', $filename, 'public');
            $data['document'] = $path;
        }

        // ==============================
        // FINAL FILTER + UPDATE
        // ==============================

        $columns = Schema::getColumnListing($property->getTable());
        $data    = array_intersect_key($data, array_flip($columns));

        $property->update($data);

        \Log::info('✅ Property updated successfully', ['id' => $property->id]);

        return redirect()
            ->route('properties.index')
            ->with('success', 'Property updated successfully.');
    }



    public function downloadImages($id)
    {
        $property = PropertiesModel::findOrFail($id);

        // Convert comma-separated string to an array & remove extra quotes
        $photos = !empty($property->photos)
            ? array_map(fn($photo) => trim($photo, ' "[]'), explode(',', $property->photos))
            : [];

        if (empty($photos)) {
            return back()->with('error', 'No images found for this property.');
        }

        // Sanitize title for folder name (remove special characters and spaces)
        $folderName = preg_replace('/[^A-Za-z0-9\-]/', '_', $property->title);

        // Define ZIP file name
        $zipFileName = $folderName . '_images.zip';
        $zipFilePath = storage_path('app/' . $zipFileName);

        // Create a new ZIP archive
        $zip = new ZipArchive;
        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            foreach ($photos as $photo) {
                $photo = trim($photo);

                // Get file name from URL
                $fileName = basename(parse_url($photo, PHP_URL_PATH));

                // Create folder inside the ZIP
                $zipFilePathInZip = $folderName . '/' . $fileName;

                // Download image temporarily
                try {
                    $imageContents = @file_get_contents($photo);
                    if ($imageContents !== false) {
                        $zip->addFromString($zipFilePathInZip, $imageContents);
                    }
                } catch (Exception $e) {
                    \Log::error("Failed to download image: $photo - " . $e->getMessage());
                    continue;
                }
            }
            $zip->close();
        } else {
            return back()->with('error', 'Failed to create ZIP file.');
        }

        // Return the ZIP file as a response
        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }

    private function processFileUploads(Request $request, array $data)
    {
        /* -----------------------------
        | GALLERY PHOTOS  (to S3)
        | input name="photos[]" multiple
        * ----------------------------*/
        $existingPhotos = [];

        // Accept existing JSON/array but keep only non-empty strings (URLs)
        if (!empty($data['photos'])) {
            $decoded = is_array($data['photos']) ? $data['photos'] : json_decode($data['photos'], true);
            if (is_array($decoded)) {
                $existingPhotos = collect($decoded)
                    ->filter(fn ($v) => is_string($v) && trim($v) !== '')
                    ->values()
                    ->all();
            }
        }

        if ($request->hasFile('photos')) {
            $files = $request->file('photos');
            // normalize if a single file was posted without []
            if ($files instanceof \Illuminate\Http\UploadedFile) {
                $files = [$files];
            }

            $folder   = $this->s3FolderFromReference($data);         // e.g. TEST123
            $basename = (string)($data['reference'] ?? 'img');       // e.g. TEST123

            foreach ($files as $file) {
                if (!$file || !$file->isValid()) continue;

                $ext  = strtolower($file->getClientOriginalExtension() ?: 'jpg');
                $rand = random_int(1000, 9999);
                $key  = "{$folder}/{$basename}_{$rand}.{$ext}";

                Storage::disk('s3')->put($key, file_get_contents($file), [
                    'visibility'   => 'public',
                    'CacheControl' => 'max-age=31536000, public',
                    'ContentType'  => $file->getMimeType(),
                ]);

                $existingPhotos[] = Storage::disk('s3')->url($key);
            }
        }

        // Always save a clean JSON array (even if empty)
        $existingPhotos = array_values(array_unique(array_filter(
            $existingPhotos,
            fn ($v) => is_string($v) && $v !== ''
        )));
        $data['photos'] = json_encode($existingPhotos, JSON_UNESCAPED_SLASHES);


        /* ------------------------------------
        | TITLE DEED IMAGES  (stay on public)
        | input name="title_deed[]" multiple
        * -----------------------------------*/
        $existingDeeds = [];
        if (!empty($data['titledeed'])) {
            $decoded = is_array($data['titledeed']) ? $data['titledeed'] : json_decode($data['titledeed'], true);
            $existingDeeds = is_array($decoded) ? $decoded : [];
        }
        if ($request->hasFile('title_deed')) {
            foreach ($request->file('title_deed') as $file) {
                if (!$file || !$file->isValid()) continue;
                $path = $file->store('uploads/titledeed', 'public'); // keep local/public
                $existingDeeds[] = $path;
            }
        }
        $data['titledeed'] = json_encode(array_values($existingDeeds), JSON_UNESCAPED_SLASHES);


        /* -------------------------------
        | FLOOR PLANS  (stay on public)
        | input name="floor_plans[]" multiple
        * ------------------------------*/
        $existingPlans = [];
        if (!empty($data['floor_plans'])) {
            $decoded = is_array($data['floor_plans']) ? $data['floor_plans'] : json_decode($data['floor_plans'], true);
            $existingPlans = is_array($decoded) ? $decoded : [];
        }
        if ($request->hasFile('floor_plans')) {
            foreach ($request->file('floor_plans') as $file) {
                if (!$file || !$file->isValid()) continue;
                $path = $file->store('uploads/floorplans', 'public'); // keep local/public
                $existingPlans[] = $path;
            }
        }
        $data['floor_plans'] = json_encode(array_values($existingPlans), JSON_UNESCAPED_SLASHES);

        return $data;
    }

    // PropertiesController.php
    public function destroy($id)
    {
        $property = \App\Models\PropertiesModel::withTrashed()->findOrFail($id);

        if (request()->boolean('hard')) {
            $property->forceDelete();                 // triggers deleteS3Assets() via forceDeleted hook
            return back()->with('status', 'Deleted permanently (S3 cleaned).');
        }

        $property->delete(); // soft delete
        return back()->with('status', 'Moved to trash.');
    }


    public function byReference($ref)
    {
        // Look up property by reference
        $property = PropertiesModel::where('reference', $ref)->first();

        if (!$property) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json([
            'id'        => $property->id,
            'title'     => $property->title ?? $property->name,
            'reference' => $property->reference,
            'location'  => $property->location,
            'type'      => $property->type,
            'bedrooms'  => $property->bedrooms,
            'bathrooms' => $property->bathrooms,
            'plot'      => $property->plot,
            'covered'   => $property->covered,
            'description' => $property->description,
            'url'       => url("/property/{$property->id}"),
        ]);
    }


    public function showByReference(string $ref)
    {
        $ref = strtoupper(trim($ref));

        $p = \App\Models\PropertiesModel::query()
            ->whereRaw('UPPER(reference) = ?', [$ref])
            ->first();

        if (!$p) {
            return response()->json(['message' => 'Not found'], 404);
        }

        // return a single record; adjust fields as needed
        return response()->json($p);
    }

    public function findByReference(\Illuminate\Http\Request $request)
    {
        $ref = strtoupper(trim($request->input('reference', '')));

        if ($ref === '') {
            return response()->json(['message' => 'reference required'], 422);
        }

        $p = \App\Models\PropertiesModel::query()
            ->whereRaw('UPPER(reference) = ?', [$ref])
            ->first();

        if (!$p) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json($p);
    }

    public function lookupByRefs(\Illuminate\Http\Request $request)
    {
        $refs = (array) $request->query('refs', []);
        if (empty($refs)) {
            return response()->json(['data' => []]);
        }

        $props = \App\Models\PropertiesModel::query()
            ->select(['reference','title','town','region','country','price','status','property_type'])
            ->whereIn('reference', $refs)
            ->get()
            ->map(function ($p) {
                return [
                    'reference'      => $p->reference,
                    'title'          => $p->title,
                    'location_line'  => trim(implode(', ', array_filter([$p->town, $p->region, $p->country]))),
                    'price'          => $p->price,
                    'status'         => $p->status,
                    'property_type'  => $p->property_type,
                ];
            })
            ->keyBy('reference');

        return response()->json(['data' => $props]);


    }

    public function picker(Request $request)
    {
        $term = $request->get('q', '');

        $query = \App\Models\PropertiesModel::select('id', 'reference', 'title', 'country', 'town', 'province');

        if (strlen($term) >= 3) {
            $query->where(function ($q) use ($term) {
                $q->where('reference', 'LIKE', "%{$term}%")
                ->orWhere('title', 'LIKE', "%{$term}%");
            });
        }

        $properties = $query->orderByDesc('id')->limit(100)->get()->map(function ($p) {
            $p->location = implode(', ', array_filter([$p->town, $p->province, $p->country]));
            return $p;
        });

        return response()->json(['items' => $properties]);
    }

    private function normalizeAreaAndDistanceKeys(array $data): array
    {
        $map = [
            // Areas
            'covered'            => 'covered_m2',
            'plot'               => 'plot_m2',
            'roofGarden'         => 'roofgarden_m2',
            'attic'              => 'attic_m2',
            'coveredVeranda'     => 'covered_veranda_m2',
            'uncoveredVeranda'   => 'uncovered_veranda_m2',
            'garden'             => 'garden_m2',
            'basement'           => 'basement_m2',
            'courtyard'          => 'courtyard_m2',
            'coveredParking'     => 'covered_parking_m2',

            // Distances
            'amenities'          => 'amenities_km',
            'airport'            => 'airport_km',
            'sea'                => 'sea_km',
            'schools'            => 'schools_km',
            'publicTransport'    => 'public_transport_km',
            'resort'             => 'resort_km',
        ];

        foreach ($map as $from => $to) {
            if (array_key_exists($from, $data)) {
                $data[$to] = $data[$from];
                unset($data[$from]);
            }
        }

        return $data;
    }

    private function s3FolderFromReference(array $data): string
    {
        $ref = (string)($data['reference'] ?? '');
        // keep only safe characters (A-Z, 0-9, _ and -)
        $folder = preg_replace('/[^A-Za-z0-9_\-]/', '', $ref);
        return $folder !== '' ? strtoupper($folder) : 'MISC';
    }


}
