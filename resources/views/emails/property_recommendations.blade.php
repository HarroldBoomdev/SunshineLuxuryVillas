{{-- resources/views/emails/property_recommendations.blade.php --}}
@php
  /** @var string $clientName */
  /** @var \Illuminate\Support\Collection|array $properties */

  // normalize to array
  $props = $properties instanceof \Illuminate\Support\Collection ? $properties->values()->all() : (array)$properties;

  // helper: money + safe text + short description
  $money = function($v){
    if ($v === null || $v === '' || !is_numeric($v)) return 'N/A';
    return '€' . number_format((float)$v, 0, '.', ',');
  };

  $safe = function($s){
    return e((string)($s ?? ''));
  };

  $trimText = function($html, $limit = 180){
    $text = trim(preg_replace('/\s+/', ' ', strip_tags((string)$html)));
    if (mb_strlen($text) <= $limit) return $text;
    return mb_substr($text, 0, $limit - 1) . '…';
  };

  // hero + rest
  $hero = $props[0] ?? null;
  $rest = array_slice($props, 1);

  // Optional: if you pass these from controller later, they show nicely.
  $budgetMin = $budgetMin ?? null;
  $budgetMax = $budgetMax ?? null;
@endphp
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="x-apple-disable-message-reformatting">
  <title>Property Recommendations</title>
</head>
<body style="margin:0; padding:0; background:#f5f7fb; font-family: Arial, Helvetica, sans-serif; color:#111827;">
  <!-- Preheader (hidden) -->
  <div style="display:none; font-size:1px; line-height:1px; max-height:0px; max-width:0px; opacity:0; overflow:hidden; mso-hide:all;">
    Your selected property recommendations are inside — tap to view details.
  </div>

  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; background:#f5f7fb;">
    <tr>
      <td align="center" style="padding:24px 12px;">
        <!-- Container -->
        <table role="presentation" width="640" cellpadding="0" cellspacing="0" style="width:640px; max-width:100%; border-collapse:collapse;">
          <!-- Brand bar -->
          <tr>
            <td style="padding:0 0 12px 0;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                <tr>
                  <td align="left" style="font-size:14px; color:#6b7280;">
                    <span style="font-weight:700; color:#111827;">Sunshine Luxury Villas</span>
                  </td>
                  <td align="right" style="font-size:12px; color:#6b7280;">
                    @if($budgetMin !== null || $budgetMax !== null)
                      <span style="display:inline-block; padding:6px 10px; border-radius:999px; background:#eef2ff; color:#3730a3; font-weight:700;">
                        {{ $budgetMin !== null ? $money($budgetMin) : '' }}{{ ($budgetMin !== null && $budgetMax !== null) ? ' – ' : '' }}{{ $budgetMax !== null ? $money($budgetMax) : '' }}
                      </span>
                    @endif
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Card -->
          <tr>
            <td style="background:#ffffff; border-radius:14px; overflow:hidden; box-shadow:0 10px 25px rgba(17,24,39,.08);">
              <!-- Header -->
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                <tr>
                  <td style="padding:22px 22px 14px 22px;">
                    <div style="font-size:22px; font-weight:800; line-height:1.2; color:#111827;">
                      Recommended Properties for You
                    </div>
                    <div style="margin-top:6px; font-size:14px; line-height:1.5; color:#6b7280;">
                      Hand-picked for <strong style="color:#111827;">{{ $safe($clientName ?? 'Client') }}</strong>.
                    </div>
                  </td>
                </tr>
                <tr>
                  <td style="padding:0 22px 16px 22px;">
                    <div style="height:1px; background:#eef2f7;"></div>
                  </td>
                </tr>
              </table>

              <!-- HERO -->
              @if($hero)
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                  <tr>
                    <td style="padding:0 22px 18px 22px;">
                      <div style="font-size:13px; letter-spacing:.08em; text-transform:uppercase; color:#6b7280; font-weight:700;">
                        Featured recommendation
                      </div>
                    </td>
                  </tr>

                  <tr>
                    <td style="padding:0 22px;">
                      <!-- hero image -->
                      @if(!empty($hero['photo']))
                        <img src="{{ $safe($hero['photo']) }}" alt="" width="596"
                             style="width:100%; max-width:596px; height:auto; display:block; border-radius:12px; background:#f3f4f6;">
                      @endif
                    </td>
                  </tr>

                  <tr>
                    <td style="padding:16px 22px 4px 22px;">
                      <div style="font-size:18px; font-weight:800; line-height:1.25; color:#111827;">
                        {{ $safe($hero['title_line'] ?? $hero['title'] ?? 'Property') }}
                      </div>
                      <div style="margin-top:6px; font-size:14px; line-height:1.45; color:#6b7280;">
                        {{ $safe($hero['location'] ?? '') }}
                        @if(!empty($hero['reference']))
                          <span style="color:#9ca3af;"> • Ref: {{ $safe($hero['reference']) }}</span>
                        @endif
                      </div>
                    </td>
                  </tr>

                  <tr>
                    <td style="padding:10px 22px 0 22px;">
                      <div style="font-size:20px; font-weight:900; color:#111827;">
                        {{ $money($hero['price'] ?? null) }}
                      </div>
                      @if(!empty($hero['desc']))
                        <div style="margin-top:10px; font-size:14px; line-height:1.6; color:#374151;">
                          {{ $safe($trimText($hero['desc'], 220)) }}
                        </div>
                      @endif
                    </td>
                  </tr>

                  <tr>
                    <td style="padding:16px 22px 22px 22px;">
                      <!-- CTA button -->
                      @php $heroUrl = $hero['url'] ?? null; @endphp
                      @if($heroUrl)
                        <a href="{{ $safe($heroUrl) }}"
                           style="display:inline-block; background:#111827; color:#ffffff; text-decoration:none; padding:12px 16px; border-radius:10px; font-weight:800; font-size:14px;">
                          View Full Details →
                        </a>
                      @endif
                    </td>
                  </tr>

                  <tr>
                    <td style="padding:0 22px 18px 22px;">
                      <div style="height:1px; background:#eef2f7;"></div>
                    </td>
                  </tr>
                </table>
              @endif

              <!-- Rest: grid cards -->
              @if(count($rest))
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                  <tr>
                    <td style="padding:0 22px 10px 22px;">
                      <div style="font-size:14px; font-weight:800; color:#111827;">
                        Other Recommendations
                      </div>
                      <div style="margin-top:4px; font-size:13px; color:#6b7280;">
                        Tap any property to view full details.
                      </div>
                    </td>
                  </tr>
                </table>

                <!-- two-column responsive: using table cells (email-safe) -->
                @php
                  $rows = array_chunk($rest, 2);
                @endphp

                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                  @foreach($rows as $row)
                    <tr>
                      @foreach($row as $card)
                        <td valign="top" width="50%" style="padding:10px 22px; {{ count($row) === 1 ? 'width:100%;' : '' }}">
                          <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                 style="border-collapse:collapse; border:1px solid #eef2f7; border-radius:12px; overflow:hidden;">
                            <tr>
                              <td style="background:#f3f4f6;">
                                @if(!empty($card['photo']))
                                  <img src="{{ $safe($card['photo']) }}" alt="" width="100%"
                                       style="width:100%; height:auto; display:block;">
                                @else
                                  <div style="height:140px;"></div>
                                @endif
                              </td>
                            </tr>
                            <tr>
                              <td style="padding:12px 12px 6px 12px;">
                                <div style="font-size:14px; font-weight:800; line-height:1.25; color:#111827;">
                                  {{ $safe($card['title_line'] ?? $card['title'] ?? 'Property') }}
                                </div>
                                <div style="margin-top:6px; font-size:12px; line-height:1.4; color:#6b7280;">
                                  {{ $safe($card['location'] ?? '') }}
                                  @if(!empty($card['reference']))
                                    <span style="color:#9ca3af;"> • Ref: {{ $safe($card['reference']) }}</span>
                                  @endif
                                </div>
                              </td>
                            </tr>
                            <tr>
                              <td style="padding:0 12px 12px 12px;">
                                <div style="font-size:14px; font-weight:900; color:#111827;">
                                  {{ $money($card['price'] ?? null) }}
                                </div>
                                @php $u = $card['url'] ?? null; @endphp
                                @if($u)
                                  <div style="margin-top:10px;">
                                    <a href="{{ $safe($u) }}"
                                       style="font-size:13px; font-weight:800; color:#2563eb; text-decoration:none;">
                                      View details →
                                    </a>
                                  </div>
                                @endif
                              </td>
                            </tr>
                          </table>
                        </td>
                      @endforeach

                      @if(count($row) === 1)
                        <!-- filler cell for table structure -->
                        <td width="50%" style="padding:10px 22px;"></td>
                      @endif
                    </tr>
                  @endforeach
                </table>
              @endif

              <!-- Footer note -->
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                <tr>
                  <td style="padding:18px 22px 22px 22px;">
                    <div style="font-size:13px; line-height:1.6; color:#6b7280;">
                      Want us to refine this list (area, type, bedrooms, or budget)? Just reply to this email and we’ll tailor it.
                    </div>
                    <div style="margin-top:10px; font-size:12px; line-height:1.6; color:#9ca3af;">
                      © {{ date('Y') }} Sunshine Luxury Villas
                    </div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Bottom spacing -->
          <tr>
            <td style="padding:18px 0;"></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  <!-- Mobile stack fix (works on most clients; safe to ignore if stripped) -->
  <style>
    @media screen and (max-width: 680px){
      table[width="640"]{ width:100% !important; }
      td[width="50%"]{ display:block !important; width:100% !important; }
    }
  </style>
</body>
</html>
