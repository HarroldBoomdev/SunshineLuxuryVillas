<section id="get-properties-list" class="mt-5">
    <h2>GET /api/properties</h2>
    <p>Returns a paginated list of properties with optional filters such as region, type, price, size, and bedrooms.</p>

    <h5>Query Parameters</h5>
    <ul>
        <li><code>category</code> – e.g. Resale, Brand New</li>
        <li><code>region</code> – e.g. Limassol</li>
        <li><code>type</code> – e.g. Apartment, Villa</li>
        <li><code>bedrooms</code></li>
        <li><code>min_price</code> / <code>max_price</code></li>
        <li><code>plot_size</code> / <code>area_size</code></li>
        <li><code>reference</code></li>
        <li><code>page</code></li>
    </ul>

    <h5>Example Request</h5>
    <pre><code id="properties-list-request">curl -X GET "https://yourdomain.com/api/properties?region=Limassol&bedrooms=2&page=1" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="properties-list-request">Copy</button>

    <h5>Example Response</h5>
    <pre><code>{
  "data": [
    {
      "title": "Apartment in Limassol",
      "price": 265000,
      "bedrooms": 2,
      "bathrooms": 2,
      "size": "123m²",
      "cover_image": "/uploads/property1.jpg",
      "reference": "LIM265000",
      "location": "Limassol",
      "slug": "apartment-in-limassol"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "total": 1086
  }
}</code></pre>
</section>

<section id="get-filters" class="mt-5">
    <h2>GET /api/filters</h2>
    <p>Returns all available options for the property search filters.</p>

    <h5>Example Request</h5>
    <pre><code id="filters-request">curl -X GET "https://yourdomain.com/api/filters" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="filters-request">Copy</button>

    <h5>Example Response</h5>
    <pre><code>{
  "categories": ["Resale", "Brand New"],
  "regions": ["Paphos", "Limassol"],
  "types": ["Apartment", "Villa", "Plot"],
  "bedrooms": [1, 2, 3, 4, 5],
  "prices": {
    "min": 50000,
    "max": 3000000
  }
}</code></pre>
</section>

<section id="get-featured-properties" class="mt-5">
    <h2>GET /api/properties/featured</h2>
    <p>Returns a list of properties marked as "featured", displayed in the sidebar.</p>

    <h5>Example Request</h5>
    <pre><code id="featured-request">curl -X GET "https://yourdomain.com/api/properties/featured" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="featured-request">Copy</button>
</section>

<section id="get-recent-properties" class="mt-5">
    <h2>GET /api/properties/recent</h2>
    <p>Returns the most recently added properties, shown in the sidebar.</p>

    <h5>Example Request</h5>
    <pre><code id="recent-request">curl -X GET "https://yourdomain.com/api/properties/recent" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="recent-request">Copy</button>
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
