<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocationsController extends Controller
{
    /**
     * Country → Region → City/Town
     */
    private array $data = [
        'Cyprus' => [
            'Nicosia' => [
                'Nicosia',
                'Strovolos',
                'Lakatamia',
                'Latsia',
                'Aglantzia',
                'Dali',
                'Tseri',
                'Pera Chorio',
                'Lythrodontas',
                'Kokkinotrimithia',
            ],
            'Limassol' => [
                'Limassol',
                'Germasogeia',
                'Agios Athanasios',
                'Mesa Geitonia',
                'Kolossi',
                'Ypsonas',
                'Parekklisia',
                'Moni',
                'Agios Tychon',
                'Episkopi',
            ],
            'Larnaca' => [
                'Larnaca',
                'Oroklini',
                'Aradippou',
                'Dromolaxia',
                'Kiti',
                'Pervolia',
                'Lefkara',
                'Meneou',
            ],
            'Paphos' => [
                'Paphos',
                'Peyia',
                'Chloraka',
                'Kissonerga',
                'Emba',
                'Tala',
                'Yeroskipou',
                'Coral Bay',
            ],
            'Famagusta' => [
                'Paralimni',
                'Ayia Napa',
                'Deryneia',
                'Sotira',
                'Liopetri',
                'Frenaros',
                'Xylofagou',
                'Avgorou',
            ],
            'Troodos' => [
                'Platres',
                'Kakopetria',
                'Pedoulas',
                'Omodos',
                'Kalopanayiotis',
            ],
        ],

        'Greece' => [
            'Attica' => [
                'Athens',
                'Piraeus',
                'Marousi',
                'Glyfada',
                'Voula',
                'Kifisia',
                'Peristeri',
                'Nea Smyrni',
            ],
            'Central Greece' => [
                'Chalkida',
                'Lamia',
                'Livadeia',
                'Karpenisi',
            ],
            'Peloponnese' => [
                'Tripoli',
                'Kalamata',
                'Corinth',
                'Sparta',
                'Argos',
                'Nafplio',
            ],
            'Crete' => [
                'Heraklion',
                'Chania',
                'Rethymno',
                'Agios Nikolaos',
                'Ierapetra',
            ],
            'Thessaly' => [
                'Larissa',
                'Volos',
                'Karditsa',
                'Trikala',
            ],
            'Central Macedonia' => [
                'Thessaloniki',
                'Serres',
                'Katerini',
                'Veria',
                'Edessa',
                'Giannitsa',
            ],
            'Western Greece' => [
                'Patras',
                'Agrinio',
                'Pyrgos',
            ],
            'Epirus' => [
                'Ioannina',
                'Arta',
                'Preveza',
            ],
            'Ionian Islands' => [
                'Corfu',
                'Zakynthos',
                'Kefalonia',
                'Lefkada',
            ],
            'North Aegean Islands' => [
                'Mytilene',
                'Chios',
                'Samos',
            ],
            'South Aegean Islands' => [
                'Rhodes',
                'Kos',
                'Santorini',
                'Mykonos',
                'Paros',
            ],
            'Eastern Macedonia and Thrace' => [
                'Kavala',
                'Xanthi',
                'Komotini',
                'Drama',
                'Alexandroupoli',
            ],
            'Western Macedonia' => [
                'Kozani',
                'Florina',
                'Grevena',
                'Kastoria',
            ],
        ],
    ];

    public function regions(Request $req)
    {
        $country = $req->query('country');
        $regions = array_keys($this->data[$country] ?? []);
        sort($regions);
        return response()->json($regions);
    }

    public function towns(Request $req)
    {
        $country = $req->query('country');
        $region  = $req->query('region');
        $towns   = $this->data[$country][$region] ?? [];
        sort($towns);
        return response()->json($towns);
    }
}
