<section id="get-sell-header" class="mt-5">
    <h2>GET /api/sections/sell-header</h2>
    <p>Returns the hero section for the "Why Sell With SLV?" page including headline, subheadline, video URL, and banner image.</p>

    <h5>Example Request</h5>
    <pre><code id="sell-header-request">curl -X GET "https://yourdomain.com/api/sections/sell-header" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="sell-header-request">Copy</button>
</section>

<section id="get-sell-reasons" class="mt-5">
    <h2>GET /api/sections/sell-reasons</h2>
    <p>Returns the title and bullet point reasons for why sellers should choose SLV.</p>

    <h5>Example Request</h5>
    <pre><code id="sell-reasons-request">curl -X GET "https://yourdomain.com/api/sections/sell-reasons" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="sell-reasons-request">Copy</button>
</section>

<section id="get-sell-services" class="mt-5">
    <h2>GET /api/sections/sell-services</h2>
    <p>Returns the service grid section showing different promotional and marketing features provided by SLV.</p>

    <h5>Example Request</h5>
    <pre><code id="sell-services-request">curl -X GET "https://yourdomain.com/api/sections/sell-services" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="sell-services-request">Copy</button>
</section>

<section id="get-sell-pitch" class="mt-5">
    <h2>GET /api/sections/sell-pitch</h2>
    <p>Returns the closing blue-banner pitch with headline, subheadline, and marketing description about SLV expertise.</p>

    <h5>Example Request</h5>
    <pre><code id="sell-pitch-request">curl -X GET "https://yourdomain.com/api/sections/sell-pitch" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="sell-pitch-request">Copy</button>
</section>

<section id="post-sell-request" class="mt-5">
    <h2>POST /api/leads/sell-request</h2>
    <p>Submits a lead from a seller, including name, email, phone, and property details.</p>

    <h5>Example Request</h5>
    <pre><code id="sell-lead-request">curl -X POST "https://yourdomain.com/api/leads/sell-request" \
-H "Content-Type: application/json" \
-d '{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+357 99 999999",
  "message": "3-bedroom villa in Paphos, sea view."
}'</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="sell-lead-request">Copy</button>
</section>

<script>
    document.querySelectorAll('.copy-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const text = document.getElementById(this.dataset.target).textContent;
            navigator.clipboard.writeText(text);
            this.innerText = 'Copied!';
            setTimeout(() => this.innerText = 'Copy', 2000);
        });
    });
</script>
