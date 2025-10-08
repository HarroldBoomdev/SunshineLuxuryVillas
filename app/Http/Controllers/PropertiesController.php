<?php

namespace App\Http\Controllers;

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
        $query = PropertiesModel::select(
            'id',
            'title',
            'property_description',
            'property_type',
            'photos',
            'reference',
            // 'proptype',
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

        // Show only user-owned properties unless admin
        if (!auth()->user()->hasPermissionTo('property.view')) {
            $query->where('user_id', auth()->id());
        }

        // Filters
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
                        $subQuery->where('reference', 'like', '%R')->whereRaw("LENGTH(reference) - LENGTH(REPLACE(reference, 'R', '')) = 1");
                        break;
                    case 'altamira':
                        $subQuery->where('reference', 'like', '%A')->whereRaw("LENGTH(reference) - LENGTH(REPLACE(reference, 'A', '')) = 1");
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

        // Log the query to see what is being executed
        \Log::info('Query executed: ' . $query->toSql());
        \Log::info('Bindings: ' . json_encode($query->getBindings()));

        // AJAX response
        if ($request->ajax()) {
            $properties = $query->get()->transform(function ($property) {
                $property->photos = json_decode($property->photos, true) ?? [];
                return $property;
            });

            return response()->json([
                'properties' => view('properties.table', compact('properties'))->render(),
                'total' => count($properties)
            ]);
        }

        // Normal view
        $query->orderBy('id', 'desc');
        $properties = $query->paginate(20);

        // Property types and countries
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

        return view('properties.index', compact('properties', 'propertyTypes', 'countries'));
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
                'html' => view('forms.new-property-form', compact('regions'))->render()
            ]);
        }

        return view('forms.new-property-form', compact('regions'));
    }

    public function store(Request $request)
    {
        // 1) Validate
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'property_description' => 'nullable|string',
            'property_type' => 'required|string',
            'floors' => 'nullable|integer',
            'parkingSpaces' => 'nullable|integer',
            'bedrooms' => 'nullable|integer',
            'bathrooms' => 'nullable|integer',
            'year_construction' => 'nullable|integer',
            'year_renovation' => 'nullable|integer',
            'furnished' => 'nullable|string',
            'reference' => 'nullable|string|max:255',
            'status' => 'nullable|string',
            'basement' => 'nullable|string',
            'orientation' => 'nullable|string',
            'energyEfficiency' => 'nullable|string',
            'vat' => 'nullable|string',
            'price' => 'nullable|numeric',
            'covered' => 'nullable|numeric',
            'attic' => 'nullable|numeric',
            'coveredVeranda' => 'nullable|numeric',
            'coveredParking' => 'nullable|numeric',
            'courtyard' => 'nullable|numeric',
            'plot' => 'nullable|numeric',
            'roofGarden' => 'nullable|numeric',
            'uncoveredVeranda' => 'nullable|numeric',
            'garden' => 'nullable|numeric',
            'owner' => 'nullable|string',
            'refId' => 'nullable|string',
            'region' => 'nullable|string',
            'town' => 'nullable|string',
            'address' => 'nullable|string',

            // arrays
            'labels' => 'nullable|array',
            'image_order' => 'nullable|array',
            'photos' => 'nullable|array',

            'regnum' => 'nullable|string|max:255',
            'plotnum' => 'nullable|string|max:255',
            'section' => 'nullable|string|max:255',
            'sheetPlan' => 'nullable|string|max:255',
            'titleDead' => 'nullable|in:-,available,in_process,no_title',
            'share' => 'nullable|numeric',
            'amenities' => 'nullable|numeric',
            'airport' => 'nullable|numeric',
            'sea' => 'nullable|numeric',
            'publicTransport' => 'nullable|numeric',
            'schools' => 'nullable|numeric',
            'resort' => 'nullable|numeric',

            'titledeed'   => 'nullable|array',
            'title_deed'  => 'nullable|array',
            'title_deed.*'=> 'file|image|max:30720',
        ]);

        // 2) JSON-encode array fields
        foreach (['labels','image_order','photos','floor_plans','titledeed'] as $jsonKey) {
            if (isset($validated[$jsonKey]) && is_array($validated[$jsonKey])) {
                $validated[$jsonKey] = json_encode($validated[$jsonKey]);
            }
        }

        // 3) Uploads + merge
        $data = $this->processFileUploads($request, $validated);

        // 4) attach user_id (if needed)
        $data['user_id'] = auth()->id();

        // 5) Create
        $property = PropertiesModel::create($data);

        \Log::info('✅ Property saved successfully', ['id' => $property->id]);

        return redirect()->route('properties.index')->with('success', 'Property added successfully.');
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

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'reference' => 'nullable|string|max:255',
                'title' => 'nullable|string|max:255',
                'property_description' => 'nullable|string',
                'property_type' => 'nullable|string|max:255',
                'price' => 'nullable|numeric',
                'vat' => 'nullable|string|max:255',
                'owner' => 'nullable|string|max:255',
                'status' => 'nullable|string|max:255',
                'bedrooms' => 'nullable|integer',
                'bathrooms' => 'nullable|integer',
                'toilets' => 'nullable|integer',
                'kitchens' => 'nullable|integer',
                'parkingSpaces' => 'nullable|integer',
                'furnished' => 'nullable|string|max:255',
                'orientation' => 'nullable|string|max:255',
                'floor' => 'nullable|string|max:255',
                'floors' => 'nullable|integer',
                'energyEfficiency' => 'nullable|string|max:10',
                'basement' => 'nullable|numeric',
                'covered' => 'nullable|numeric',
                'attic' => 'nullable|numeric',
                'coveredVeranda' => 'nullable|numeric',
                'coveredParking' => 'nullable|numeric',
                'courtyard' => 'nullable|numeric',
                'roofGarden' => 'nullable|numeric',
                'uncoveredVeranda' => 'nullable|numeric',
                'plot' => 'nullable|numeric',
                'garden' => 'nullable|numeric',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'accuracy' => 'nullable|string|max:255',
                'region' => 'nullable|string|max:255',
                'town' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'complex' => 'nullable|string|max:255',
                'street' => 'nullable|string|max:255',
                'zipcode' => 'nullable|string|max:255',
                'image_order' => 'nullable|array',
                'photos'      => 'nullable|array',
                'labels'      => 'nullable|array',
                'regnum'        => 'nullable|string|max:255',
                'plotnum'       => 'nullable|string|max:255',
                'section'       => 'nullable|string|max:255',
                'sheetPlan'     => 'nullable|string|max:255',
                'titleDead'     => 'nullable|in:-,available,in_process,no_title',
                'share'         => 'nullable|numeric',
                'amenities'       => 'nullable|numeric',
                'airport'         => 'nullable|numeric',
                'sea'             => 'nullable|numeric',
                'publicTransport' => 'nullable|numeric',
                'schools'         => 'nullable|numeric',
                'resort'          => 'nullable|numeric',
                'titledeed'    => 'nullable|array',
                'title_deed'   => 'nullable|array',
                'title_deed.*' => 'file|image|max:30720',


            ]);

            AuditLog::create([
                'trace_id' => uniqid(),
                'type' => 'UPDATE',
                'resource_action' => 'Updated property: ' . $property->reference,
                'user_name' => auth()->user()->name,
                'ip_address' => $request->ip(),
                'date_time' => now(),
            ]);

            return redirect()->route('properties.show', $id)->with('success', 'Property updated successfully!');
            } catch (\Exception $e) {
                \Log::error('Error occurred while updating the property: ' . $e->getMessage());
                return back()->with('error', 'An unexpected error occurred while updating the property.')->withInput();
            }
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
        // ----- GALLERY PHOTOS (name="photos[]") -----
        $existingPhotos = [];
        if (!empty($data['photos'])) {
            $decoded = is_array($data['photos']) ? $data['photos'] : json_decode($data['photos'], true);
            $existingPhotos = is_array($decoded) ? $decoded : [];
        }
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $path = $file->store('uploads/photos', 'public');
                $existingPhotos[] = $path;
            }
        }
        if (!empty($existingPhotos)) {
            $data['photos'] = json_encode($existingPhotos);
        }

        // ----- TITLE DEED IMAGES -----
        // Hidden ordered list (existing): name="titledeed"
        $existingDeeds = [];
        if (!empty($data['titledeed'])) {
            $decoded = is_array($data['titledeed']) ? $data['titledeed'] : json_decode($data['titledeed'], true);
            $existingDeeds = is_array($decoded) ? $decoded : [];
        }
        // New uploads: name="title_deed[]"
        if ($request->hasFile('title_deed')) {
            foreach ($request->file('title_deed') as $file) {
                $path = $file->store('uploads/titledeed', 'public');
                $existingDeeds[] = $path; // appended to the end
            }
        }
        $data['titledeed'] = json_encode($existingDeeds);

        // ----- FLOOR PLANS (optional merge) -----
        $existingPlans = [];
        if (!empty($data['floor_plans'])) {
            $decoded = is_array($data['floor_plans']) ? $data['floor_plans'] : json_decode($data['floor_plans'], true);
            $existingPlans = is_array($decoded) ? $decoded : [];
        }
        if ($request->hasFile('floor_plans')) {
            foreach ($request->file('floor_plans') as $file) {
                $path = $file->store('uploads/floorplans', 'public');
                $existingPlans[] = $path;
            }
        }
        if (!empty($existingPlans)) {
            $data['floor_plans'] = json_encode($existingPlans);
        }

        return $data;
    }


    public function destroy($id)
    {
        $property = PropertiesModel::findOrFail($id);
        $property->delete();

        return redirect()->route('properties.index')->with('success', 'Property deleted successfully.');
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

    public function picker(Request $request)
    {
    $q = $request->get('q');

    $query = \App\Models\PropertiesModel::query();

    if ($q) {
        $query->where('reference', 'like', "%{$q}%")
                ->orWhere('title', 'like', "%{$q}%")
                ->orWhere('location', 'like', "%{$q}%");
    }

    $items = $query->limit(20)->get(['id','reference','title','location']);

    return response()->json([
        'items' => $items
    ]);
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






}
