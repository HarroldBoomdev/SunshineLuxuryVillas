<section id="get-investor-intro" class="mt-5">
    <h2>GET /api/sections/investor-club-intro</h2>
    <p>Returns the headline, subheadline, and descriptive text for the Investor Club page.</p>

    <h5>Example Request</h5>
    <pre><code id="investor-intro-request">curl -X GET "https://yourdomain.com/api/sections/investor-club-intro" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="investor-intro-request">Copy</button>
</section>

<section id="get-investor-features" class="mt-5">
    <h2>GET /api/sections/investor-club-features</h2>
    <p>Returns a list of highlighted features (cards with icon, title, description) that explain benefits of joining the Investor Club.</p>

    <h5>Example Request</h5>
    <pre><code id="investor-features-request">curl -X GET "https://yourdomain.com/api/sections/investor-club-features" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="investor-features-request">Copy</button>
</section>

<section id="post-investor-form" class="mt-5">
    <h2>POST /api/leads/investor-club</h2>
    <p>Submits a user's request to join the investor club, including name, email, phone, and message.</p>

    <h5>Example Request</h5>
    <pre><code id="investor-post-request">curl -X POST "https://yourdomain.com/api/leads/investor-club" \
-H "Content-Type: application/json" \
-d '{
  "name": "Investor Name",
  "email": "investor@example.com",
  "phone": "+357 123456789",
  "message": "I am interested in investment properties."
}'</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="investor-post-request">Copy</button>
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
