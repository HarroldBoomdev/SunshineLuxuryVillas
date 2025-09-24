<section id="get-blogs-list" class="mt-5">
    <h2>GET /api/blogs</h2>
    <p>Returns a paginated list of blog posts, optionally filterable by category, search keyword, or sort options.</p>

    <h5>Query Parameters</h5>
    <ul>
        <li><code>search</code> — Filter by keyword in title or content</li>
        <li><code>category</code> — Filter by category (e.g. investment)</li>
        <li><code>sort</code> — Sort by latest, oldest, etc.</li>
        <li><code>page</code> — Pagination page number</li>
    </ul>

    <h5>Example Request</h5>
    <pre><code id="blogs-list-request">curl -X GET "https://yourdomain.com/api/blogs?page=1&sort=latest" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="blogs-list-request">Copy</button>

    <h5>Example Response</h5>
    <pre><code>{
  "data": [
    {
      "title": "The Ultimate Selecting Guide Commercial",
      "slug": "ultimate-guide-commercial",
      "excerpt": "Lorem ipsum dolor sit amet...",
      "cover": "/images/blogs/guide.jpg",
      "read_time": "7 min read"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5
  }
}</code></pre>
</section>

<section id="get-single-blog" class="mt-5">
    <h2>GET /api/blogs/{slug}</h2>
    <p>Returns the full content of a single blog post based on the slug (unique URL identifier).</p>

    <h5>Example Request</h5>
    <pre><code id="blog-detail-request">curl -X GET "https://yourdomain.com/api/blogs/ultimate-guide-commercial" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="blog-detail-request">Copy</button>

    <h5>Example Response</h5>
    <pre><code>{
  "title": "The Ultimate Selecting Guide Commercial",
  "slug": "ultimate-guide-commercial",
  "cover": "/images/blogs/guide.jpg",
  "read_time": "7 min read",
  "content": "<p>This is the full HTML content of the blog post...</p>",
  "created_at": "2025-06-11"
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
