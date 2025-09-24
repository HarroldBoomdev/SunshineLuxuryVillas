<section id="get-properties" class="mt-5">
    <h2>GET /api/properties</h2>
    <p>Retrieves a paginated list of properties with optional filters (e.g. resale, region, town, property type).</p>

    <h5>Example Request</h5>
    <pre><code id="properties-request">curl -X GET "https://yourdomain.com/api/properties?page=1"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="properties-request">Copy</button>

    <h5 class="mt-4">Try It Now</h5>
    <form id="try-properties-form">
        <input type="text" name="reference" class="form-control mb-2" placeholder="Reference (optional)">
        <button type="submit" class="btn btn-primary btn-sm">Send Request</button>
        <pre id="properties-response" class="bg-light border p-2 mt-3 rounded small" style="display:none;"></pre>
    </form>
</section>

<section id="get-map" class="mt-5">
    <h2>GET /api/properties/map</h2>
    <p>Returns properties with <code>latitude</code>, <code>longitude</code>, and <code>photo</code> fields for map display.</p>

    <h5>Example Request</h5>
    <pre><code id="map-request">curl -X GET "https://yourdomain.com/api/properties/map"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="map-request">Copy</button>

    <h5 class="mt-4">Try It Now</h5>
    <form id="try-map-form">
        <button type="submit" class="btn btn-success btn-sm">Load Map Data</button>
        <pre id="map-response" class="bg-light border p-2 mt-3 rounded small" style="display:none;"></pre>
    </form>
</section>

<section id="get-statistics" class="mt-5">
    <h2>GET /api/sections/statistics</h2>
    <p>Returns key performance metrics such as years of experience, number of buyers assisted, and property value sold.</p>

    <h5>Example Request</h5>
    <pre><code id="stats-request">curl -X GET "https://yourdomain.com/api/sections/statistics"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="stats-request">Copy</button>
</section>

<section id="get-about-home" class="mt-5">
    <h2>GET /api/sections/about-home</h2>
    <p>Returns the “About SLV Estates Cyprus” and “Why Choose SLV” content shown on the homepage. This data is fully editable from the backend CMS and returned as decoded JSON.</p>

    <h5>Example Request</h5>
    <pre><code id="about-home-request">curl -X GET "https://yourdomain.com/api/sections/about-home"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="about-home-request">Copy</button>
</section>

<section id="get-testimonials" class="mt-5">
    <h2>GET /api/sections/testimonials</h2>
    <p>Returns client testimonials with names, star ratings, and quotes.</p>

    <h5>Example Request</h5>
    <pre><code id="testimonials-request">curl -X GET "https://yourdomain.com/api/sections/testimonials"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="testimonials-request">Copy</button>
</section>

<section id="get-top-properties" class="mt-5">
    <h2>GET /api/properties/top</h2>
    <p>Returns the list of top 12 featured properties (used for carousel display).</p>

    <h5>Example Request</h5>
    <pre><code id="top-prop-request">curl -X GET "https://yourdomain.com/api/properties/top"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="top-prop-request">Copy</button>
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

    // Try Now handlers
    document.getElementById('try-properties-form')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const ref = this.reference.value;
        const res = await fetch(`/api/properties${ref ? '?reference=' + ref : ''}`);
        const data = await res.json();
        const box = document.getElementById('properties-response');
        box.style.display = 'block';
        box.textContent = JSON.stringify(data, null, 2);
    });

    document.getElementById('try-map-form')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const res = await fetch(`/api/properties/map`);
        const data = await res.json();
        const box = document.getElementById('map-response');
        box.style.display = 'block';
        box.textContent = JSON.stringify(data, null, 2);
    });
</script>
