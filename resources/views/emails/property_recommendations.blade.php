<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Property Recommendations</title>
</head>
<body style="font-family: Arial, sans-serif; color:#222;">

  <div style="max-width: 820px; margin:0 auto; padding:20px;">
    <h2 style="margin:0 0 10px 0;">Sunshine Luxury Villas</h2>

    <p>Dear {{ $clientName }},</p>

    <p>
      Further to your request, please find details below on selected properties that we feel may be of interest to you.
    </p>

    @foreach($properties as $p)
      <div style="margin:18px 0; border-top:1px solid #eee; padding-top:12px;">
        <div style="background:#f2f2f2; padding:8px 10px; font-weight:bold;">
          {{ $p['title_line'] ?: 'Property' }}
        </div>

        <table cellpadding="0" cellspacing="0" style="width:100%; margin-top:10px;">
          <tr>
            <td style="width:180px; vertical-align:top;">
              <img src="{{ $p['photo'] }}" alt="" style="width:160px; height:110px; object-fit:cover; border:1px solid #ddd;">
            </td>
            <td style="vertical-align:top;">
              <div style="margin-bottom:8px;">
                {{ $p['desc'] }}
              </div>

              <div style="margin:6px 0;">
                Ref: {{ $p['reference'] }} &nbsp;&nbsp;&nbsp;
                {{ number_format($p['price'], 0) }} €
              </div>

              <a href="{{ $p['url'] }}" style="color:#1a73e8; text-decoration:underline;">
                View Further Details
              </a>
            </td>
          </tr>
        </table>
      </div>
    @endforeach
  </div>

</body>
</html>
