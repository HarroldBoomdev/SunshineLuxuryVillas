<section id="get-repossessions-info" class="mt-5">
    <h2>GET /api/sections/repossessions-info</h2>
    <p>Returns the headline, intro paragraph, and key bullet points about the Cyprus repossession process.</p>

    <h5>Example Request</h5>
    <pre><code id="reposs-info-request">curl -X GET "https://yourdomain.com/api/sections/repossessions-info" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="reposs-info-request">Copy</button>
</section>

<section id="get-reposs-properties" class="mt-5">
    <h2>GET /api/properties/repossessions</h2>
    <p>Returns a list of featured repossession properties with image, title, and location.</p>

    <h5>Example Request</h5>
    <pre><code id="reposs-prop-request">curl -X GET "https://yourdomain.com/api/properties/repossessions" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="reposs-prop-request">Copy</button>
</section>

<section id="get-reposs-guidelines" class="mt-5">
    <h2>GET /api/sections/repossessions-guidelines</h2>
    <p>Returns decision timelines, tender rules, and advisory text for buyers.</p>

    <h5>Example Request</h5>
    <pre><code id="reposs-guide-request">curl -X GET "https://yourdomain.com/api/sections/repossessions-guidelines" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="reposs-guide-request">Copy</button>
</section>

<section id="get-investor-invite" class="mt-5">
    <h2>GET /api/sections/investor-invite</h2>
    <p>Returns the text and call-to-action for inviting users to join the Cyprus Investor Club.</p>

    <h5>Example Request</h5>
    <pre><code id="investor-invite-request">curl -X GET "https://yourdomain.com/api/sections/investor-invite" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="investor-invite-request">Copy</button>
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
