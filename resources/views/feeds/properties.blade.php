{{-- resources/views/feeds/properties.blade.php --}}
@php
use Illuminate\Support\Str;

$abs = function ($url) {
    if (!$url) return '';
    if (Str::startsWith($url, ['http://','https://','//'])) return $url;
    return url($url);
};

$arr = function ($val) {
    if (is_array($val)) return $val;
    if (is_string($val)) {
        $json = json_decode($val, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($json)) return $json;
        return array_values(array_filter(array_map('trim', explode(',', $val))));
    }
    return [];
};

$descOf   = fn($p) => $p->description ?: $p->property_description ?: null;
$bedsOf   = fn($p) => $p->bedrooms ?? $p->beds ?? null;
$bathsOf  = fn($p) => $p->bathrooms ?? $p->baths ?? $p->showers ?? null;
$coveredOf= fn($p) => $p->covered_area ?? $p->covered ?? $p->covered_m2 ?? $p->built_area ?? null;
@endphp

<properties generated_at="{{ $generatedAt }}">
@foreach($properties as $p)
  <property id="{{ $p->reference ?? $p->id }}">
    <url>{{ $p->url ?: ($p->external_url ?: $abs('/property/' . ($p->external_slug ?? $p->id))) }}</url>
    @if(!empty($p->title))<title><![CDATA[{{ $p->title }}]]></title>@endif

    <location>
      @if(!empty($p->address))  <address><![CDATA[{{ $p->address }}]]></address>@endif
      @if(!empty($p->street))   <street><![CDATA[{{ $p->street }}]]></street>@endif
      @if(!empty($p->complex))  <complex><![CDATA[{{ $p->complex }}]]></complex>@endif
      @if(!empty($p->town))     <town><![CDATA[{{ $p->town }}]]></town>@endif
      @if(!empty($p->city))     <city><![CDATA[{{ $p->city }}]]></city>@endif
      @if(!empty($p->province)) <province><![CDATA[{{ $p->province }}]]></province>@endif
      @if(!empty($p->region))   <region><![CDATA[{{ $p->region }}]]></region>@endif
      @if(!empty($p->country))  <country><![CDATA[{{ $p->country }}]]></country>@endif
      @if(!empty($p->zipcode))  <postcode><![CDATA[{{ $p->zipcode }}]]></postcode>@endif
      @if(!empty($p->latitude) && !empty($p->longitude))
        <lat>{{ $p->latitude }}</lat><lng>{{ $p->longitude }}</lng>
      @endif
    </location>

    @if(!empty($p->property_type ?? $p->proptype))
      <type><![CDATA[{{ $p->property_type ?? $p->proptype }}]]></type>
    @endif
    @if(!empty($p->status ?? $p->property_status))
      <status><![CDATA[{{ $p->status ?? $p->property_status }}]]></status>
    @endif

    @if(!is_null($p->price))
      <price @if(!empty($p->price_freq)) frequency="{{ $p->price_freq }}" @endif>{{ (int) $p->price }}</price>
    @endif

    @php $covered = $coveredOf($p); @endphp
    @if(!is_null($covered)) <covered_m2>{{ (float) $covered }}</covered_m2>@endif
    @if(!is_null($p->plot_area ?? $p->plot ?? $p->plot_m2))
      <plot_m2>{{ (float) ($p->plot_area ?? $p->plot ?? $p->plot_m2) }}</plot_m2>
    @endif

    @php $beds = $bedsOf($p); $baths = $bathsOf($p); @endphp
    @if(!is_null($beds))  <bedrooms>{{ (int) $beds }}</bedrooms>@endif
    @if(!is_null($baths)) <bathrooms>{{ (int) $baths }}</bathrooms>@endif

    @php
      $images = $arr($p->photos);
      $plans  = $arr($p->floor_plans);
      $yt     = $arr($p->youtube_links);
    @endphp
    @if(!empty($images))
      <images>
        @foreach($images as $img) @if($img)<image>{{ $abs($img) }}</image>@endif @endforeach
      </images>
    @endif
    @if(!empty($plans))
      <floor_plans>
        @foreach($plans as $fp) @if($fp)<plan>{{ $abs($fp) }}</plan>@endif @endforeach
      </floor_plans>
    @endif
    @if(!empty($yt))
      <videos>
        @foreach($yt as $y) @if($y)<youtube>{{ $y }}</youtube>@endif @endforeach
      </videos>
    @endif

    @if($descOf($p))
      <description><![CDATA[{!! \Illuminate\Support\Str::limit(strip_tags($descOf($p)), 5000) !!}]]></description>
    @endif

    @if(!empty($p->reference))  <reference><![CDATA[{{ $p->reference }}]]></reference>@endif
    @if(!empty($p->external_id))<external_id><![CDATA[{{ $p->external_id }}]]></external_id>@endif
    <updated_at>{{ optional($p->updated_at)->toIso8601String() ?? now()->toIso8601String() }}</updated_at>
    @if(!empty($p->published_at))<published_at>{{ \Illuminate\Support\Carbon::parse($p->published_at)->toIso8601String() }}</published_at>@endif
  </property>
@endforeach
</properties>
