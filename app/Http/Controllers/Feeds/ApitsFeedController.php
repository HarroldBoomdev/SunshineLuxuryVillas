<?php
// app/Http/Controllers/Feeds/ApitsFeedController.php

namespace App\Http\Controllers\Feeds;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Response;

class ApitsFeedController extends Controller
{
    public function index(Request $request)
    {
        // 1) Pull exportable properties (adjust scopes/columns to your schema)
        $props = \App\Models\Property::query()
            ->where('status', 'published')
            ->whereJsonLength('photos', '>', 0)
            ->with(['agent']) // if you need lead email
            ->limit(5000)     // APITS supports large feeds; tune or paginate if needed
            ->get();

        $TYPE_MAP = [
            'apartment'=>'Apartment','flat'=>'Flat','condo'=>'Condo','villa'=>'Villa','townhouse'=>'Townhouse',
            'bungalow'=>'Bungalow','penthouse'=>'Penthouse','studio'=>'Studio','house'=>'House','land'=>'Land',
            'plot'=>'Plot','commercial'=>'Commercial','hotel'=>'Hotel','restaurant'=>'Restaurant','farmhouse'=>'Farmhouse',
            'country house'=>'Country House','cottage'=>'Cottage','key ready'=>'Key Ready','new home'=>'New Homes',
            'bar'=>'Bar','barn'=>'Barn','cortijo'=>'Cortijo','chateaux'=>'Chateaux','quad'=>'Quad','vineyard'=>'Vineyard',
            '*' => 'Property',
        ];

        // Use XMLWriter for speed/memory
        $x = new \XMLWriter();
        $x->openMemory();
        $x->startDocument('1.0', 'UTF-8');
        $x->setIndent(true);

        $x->startElement('Properties');

        foreach ($props as $p) {
            // ---- helpers & transforms ----
            $uid = (string)($p->reference_code ?: $p->id);
            $updated = optional($p->updated_at)->format('Y-m-d H:i:s') ?: now()->format('Y-m-d H:i:s');

            $country = (string)($p->country ?: '');
            $region  = (string)($p->region  ?: '');
            $subreg  = (string)($p->area    ?: ($p->subregion ?? ''));
            $town    = (string)($p->town    ?: '');

            $postcode= (string)($p->postcode ?: '');
            $address = (string)($p->address  ?: '');

            $lat = $p->lat !== null ? (string)$p->lat : '';
            $lng = $p->lng !== null ? (string)$p->lng : '';

            // price + currency
            $salePrice = $p->price ?? null;                 // choose your primary price column
            $currency  = strtoupper($p->currency ?? 'GBP'); // APITS default GBP if empty

            // handle POA
            $salePriceNode = '';
            if (is_numeric($salePrice) && $salePrice >= 5000) {
                $salePriceNode = (string)intval($salePrice);
            } else {
                $salePriceNode = 'POA';
            }

            // type mapping
            $rawType = Str::of((string)($p->type ?? ''))->lower()->value();
            $apitsType = $TYPE_MAP[$rawType] ?? $TYPE_MAP['*'];

            // bool-ish flags
            $newBuild = $this->boolish($p->is_new_build ?? false);
            $hasPool  = $this->boolish(
                ($p->pool ?? null) ? true :
                (is_array($p->amenities ?? null) && collect($p->amenities)->contains(fn($a)=>Str::contains(Str::lower($a),'pool')))
            );

            // areas
            $built = $p->covered_area ?? $p->built_area ?? null;
            $plot  = $p->plot_area ?? null;

            // energy
            $cons = $this->gradeOrEmpty($p->energy_consumption ?? null);
            $emis = $this->gradeOrEmpty($p->energy_emissions ?? null);

            // text (strip HTML except <br/>; wrap CDATA)
            $title   = $p->title ?: '';
            $intro   = Str::limit(strip_tags(($p->short_description ?? ''), '<br>'), 1000, '');
            $desc    = strip_tags(($p->description ?? ''), '<br>');

            // media
            $photos = collect($p->photos ?? [])->take(30)->values();
            $floorplans = collect($p->floor_plans ?? [])->take(30)->values();

            // virtual tour
            $virtual = $p->matterport_link ?? $p->kuula_link ?? $p->youtube_url ?? $p->vimeo_url ?? '';

            // lead email
            $leadEmail = $p->agent_email ?? optional($p->agent)->email ?? config('slv.apits_default_lead_email', '');

            // features
            $features = collect($p->features ?? $p->amenities ?? [])->filter()->values()->take(50);

            // ---- write property ----
            $x->startElement('Property');

              $this->node($x, 'UniquePropertyID', $uid);
              $this->node($x, 'LastUpdateDate', $updated);
              $this->node($x, 'LeadEmail', $leadEmail);

              $this->node($x, 'Country', $country);
              $this->node($x, 'Region', $region);
              $this->node($x, 'Subregion', $subreg);
              $this->node($x, 'Town', $town);

              $this->node($x, 'Postcode', $postcode);
              $this->node($x, 'Address', $address);

              $x->startElement('GeoLocation');
                $this->node($x, 'Latitude', $lat);
                $this->node($x, 'Longitude', $lng);
              $x->endElement(); // GeoLocation

              $this->node($x, 'SalePrice', $salePriceNode);
              $this->node($x, 'PriceType', 'Sale');
              $this->node($x, 'Currency', in_array($currency, ['GBP','EUR','USD']) ? $currency : 'GBP');

              $this->node($x, 'PropertyType', $apitsType);
              $this->node($x, 'NumBedrooms', (string)($p->bedrooms ?? ''));
              $this->node($x, 'NumBathrooms', (string)($p->bathrooms ?? ''));
              $this->node($x, 'NewBuild', $newBuild);
              $this->node($x, 'Pool', $hasPool);
              $this->node($x, 'VirtualTour', $virtual);

              $x->startElement('SurfaceArea');
                $this->node($x, 'Built', $built !== null ? (string)$built : '');
                $this->node($x, 'Plot',  $plot  !== null ? (string)$plot  : '');
              $x->endElement(); // SurfaceArea

              $x->startElement('EnergyRating');
                $this->node($x, 'Consumption', $cons);
                $this->node($x, 'Emissions',   $emis);
              $x->endElement(); // EnergyRating

              $x->startElement('PropertyName');
                $this->cdataNode($x, 'en', $title);
              $x->endElement(); // PropertyName

              $x->startElement('Introduction');
                $this->cdataNode($x, 'en', $intro);
              $x->endElement(); // Introduction

              $x->startElement('Description');
                $this->cdataNode($x, 'en', $desc);
              $x->endElement(); // Description

              $x->startElement('Features');
                foreach ($features as $f) {
                  $x->startElement('Feature');
                    $this->cdataNode($x, 'en', (string)$f);
                  $x->endElement();
                }
              $x->endElement(); // Features

              $x->startElement('Photos');
                foreach ($photos as $i => $ph) {
                    $url = is_array($ph) ? ($ph['url'] ?? $ph['src'] ?? '') : (string)$ph;
                    // ensure absolute URL
                    if ($url && !Str::startsWith($url, ['http://','https://'])) {
                        $url = URL::to($url);
                    }
                    $cap = is_array($ph) ? ($ph['caption'] ?? '') : '';
                    $x->startElement('Photo');
                      $this->node($x, 'Url', $url);
                      $this->node($x, 'Caption', $cap ?: 'Photo '.($i+1));
                    $x->endElement();
                }
              $x->endElement(); // Photos

              $x->startElement('Floorplans');
                foreach ($floorplans as $i => $fp) {
                    $url = is_array($fp) ? ($fp['url'] ?? $fp['src'] ?? '') : (string)$fp;
                    if ($url && !Str::startsWith($url, ['http://','https://'])) {
                        $url = URL::to($url);
                    }
                    $cap = is_array($fp) ? ($fp['caption'] ?? '') : '';
                    $x->startElement('Floorplan');
                      $this->node($x, 'Url', $url);
                      $this->node($x, 'Caption', $cap ?: 'Floorplan '.($i+1));
                    $x->endElement();
                }
              $x->endElement(); // Floorplans

            $x->endElement(); // Property
        }

        $x->endElement(); // Properties
        $x->endDocument();

        return Response::make($x->outputMemory(), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8'
        ]);
    }

    private function node(\XMLWriter $x, string $name, $value): void
    {
        $x->startElement($name);
        $x->text($value === null ? '' : (string)$value);
        $x->endElement();
    }

    private function cdataNode(\XMLWriter $x, string $name, $value): void
    {
        $x->startElement($name);
        $x->writeCData((string)($value ?? ''));
        $x->endElement();
    }

    private function boolish($v): string
    {
        if (is_string($v)) {
            $v = strtolower($v);
            return in_array($v, ['1','yes','true','y']) ? '1' : (in_array($v, ['0','no','false','n']) ? '0' : '');
        }
        return $v ? '1' : '0';
    }

    private function gradeOrEmpty($v): string
    {
        $v = strtoupper((string)$v);
        return in_array($v, ['A','B','C','D','E','F','G']) ? $v : '';
    }
}
