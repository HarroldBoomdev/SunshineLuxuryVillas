<section id="get-legal-header" class="mt-5">
    <h2>GET /api/sections/legal-partners-header</h2>
    <p>Returns the heading text for the Trusted Legal Partners page.</p>

    <h5>Example Request</h5>
    <pre><code id="legal-header-request">curl -X GET "https://yourdomain.com/api/sections/legal-partners-header" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="legal-header-request">Copy</button>
</section>

<section id="get-legal-partners" class="mt-5">
    <h2>GET /api/legal-partners</h2>
    <p>Returns a list of regions, each containing a background image and associated law firms with logos.</p>

    <h5>Example Response</h5>
    <pre><code>{
  "data": [
    {
      "region": "Paphos",
      "image": "/images/regions/paphos.jpg",
      "firms": [
        {
          "name": "Michael Kyprianou",
          "logo": "/logos/kyprianou.png"
        },
        {
          "name": "Andreas Demetriades & Co LLC",
          "logo": "/logos/andreas.png"
        }
      ]
    },
    {
      "region": "Limassol",
      "image": "/images/regions/limassol.jpg",
      "firms": [
        {
          "name": "L.G. Zambartas LLC",
          "logo": "/logos/zambartas.png"
        },
        {
          "name": "Polymage",
          "logo": "/logos/polymage.png"
        }
      ]
    }
  ]
}</code></pre>

    <h5>Example Request</h5>
    <pre><code id="legal-partners-request">curl -X GET "https://yourdomain.com/api/legal-partners" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="legal-partners-request">Copy</button>
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
