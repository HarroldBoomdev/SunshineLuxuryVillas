<p>Hi {{ $data['name'] ?? 'there' }},</p>

<p>Thanks for contacting <strong>Sunshine Luxury Villas</strong>. We’ve received your enquiry and will get back to you shortly.</p>

@if(!empty($data['reference']))
    <p>Property reference: <strong>{{ $data['reference'] }}</strong></p>
@endif

@if(!empty($data['payload']['page_url']))
    <p>Page: <a href="{{ $data['payload']['page_url'] }}">{{ $data['payload']['page_url'] }}</a></p>
@endif

<p>— Sunshine Luxury Villas</p>
