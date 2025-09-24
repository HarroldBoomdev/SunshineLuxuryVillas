<section id="get-about-us" class="mt-5">
    <h2>GET /api/sections/about-us</h2>
    <p>Returns the welcome headline, intro video URL, and introductory text for the About Us page.</p>

    <h5>Example Request</h5>
    <pre><code id="aboutus-request">curl -X GET "https://yourdomain.com/api/sections/about-us" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="aboutus-request">Copy</button>
</section>

<section id="get-paul-story" class="mt-5">
    <h2>GET /api/sections/paul-story</h2>
    <p>Returns the story section content about Paul, including image, description, and video/article link.</p>

    <h5>Example Request</h5>
    <pre><code id="paul-request">curl -X GET "https://yourdomain.com/api/sections/paul-story" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="paul-request">Copy</button>
</section>

<section id="get-about-statistics" class="mt-5">
    <h2>GET /api/statistics</h2>
    <p>Returns company statistics including years of service, buyers assisted, and property value sold.</p>

    <h5>Example Request</h5>
    <pre><code id="aboutstats-request">curl -X GET "https://yourdomain.com/api/statistics" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="aboutstats-request">Copy</button>
</section>

<section id="get-our-team" class="mt-5">
    <h2>GET /api/sections/our-team</h2>
    <p>Returns the content and image/video of the team section on the About Us page.</p>

    <h5>Example Request</h5>
    <pre><code id="team-request">curl -X GET "https://yourdomain.com/api/sections/our-team" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="team-request">Copy</button>
</section>

<section id="get-legal-guidance" class="mt-5">
    <h2>GET /api/sections/legal-guidance</h2>
    <p>Returns the content and image for the “Expert Legal & Financial Guidance” section.</p>

    <h5>Example Request</h5>
    <pre><code id="legal-request">curl -X GET "https://yourdomain.com/api/sections/legal-guidance" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="legal-request">Copy</button>
</section>

<section id="get-support-flow" class="mt-5">
    <h2>GET /api/sections/support-flow</h2>
    <p>Returns bullet point support content shown in the “Start-to-Finish Support” section.</p>

    <h5>Example Request</h5>
    <pre><code id="supportflow-request">curl -X GET "https://yourdomain.com/api/sections/support-flow" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="supportflow-request">Copy</button>
</section>

<section id="get-about-testimonials" class="mt-5">
    <h2>GET /api/testimonials</h2>
    <p>Returns client testimonials (reused from homepage) displayed under “Our Clients' Experience”.</p>

    <h5>Example Request</h5>
    <pre><code id="abouttestimonials-request">curl -X GET "https://yourdomain.com/api/testimonials" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="abouttestimonials-request">Copy</button>
</section>

<section id="get-free-access" class="mt-5">
    <h2>GET /api/features/free-access</h2>
    <p>Returns a list of features shown at the bottom of the About page (Investor Club, Fees Calculator, Currency Options).</p>

    <h5>Example Request</h5>
    <pre><code id="freeaccess-request">curl -X GET "https://yourdomain.com/api/features/free-access" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="freeaccess-request">Copy</button>
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
