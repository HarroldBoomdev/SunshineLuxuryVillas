<!-- Tabs Container -->
<div class="container mt-3" id="tabContainer" style="display: none;">
    <ul class="nav nav-tabs" id="pageTabs"></ul>
</div>

<div class="tab-content mt-2" id="tabContent"></div>

<!-- Top Controls: Search, New Dropdown, Actions Menu -->
<div class="d-flex justify-content-end align-items-center mb-3">
    <!-- Search Field -->
    <div class="me-2">
        <input type="text" id="search-input" class="form-control" placeholder="Search..." style="width: 200px;">
    </div>

    <!-- + New Dropdown -->
    <div class="dropdown me-2">
        <button class="btn btn-success dropdown-toggle" type="button" id="newDropdownButton" data-bs-toggle="dropdown" aria-expanded="false">
            + New
        </button>
        <ul class="dropdown-menu" aria-labelledby="newDropdownButton">
            @can('property.create')
                <a class="dropdown-item" href="{{ route('properties.create') }}">Property</a>
            @endcan
            @can('client.create')
                <a class="dropdown-item" href="{{ route('clients.create') }}">Client</a>
            @endcan
            @can('developer.create')
                <a class="dropdown-item" href="{{ route('developers.create') }}">Developer</a>
            @endcan
            @can('agent.create')
                <a class="dropdown-item" href="{{ route('agents.create') }}">Agent</a>
            @endcan
            @can('deal.create')
                <a class="dropdown-item" href="#">Deal</a>
            @endcan
            @can('diary.create')
                <a class="dropdown-item" href="#">Diary</a>
            @endcan
            @can('match.create')
                <a class="dropdown-item" href="#">Match</a>
            @endcan
            @can('report.create')
                <a class="dropdown-item" href="#">Report</a>
            @endcan
        </ul>
    </div>


</div>


<!-- Channel Manager Modal -->
<div class="modal fade" id="channelManagerModal" tabindex="-1" aria-labelledby="channelManagerLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="channelManagerLabel">Channel Manager</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Alert Message -->
      <div class="alert alert-info rounded-0 m-0 px-4 py-2" role="alert">
        It may take some time until the property is published on certain websites.
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        <!-- Website Checkbox -->
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" value="" id="websiteCheckbox">
          <label class="form-check-label" for="websiteCheckbox">Website</label>
          <span id="featuredBadge" class="badge bg-warning text-dark ms-2 d-none">Featured</span>
        </div>

        <!-- Integrations Checkbox -->
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" value="" id="integrationCheckbox">
          <label class="form-check-label" for="integrationCheckbox">Integrations</label>
        </div>

        <!-- Integrations List (hidden by default) -->
        <div id="integrationList" class="border rounded p-2 mt-2" style="max-height: 200px; overflow-y: auto; display: none;">
          @foreach([
            'Akinia', 'Rightmove', 'Zoopla', 'A Place In The Sun', 'Bazaraki', 'James Edition'
          ] as $channel)
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="integration-{{ Str::slug($channel) }}">
              <label class="form-check-label" for="integration-{{ Str::slug($channel) }}">{{ $channel }}</label>
            </div>
          @endforeach
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success">Save</button>
      </div>

    </div>
  </div>
</div>

<!-- PDF Modal -->
<div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="pdfModalLabel">PDF</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        <!-- Template Dropdown -->
        <div class="mb-3">
          <label for="templateSelect" class="form-label">Template</label>
          <select class="form-select" id="templateSelect">
            <option selected>Comprehensive</option>
            <option>Classic portrait</option>
            <option>Classic landscape</option>
            <option>Modern portrait</option>
            <option>Modern landscape</option>
          </select>
        </div>

        <!-- Agent Dropdown (with avatar) -->
        <div class="mb-3">
          <label for="agentSelect" class="form-label">Agent details</label>
          <select class="form-select" id="agentSelect">
            <option selected>
              🧑 Sofia Andreasson
            </option>
            <option>👨 Thomas Harrison</option>
            <option>👩 Jane Doe</option>
          </select>
        </div>

        <!-- Language Dropdown -->
        <div class="mb-3">
          <label for="languageSelect" class="form-label">Language</label>
          <select class="form-select" id="languageSelect">
            <option selected>English</option>
            <option>French</option>
            <option>German</option>
            <option>Spanish</option>
          </select>
        </div>

        <!-- White Label Checkbox -->
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="" id="whiteLabelCheckbox">
          <label class="form-check-label" for="whiteLabelCheckbox">
            White label (branding free)
          </label>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success">
          <i class="bi bi-download me-1"></i> Download
        </button>
      </div>

    </div>
  </div>
</div>


<!-- Share Modal -->
<div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="shareModalLabel">Share</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        <div class="row mb-3">
          <div class="col-md-6">
            <label for="shareClient" class="form-label">Client</label>
            <select id="shareClient" class="form-select">
              <option selected disabled>-</option>
              <option>Thomas Harrison</option>
              <option>Sofia Andreasson</option>
            </select>
          </div>
          <div class="col-md-6">
            <label for="shareLanguage" class="form-label">Language</label>
            <select id="shareLanguage" class="form-select">
              <option selected disabled>-</option>
              <option>English</option>
              <option>French</option>
              <option>German</option>
            </select>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label for="shareEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="shareEmail" placeholder="client@example.com">
          </div>
          <div class="col-md-6">
            <label for="shareMobile" class="form-label">Mobile</label>
            <div class="input-group">
              <span class="input-group-text">+44</span>
              <input type="text" class="form-control" id="shareMobile" placeholder="7123456789">
            </div>
          </div>
        </div>

        <!-- Method Selection -->
            <div class="mb-3">
            <label class="form-label">Method</label>

            <!-- Radio Options -->
            <div class="d-flex gap-3 mb-3">
                @foreach(['Email', 'SMS', 'WhatsApp', 'Viber'] as $method)
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="shareMethod" id="shareMethod{{ $method }}" value="{{ strtolower($method) }}" {{ $loop->first ? 'checked' : '' }}>
                    <label class="form-check-label" for="shareMethod{{ $method }}">{{ $method }}</label>
                </div>
                @endforeach
            </div>

            <!-- Shared Message Box -->
            <div id="shareMessageWrapper" class="border rounded p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                <strong id="selectedMethodLabel">Email</strong>
                <small class="text-muted">Not available</small>
                </div>

                <textarea class="form-control mb-2" rows="4" placeholder="Your message"></textarea>

                <div class="d-flex justify-content-between align-items-center">
                <button class="btn btn-outline-secondary btn-sm" disabled>Generate with AI</button>
                <small class="text-muted">What is this?</small>
                </div>
            </div>
            </div>
      </div>

      <!-- Modal Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success">
          <i class="bi bi-send-fill me-1"></i> Share
        </button>
      </div>

    </div>
  </div>
</div>





<!-- JavaScript for Tab Management -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabContainer = document.getElementById("tabContainer");
    const tabNav = document.getElementById("pageTabs");
    const tabContent = document.getElementById("tabContent");

    window.openTab = function (title, url) {
        let tabId = title.replace(/\s+/g, '-').toLowerCase();

        // Show tab container
        tabContainer.style.display = "block";

        // Check if tab already exists
        if (document.getElementById(`tab-${tabId}`)) {
            new bootstrap.Tab(document.querySelector(`#tab-${tabId}-link`)).show();
            return;
        }

        // Create new tab
        const newTab = document.createElement("li");
        newTab.classList.add("nav-item");
        newTab.innerHTML = `
            <a class="nav-link" id="tab-${tabId}-link" data-bs-toggle="tab" href="#tab-${tabId}">
                ${title}
                <span class="ms-2 text-danger" style="cursor:pointer;" onclick="closeTab('${tabId}')">&times;</span>
            </a>`;
        tabNav.appendChild(newTab);

        // Create tab content
        const newTabContent = document.createElement("div");
        newTabContent.classList.add("tab-pane", "fade");
        newTabContent.id = `tab-${tabId}`;
        newTabContent.innerHTML = `<div class="text-center mt-3">Loading...</div>`;
        tabContent.appendChild(newTabContent);

        // Show tab
        new bootstrap.Tab(document.querySelector(`#tab-${tabId}-link`)).show();

        // Fetch page content
        fetch(url)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const content = doc.querySelector('.content-wrapper');
                newTabContent.innerHTML = content ? content.innerHTML : "<p>Error: Content not found.</p>";
            })
            .catch(() => {
                newTabContent.innerHTML = "<p>Error loading content.</p>";
            });
    };

    window.closeTab = function (tabId) {
        const tab = document.getElementById(`tab-${tabId}`);
        const link = document.querySelector(`#tab-${tabId}-link`);
        if (tab) tab.remove();
        if (link) link.parentElement.remove();

        const remainingTabs = tabNav.querySelectorAll("li");
        if (remainingTabs.length === 0) {
            tabContainer.style.display = "none";
        } else {
            new bootstrap.Tab(remainingTabs[0].querySelector("a")).show();
        }
    };
});


document.addEventListener('DOMContentLoaded', function () {
    const websiteCheckbox = document.getElementById('websiteCheckbox');
    const featuredBadge = document.getElementById('featuredBadge');
    const integrationCheckbox = document.getElementById('integrationCheckbox');
    const integrationList = document.getElementById('integrationList');

    websiteCheckbox.addEventListener('change', function () {
        featuredBadge.classList.toggle('d-none', !this.checked);
    });

    integrationCheckbox.addEventListener('change', function () {
        integrationList.style.display = this.checked ? 'block' : 'none';
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const dropdownBtn = document.getElementById('templateDropdown');
    const radios = document.querySelectorAll('input[name="pdfTemplate"]');

    radios.forEach(radio => {
        radio.addEventListener('change', function () {
            dropdownBtn.textContent = this.value;
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
  const methodRadios = document.querySelectorAll('input[name="shareMethod"]');
  const label = document.getElementById('selectedMethodLabel');

  methodRadios.forEach(radio => {
    radio.addEventListener('change', () => {
      label.textContent = radio.labels[0].innerText;
    });
  });
});

</script>
