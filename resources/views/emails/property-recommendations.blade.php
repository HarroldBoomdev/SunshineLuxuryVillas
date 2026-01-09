<!doctype html>
<html>
<head>
  <meta charset="utf-8">
</head>
<body style="font-family: Arial, Helvetica, sans-serif; background:#f6f7f9; padding:24px;">
  <div style="max-width:820px;margin:0 auto;background:#ffffff;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;">
    <div style="padding:18px 20px;border-bottom:1px solid #e5e7eb;">
      <div style="font-size:18px;font-weight:700;">Sunshine Luxury Villas</div>
      <div style="font-size:12px;color:#6b7280;">SLV Estates Ltd UK</div>
    </div>

    <div style="padding:18px 20px;">
      <p style="margin:0 0 12px 0;">Dear {{ $clientName }},</p>
      <p style="margin:0 0 18px 0;color:#374151;">
        Further to your request, please find details below on selected properties that we feel may be of interest to you.
      </p>

      @foreach($properties as $p)
        <div style="border-top:1px solid #e5e7eb; padding:14px 0; display:flex; gap:14px; align-items:flex-start;">
          @if(!empty($p['thumb']))
            <img src="{{ $p['thumb'] }}" alt="" style="width:160px;height:120px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;">
          @endif

          <div style="flex:1;">
            <div style="font-weight:700; color:#111827; margin-bottom:6px;">
              {{ $p['title'] ?: 'Property' }}
            </div>
            <div style="color:#374151; font-size:13px; margin-bottom:6px;">
              {{ $p['location'] ?: 'N/A' }} • {{ $p['property_type'] ?: 'N/A' }}
            </div>
            <div style="color:#111827; font-size:13px; margin-bottom:6px;">
              Ref: <strong>{{ $p['reference'] }}</strong>
              @if(!empty($p['price']))
                &nbsp;&nbsp; Price: <strong>€{{ number_format((float)$p['price'], 0, '.', ',') }}</strong>
              @endif
            </div>

            {{-- optional link (if you have public URL, replace this) --}}
            <div style="margin-top:10px;">
              <span style="display:inline-block;background:#111827;color:#fff;padding:8px 10px;border-radius:6px;font-size:12px;">
                View Further Details (link can be wired next)
              </span>
            </div>
          </div>
        </div>
      @endforeach

      <div style="border-top:1px solid #e5e7eb;margin-top:16px;padding-top:12px;color:#6b7280;font-size:12px;">
        If you wish to change your search criteria, you may email us at any time detailing the locations, price range,
        property type and minimum bedrooms that best match your requirements.
      </div>

      <div style="margin-top:16px;">
        Kind regards,<br>
        <strong>Sunshine Luxury Villas</strong>
      </div>
    </div>
  </div>
</body>
</html>
