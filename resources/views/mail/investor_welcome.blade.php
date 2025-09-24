<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Welcome to The Cyprus Investor Club</title>
</head>
<body style="font-family:Arial,Helvetica,sans-serif; line-height:1.5; color:#111; margin:0; padding:0; background:#f7f7f7;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f7f7f7; padding:24px 0;">
    <tr>
      <td align="center">
        <table role="presentation" width="640" cellspacing="0" cellpadding="0" style="background:#fff; border-radius:8px; overflow:hidden;">
          <tr>
            <td style="background:#0b3a68; color:#fff; padding:24px 28px; font-size:20px; font-weight:700;">
              Welcome to The Cyprus Investor Club!
            </td>
          </tr>
          <tr>
            <td style="padding:24px 28px; font-size:15px; color:#222;">
              @if(!empty($name))
                <p style="margin:0 0 14px;">Hi {{ $name }},</p>
              @endif
              <p style="margin:0 0 12px;">
                <strong>Congratulations</strong> on becoming a member. You now have exclusive access to some of Cyprus’s most lucrative investment opportunities with the highest Return on Investment (ROI).
              </p>

              <p style="margin:18px 0 10px;"><strong>As a valued member, you will receive free:</strong></p>
              <ol style="margin:0 0 16px 20px; padding:0;">
                <li style="margin:6px 0;">
                  <strong>Repossession Alerts:</strong> Be the first to know about the newest repossessed properties before they’re listed on popular platforms such as Rightmove.
                </li>
                <li style="margin:6px 0;">
                  <strong>Regular Newsletters:</strong> In-depth coverage of the best investment opportunities, including:
                  <ul style="margin:8px 0 0 18px;">
                    <li>Long-term rentals: 7–11% Net ROI</li>
                    <li>Short-term (managed) rentals: 10–20% Net ROI</li>
                    <li>Commercial properties: 10%+ Net ROI</li>
                    <li>Distressed property flips: 20%+ Net ROI</li>
                    <li>Repossessed, unfinished developments: 20%+ Net ROI</li>
                  </ul>
                </li>
              </ol>

              <p style="margin:16px 0;">
                If you have any questions, need assistance, or would like to schedule a one-on-one voice or video call, please don’t hesitate to reach out by replying to this email.
              </p>

              <p style="margin:18px 0;">We look forward to a successful partnership!</p>

              <p style="margin:0 0 4px;"><strong>The Cyprus Investor Club</strong></p>
              <p style="margin:0 0 4px;">by SLV Estates LTD</p>
            </td>
          </tr>
        </table>

        <p style="font-size:12px; color:#666; margin:12px 0 0;">© {{ date('Y') }} Sunshine Luxury Villas</p>
      </td>
    </tr>
  </table>
</body>
</html>
