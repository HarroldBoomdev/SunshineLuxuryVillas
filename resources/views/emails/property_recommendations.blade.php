<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Property Recommendations</title>
  </head>
  <body style="margin:0;padding:0;background:#f5f5f5;font-family:Arial,Helvetica,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:24px 0;">
      <tr>
        <td align="center">
          <table width="720" cellpadding="0" cellspacing="0" style="max-width:720px;width:100%;background:#ffffff;border:1px solid #e6e6e6;border-radius:8px;overflow:hidden;">
            <tr>
              <td style="padding:20px 22px;">
                <h2 style="margin:0 0 8px 0;font-size:20px;line-height:1.3;color:#111;">
                  Sunshine Luxury Villas
                </h2>
                <p style="margin:0 0 14px 0;font-size:14px;line-height:1.5;color:#333;">
                  Dear {{ $clientName }},
                </p>
                <p style="margin:0 0 18px 0;font-size:14px;line-height:1.5;color:#333;">
                  Further to your request, please find details below on selected properties that we feel may be of interest to you.
                </p>

                @foreach($properties as $p)
                  <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #eaeaea;border-radius:6px;overflow:hidden;margin:0 0 14px 0;">
                    <tr>
                      <td style="background:#f1f1f1;padding:10px 12px;font-size:13px;font-weight:bold;color:#111;">
                        {{ $p['title_line'] }}
                      </td>
                    </tr>
                    <tr>
                      <td style="padding:12px;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                          <tr>
                            <td width="180" valign="top" style="padding-right:12px;">
                              @if(!empty($p['photo']))
                                <img src="{{ $p['photo'] }}" alt="" style="width:180px;max-width:180px;height:auto;border:1px solid #e6e6e6;border-radius:4px;display:block;">
                              @else
                                <div style="width:180px;height:120px;border:1px solid #e6e6e6;border-radius:4px;display:flex;align-items:center;justify-content:center;color:#777;font-size:12px;">
                                  No image
                                </div>
                              @endif
                            </td>
                            <td valign="top" style="font-size:13px;line-height:1.5;color:#333;">
                              @if(!empty($p['desc']))
                                <div style="margin:0 0 10px 0;">
                                  {{ $p['desc'] }}
                                </div>
                              @endif

                              <div style="margin:0 0 6px 0;">
                                <strong>Ref:</strong> {{ $p['reference'] ?: '-' }}
                                &nbsp;&nbsp;|&nbsp;&nbsp;
                                <strong>Price:</strong> {{ number_format((float)$p['price'], 0) }} €
                              </div>

                              <div style="margin-top:8px;">
                                <a href="{{ $p['url'] }}" style="color:#1a73e8;text-decoration:underline;">View Further Details</a>
                              </div>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                @endforeach

                <p style="margin:18px 0 0 0;font-size:12px;line-height:1.5;color:#777;">
                  If you have any questions, just reply to this email.
                </p>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>
