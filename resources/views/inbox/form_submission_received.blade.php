@php
    // Core fields: hide null/empty/"N/A"
    $core = [
        'Name'      => $submission->name ?? null,
        'Email'     => $submission->email ?? null,
        'Phone'     => $submission->phone ?? null,
        'Reference' => $submission->reference ?? null,
    ];

    $isReal = function ($v) {
        if (is_null($v)) return false;
        $s = trim(is_array($v) ? implode(', ', array_filter($v)) : (string) $v);
        if ($s === '') return false;
        $bad = ['N/A','NA','NONE','-','—'];
        return !in_array(mb_strtoupper($s), $bad, true);
    };

    $core = array_filter($core, $isReal);

    // Payload filtered
    $filteredPayload = [];
    if (is_array($submission->payload)) {
        foreach ($submission->payload as $k => $v) {
            $s = is_array($v) ? implode(', ', array_filter($v)) : (string) $v;
            if ($isReal($s)) {
                $filteredPayload[$k] = $s;
            }
        }
    }

    $title = ucwords(str_replace('_',' ', $submission->form_key));
@endphp

@component('mail::message')
# New {{ $title }} submission

@component('mail::panel')
**Form:** {{ $title }}
**Submission ID:** #{{ $submission->id }}
@endcomponent

@if(count($core))
@component('mail::table')
| Field | Value |
|:--|:--|
@foreach($core as $k => $v)
| {{ $k }} | {{ $v }} |
@endforeach
@endcomponent
@endif

@if(count($filteredPayload))
@component('mail::table')
| Field | Value |
|:--|:--|
@foreach($filteredPayload as $k => $v)
| {{ ucwords(str_replace('_',' ', $k)) }} | {{ $v }} |
@endforeach
@endcomponent
@endif

Thanks,
**Sunshine Luxury Villas**
@endcomponent
