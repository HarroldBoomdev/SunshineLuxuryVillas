<section id="calculate-buying-fees" class="mt-5">
    <h2>POST /api/tools/buying-fees</h2>
    <p>Calculates total purchase taxes including transfer fees and stamp duty based on purchase price.</p>

    <h5>Example Request</h5>
    <pre><code id="fees-request">curl -X POST "https://yourdomain.com/api/tools/buying-fees" \
-H "Content-Type: application/json" \
-d '{ "purchase_price": 150000 }'</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="fees-request">Copy</button>

    <h5 class="mt-4">Try It Now</h5>
    <form id="try-buying-fees-form">
        <input type="number" name="purchase_price" class="form-control mb-2" placeholder="Enter purchase price (€)" required>
        <button type="submit" class="btn btn-primary btn-sm">Calculate</button>
        <pre id="fees-response" class="bg-light border p-2 mt-3 rounded small" style="display:none;"></pre>
    </form>
</section>

<section id="get-buying-bands" class="mt-5">
    <h2>GET /api/tools/buying-fees/bands</h2>
    <p>Returns the full breakdown of tax bands and percentage rates used in the calculator.</p>

    <h5>Example Request</h5>
    <pre><code id="bands-request">curl -X GET "https://yourdomain.com/api/tools/buying-fees/bands" \
-H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
    <button class="btn btn-outline-secondary btn-sm copy-btn" data-target="bands-request">Copy</button>
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

    document.getElementById('try-buying-fees-form')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const price = this.purchase_price.value;
        const res = await fetch('/api/tools/buying-fees', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ purchase_price: price })
        });
        const data = await res.json();
        const box = document.getElementById('fees-response');
        box.style.display = 'block';
        box.textContent = JSON.stringify(data, null, 2);
    });
</script>
