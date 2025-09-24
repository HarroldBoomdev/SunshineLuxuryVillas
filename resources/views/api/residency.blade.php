<section id="get-residency-header" class="mt-5">
    <h2>GET /api/sections/residency-header</h2>
    <p>Returns the page header info, including the banner title, Esme Palas's name, firm, and contact details.</p>

    <h5>Example Request</h5>
    <pre><code id="residency-header-request">curl -X GET "https://yourdomain.com/api/sections/residency-header" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="residency-header-request">Copy</button>
</section>

<section id="get-residency-video" class="mt-5">
    <h2>GET /api/sections/residency-video</h2>
    <p>Returns the embedded interview video URL, poster image, and headline/title of the video section.</p>

    <h5>Example Request</h5>
    <pre><code id="residency-video-request">curl -X GET "https://yourdomain.com/api/sections/residency-video" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="residency-video-request">Copy</button>
</section>

<section id="get-residency-faqs" class="mt-5">
    <h2>GET /api/residency-faqs</h2>
    <p>Returns the list of questions and answers shown in the accordion-style Residency Q&A section.</p>

    <h5>Example Response</h5>
    <pre><code>{
  "data": [
    {
      "question": "Are UK Nationals entitled to purchase property in Cyprus post-Brexit?",
      "answer": "Yes, they are. The law permits foreign nationals to buy up to 2 properties in Cyprus."
    },
    {
      "question": "Is there any difference in the purchase process post-Brexit?",
      "answer": "There are no major changes in the process. UK buyers follow the standard route."
    }
  ]
}</code></pre>
    <h5>Example Request</h5>
    <pre><code id="residency-faqs-request">curl -X GET "https://yourdomain.com/api/residency-faqs" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="residency-faqs-request">Copy</button>
</section>

<section id="get-residency-footer" class="mt-5">
    <h2>GET /api/sections/residency-footer</h2>
    <p>Returns Esme Palas's biography, credentials, and final contact details section at the bottom of the page.</p>

    <h5>Example Request</h5>
    <pre><code id="residency-footer-request">curl -X GET "https://yourdomain.com/api/sections/residency-footer" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="residency-footer-request">Copy</button>
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
