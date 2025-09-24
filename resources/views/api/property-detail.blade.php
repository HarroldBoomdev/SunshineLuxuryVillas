<section id="get-property-detail" class="mt-5">
    <h2>GET /api/properties/{slug}</h2>
    <p>Returns full property details for a specific listing including price, specifications, features, media, and embedded map.</p>

    <h5>Path Parameter</h5>
    <ul>
        <li><code>{slug}</code> — Unique property identifier, usually based on the title (e.g. <code>coastal-commercial-property</code>)</li>
    </ul>

    <h5>Example Request</h5>
    <pre><code id="property-detail-request">curl -X GET "https://yourdomain.com/api/properties/coastal-commercial-property" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="property-detail-request">Copy</button>

    <h5>Example Response</h5>
    <pre><code>{
  "title": "Coastal commercial property with water and countryside views on Nature Reserve",
  "reference": "1175",
  "price": 1250000,
  "location": "South West, Dorset",
  "category": "Brand New",
  "status": "Resale",
  "type": "Commercial Property",
  "floor": 2,
  "orientation": "South",
  "furnished": "Optional furnished",
  "year_built": 1999,
  "structure": "Concrete",
  "energy_rating": "D",
  "covered_area": "578m²",
  "plot_area": "1457m²",
  "title_deed": "Yes",
  "description": "<p>This stunning home is over 2,000 sqft...</p>",
  "photos": [
    "/uploads/properties/1175/1.jpg",
    "/uploads/properties/1175/2.jpg"
  ],
  "facilities": [
    "Aircondition, Split system",
    "Heating, Central, Independent"
  ],
  "features": [
    "Alarm system",
    "Country view",
    "Reception",
    "Sea front"
  ],
  "distances": {
    "amenities": "2 km",
    "sea": "10 m"
  },
  "qr_code": "/qr/1175.png",
  "virtual_tour": "https://virtualtour.com/view/1175",
  "map_embed": "<iframe src='https://maps.google.com/...'></iframe>"
}</code></pre>
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
