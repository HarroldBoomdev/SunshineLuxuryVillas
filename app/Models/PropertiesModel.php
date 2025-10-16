<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PropertiesModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'properties';

    protected $fillable = [
        'title',
        'property_description',
        'reference',
        'property_type',
        'floors',
        'parkingSpaces',
        'bathrooms',
        'bedrooms',
        'furnished',
        'orientation',
        'year_construction',
        'year_renovation',
        'price',
        'vat',
        'status',
        'energyEfficiency',
        'owner',
        'refId',
        'region',
        'town',
        'address',
        'labels',
        'image_order',
        'photos',
        'photo',

        // 🏠 Areas (match DB)
        'covered_m2',
        'plot_m2',
        'roofgarden_m2',
        'attic_m2',
        'covered_veranda_m2',
        'uncovered_veranda_m2',
        'garden_m2',
        'basement_m2',
        'courtyard_m2',
        'covered_parking_m2',

        // 🧭 Distances (match DB)
        'amenities_km',
        'airport_km',
        'sea_km',
        'public_transport_km',
        'schools_km',
        'resort_km',

        // 🧾 Land fields
        'regnum',
        'plotnum',
        'section',
        'sheetPlan',
        'titleDead',
        'share',

        // JSON fields
        'facilities',
        'titledeed',
        'property_status',
        'published_at',
        'external_slug',
    ];

    protected $casts = [
        // arrays / json
        'facilities' => 'array',
        'titledeed'  => 'array',
        // keep photos cast as array; accessor will sanitize it further
        'photos'     => 'array',

        // numbers
        'price'      => 'float',
        'vat'        => 'float',

        // 🏠 Areas
        'covered_m2'           => 'float',
        'plot_m2'              => 'float',
        'roofgarden_m2'        => 'float',
        'attic_m2'             => 'float',
        'covered_veranda_m2'   => 'float',
        'uncovered_veranda_m2' => 'float',
        'garden_m2'            => 'float',
        'basement_m2'          => 'float',
        'courtyard_m2'         => 'float',
        'covered_parking_m2'   => 'float',

        // 🧭 Distances
        'amenities_km'         => 'float',
        'airport_km'           => 'float',
        'sea_km'               => 'float',
        'public_transport_km'  => 'float',
        'schools_km'           => 'float',
        'resort_km'            => 'float',

        'share'        => 'float',
        'movedinReady' => 'date',
        'published_at' => 'datetime',
    ];

    /* -----------------------------------------------------------------
     |  Deletion hooks (S3 cleanup on force delete by default)
     | ----------------------------------------------------------------- */

    protected static function booted(): void
    {
        // Clean S3 when the record is permanently removed
        static::forceDeleted(function (PropertiesModel $property) {
            $property->deleteS3Assets();
        });

        // If you want to delete from S3 on SOFT delete as well, uncomment:
        /*
        static::deleting(function (PropertiesModel $property) {
            if (!method_exists($property, 'isForceDeleting') || !$property->isForceDeleting()) {
                $property->deleteS3Assets();
            }
        });
        */
    }

    public function deleteS3Assets(): void
    {
        // Delete folder by reference (adjust if you store under a subdir)
        $prefix = trim((string) $this->reference);
        if ($prefix !== '' && preg_match('/^[A-Za-z0-9._-]+$/', $prefix)) {
            // Example if keys are like "TEST123/...":
            Storage::disk('s3')->deleteDirectory(strtoupper($prefix));

            // If your keys are "properties/TEST123/...":
            // Storage::disk('s3')->deleteDirectory("properties/{$prefix}");
        }

        // Defensive: delete individual keys we can infer from stored URLs
        foreach ($this->photos as $url) {
            if ($key = $this->s3KeyFromUrl($url)) {
                Storage::disk('s3')->delete($key);
            }
        }
        if ($this->photo && ($key = $this->s3KeyFromUrl($this->photo))) {
            Storage::disk('s3')->delete($key);
        }
    }

    protected function s3KeyFromUrl(string $url): ?string
    {
        if (preg_match('#^https?://#i', $url)) {
            $path = parse_url($url, PHP_URL_PATH);
            return $path ? ltrim($path, '/') : null;
        }
        $url = ltrim($url, '/');
        if (Str::startsWith($url, 'storage/')) {
            return substr($url, 8); // drop "storage/"
        }
        return $url ?: null;
    }

    /* -----------------------------------------------------------------
     |  Accessors / Normalizers
     | ----------------------------------------------------------------- */

    /**
     * Normalize photos column to a clean array of URLs.
     * Handles: proper arrays, JSON strings, and fragmented/double-encoded values.
     */
    public function getPhotosAttribute($value): array
    {
        // Coerce to array of fragments
        $raw = $this->attributes['photos'] ?? $value;

        if (is_array($raw)) {
            $fragments = $raw;
        } elseif (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $fragments = is_array($decoded) ? $decoded : [$raw];
        } else {
            return [];
        }

        // Detect fragmented/double-encoded pattern and attempt to re-decode
        $looksFragmented = collect($fragments)->contains(function ($v) {
            return is_string($v)
                && (Str::contains($v, ['\"https://','\"http://'])
                    || Str::startsWith($v, '"[')
                    || Str::endsWith($v, ']"'));
        });

        if ($looksFragmented) {
            $blob = implode('', array_map(fn($v) => is_string($v) ? $v : json_encode($v), $fragments));
            $blob = trim($blob);
            if (preg_match('/^".*"$/s', $blob)) $blob = trim($blob, '"');
            $blob = str_replace(['\\"','\\/','\\u002F'], ['"', '/', '/'], $blob);

            $decodedAgain = json_decode($blob, true);
            if (is_array($decodedAgain)) {
                $fragments = $decodedAgain;
            }
        }

        // Extract strings from mixed structures
        $urls = [];
        $pull = function ($item) {
            if (is_string($item)) return trim($item);
            if (is_array($item)) {
                foreach (['url','src','path','thumbnail','original','image','href'] as $k) {
                    if (!empty($item[$k]) && is_string($item[$k])) return trim($item[$k]);
                }
            }
            return null;
        };
        foreach ($fragments as $f) {
            if ($u = $pull($f)) $urls[] = $u;
        }

        // Sanitize each to absolute/public URL
        $urls = array_values(array_unique(array_filter(array_map(function ($u) {
            // If an http(s) image URL is embedded inside a messy string, extract it
            if (!preg_match('#^https?://#i', $u)) {
                if (preg_match('#https?://[^\s"\'\]]+\.(?:jpg|jpeg|png|webp|gif)#i', $u, $m)) {
                    $u = $m[0];
                }
            }

            if (preg_match('#^https?://#i', $u)) return $u;

            $u = ltrim($u, '/');
            if (Str::startsWith($u, 'storage/')) {
                return asset($u);
            }
            return asset('storage/' . $u);
        }, $urls))));

        return $urls;
    }

    /**
     * Primary/cover photo. Uses 'photo' if provided, else first from normalized photos.
     */
    public function getPhotoAttribute($value): ?string
    {
        if (is_string($value) && $value !== '') return $value;

        $photos = $this->photos; // normalized
        return $photos[0] ?? null;
    }

    /**
     * Thumbnail URL with placeholder fallback.
     */
    public function getThumbUrlAttribute(): string
    {
        return $this->photo ?: asset('images/no-image.jpg');
    }

    /* -----------------------------------------------------------------
     |  Other attribute normalizers you already had (kept as-is)
     | ----------------------------------------------------------------- */

    public function getLabelsAttribute($value)
    {
        if (is_array($value)) return $value;
        if ($value === null || $value === '') return [];

        $trimmed = trim($value);
        if (str_starts_with($trimmed, '[') || str_starts_with($trimmed, '{')) {
            $decoded = json_decode($trimmed, true);
            return is_array($decoded) ? array_map('trim', $decoded) : [];
        }
        return array_values(array_filter(array_map('trim', explode(',', $value))));
    }

    public function getLivingRoomsAttribute($value)     { return $this->attributes['livingRooms']     ?? $value; }
    public function getParkingSpacesAttribute($value)   { return $this->attributes['parkingSpaces']   ?? $value; }
    public function getEnergyEfficiencyAttribute($value){ return $this->attributes['energyEfficiency']?? $value; }
    public function getYearConstructionAttribute($value){ return $this->attributes['yearConstruction']?? $value; }
    public function getYearRenovationAttribute($value)  { return $this->attributes['yearRenovation']  ?? $value; }
    public function getCoveredVerandaAttribute($value)  { return $this->attributes['coveredVeranda']  ?? $value; }
    public function getUncoveredVerandaAttribute($value){ return $this->attributes['uncoveredVeranda']?? $value; }
    public function getPublicTransportAttribute($value) { return $this->attributes['publicTransport'] ?? $value; }
    public function scopeActive($q)
    {
        return $q->where('property_status', 'Active');
    }

    public function scopeBlind($q)
    {
        return $q->where(function ($qq) {
            $qq->whereNull('property_status')->orWhere('property_status', '');
        });
    }


}
