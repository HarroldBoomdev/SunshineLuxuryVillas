{{-- ===== Title Deed Images (uses DB column: titledeed ARRAY) ===== --}}
<div class="card p-3">
  <div class="form-group">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <label class="mb-0 fw-bold">Title Deed Images</label>
      <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#titleDeedModal">
        Manage
      </button>
    </div>

    {{-- Featured + thumbs preview --}}
    <div id="titleDeedPreview" class="d-flex border rounded overflow-hidden" style="height: 250px;">
      <div class="flex-grow-1 position-relative bg-light">
        <img src="" id="titleDeedFeatured" class="w-100 h-100 object-fit-cover d-none" />
        <span class="position-absolute bottom-0 end-0 m-2 bg-dark text-white small px-2 py-1 rounded" id="titleDeedCount">0</span>
      </div>
      <div class="d-flex flex-column gap-1 p-1" style="width: 150px;" id="titleDeedThumbs"></div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="titleDeedModal" tabindex="-1" aria-labelledby="titleDeedModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Title Deed Manager</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div class="mb-3 text-center border border-dashed p-4 position-relative" id="titleDeedDropZone" style="cursor: pointer;">
          <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
          <div>Drag or click to choose files</div>
          <div class="text-muted small">Maximum file size: 30MB</div>

          {{-- New uploads (files) --}}
          <input type="file" id="title_deed" name="title_deed[]" multiple accept="image/*"
                 style="position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;" />
        </div>

        {{-- Existing + new items grid (sortable) --}}
        <div id="titleDeedGrid" class="d-flex flex-wrap gap-2"></div>
      </div>

      <div class="modal-footer">
        <button type="button" id="titleDeedDone" class="btn btn-success" data-bs-dismiss="modal">Done</button>
      </div>
    </div>
  </div>
</div>

{{-- Hidden field that mirrors the ordered list of image paths (DB column: titledeed) --}}
<input type="hidden" id="titledeed_json" name="titledeed" value='@json(old("titledeed", $property->titledeed ?? []))' />

{{-- ===== Land (DB columns: regnum, section, plotnum, sheetPlan, titleDead, share) ===== --}}
<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm mt-4">
  <h3 class="mb-4 text-sm font-semibold text-gray-700">Land</h3>

  <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    {{-- Registration number -> regnum --}}
    <label class="block">
      <span class="text-xs font-medium text-gray-600">Registration number</span>
      <input
        type="text"
        name="regnum"
        value="{{ old('regnum', $property->regnum ?? '') }}"
        class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
      />
    </label>

    {{-- Section --}}
    <label class="block">
      <span class="text-xs font-medium text-gray-600">Section</span>
      <input
        type="text"
        name="section"
        value="{{ old('section', $property->section ?? '') }}"
        class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
      />
    </label>

    {{-- Plot number -> plotnum --}}
    <label class="block">
      <span class="text-xs font-medium text-gray-600">Plot number</span>
      <input
        type="text"
        name="plotnum"
        value="{{ old('plotnum', $property->plotnum ?? '') }}"
        class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
      />
    </label>

    {{-- Sheet/plan -> sheetPlan --}}
    <label class="block">
      <span class="text-xs font-medium text-gray-600">Sheet/plan</span>
      <input
        type="text"
        name="sheetPlan"
        value="{{ old('sheetPlan', $property->sheetPlan ?? '') }}"
        class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
      />
    </label>

    {{-- Title deed status -> titleDead --}}
    <label class="block">
      <span class="text-xs font-medium text-gray-600">Title deed</span>
      @php $titleDeed = old('titleDead', $property->titleDead ?? ''); @endphp
      <select
        name="titleDead"
        class="mt-1 w-full rounded-lg border-gray-300 bg-white text-sm focus:border-indigo-500 focus:ring-indigo-500"
      >
        <option value="" {{ $titleDeed==='' ? 'selected' : '' }}>-</option>
        <option value="available"  {{ $titleDeed==='available' ? 'selected' : '' }}>Available</option>
        <option value="in_process" {{ $titleDeed==='in_process' ? 'selected' : '' }}>In process</option>
        <option value="no_title"   {{ $titleDeed==='no_title' ? 'selected' : '' }}>No title deed</option>
      </select>
    </label>

    {{-- Share --}}
    <label class="block">
      <span class="text-xs font-medium text-gray-600">Share</span>
      <input
        type="text"
        name="share"
        value="{{ old('share', $property->share ?? '') }}"
        class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
      />
    </label>
  </div>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const MAX_FILES = 30;
  const MAX_SIZE_MB = 30; // matches the label in UI

  const input     = document.getElementById("title_deed");
  const grid      = document.getElementById("titleDeedGrid");
  const featured  = document.getElementById("titleDeedFeatured");
  const count     = document.getElementById("titleDeedCount");
  const thumbs    = document.getElementById("titleDeedThumbs");
  const doneBtn   = document.getElementById("titleDeedDone");
  const dropZone  = document.getElementById("titleDeedDropZone");
  const hidden    = document.getElementById("titledeed_json");

  // Load existing images (paths) from hidden input (JSON)
  let images = [];
  try {
    const existing = JSON.parse(hidden.value || "[]");
    // Normalize existing: store as {name, src, persisted:true}
    images = (existing || []).map(src => ({ name: src.split('/').pop(), src, persisted: true }));
  } catch(e) { images = []; }

  // Initial render for edit screen
  renderImages();
  paintPreview();

  input.addEventListener("change", () => {
    const files = Array.from(input.files);

    if (files.length + images.length > MAX_FILES) {
      alert(`Maximum ${MAX_FILES} images allowed.`);
      return;
    }

    files.forEach(file => {
      if (file.size / 1024 / 1024 > MAX_SIZE_MB) {
        alert(`${file.name} exceeds ${MAX_SIZE_MB}MB and was skipped.`);
        return;
      }

      const reader = new FileReader();
      reader.onload = function (e) {
        images.push({ name: file.name, src: e.target.result, persisted: false });
        renderImages();
      };
      reader.readAsDataURL(file);
    });

    input.value = "";
  });

  function renderImages() {
    grid.innerHTML = "";

    images.forEach((img, index) => {
      const wrap = document.createElement("div");
      wrap.className = "position-relative border rounded overflow-hidden";
      wrap.style.width = "110px";
      wrap.style.height = "110px";
      wrap.style.cursor = "move";
      wrap.dataset.index = index;

      const image = document.createElement("img");
      image.src = img.src;
      image.className = "img-fluid h-100 w-100 object-fit-cover";

      // Small badge if already saved
      if (img.persisted) {
        const badge = document.createElement("span");
        badge.className = "position-absolute top-0 end-0 m-1 badge bg-secondary";
        badge.textContent = "saved";
        wrap.appendChild(badge);
      }

      wrap.appendChild(image);
      grid.appendChild(wrap);
    });

    // Update hidden JSON with ONLY persisted URLs + any server-returned later.
    // For now, keep existing persisted order so edit view posts correct order.
    hidden.value = JSON.stringify(images.filter(i => i.persisted).map(i => i.src));
  }

  new Sortable(grid, {
    animation: 150,
    onEnd() {
      const reordered = [...grid.querySelectorAll("img")].map(img => img.src);
      images = reordered.map(src => images.find(i => i.src === src)).filter(Boolean);
      renderImages();
      paintPreview();
    }
  });

  doneBtn.addEventListener("click", paintPreview);

  function paintPreview() {
    if (!images.length) {
      featured.classList.add("d-none");
      featured.src = "";
      count.innerText = "0";
      thumbs.innerHTML = "";
      return;
    }

    featured.src = images[0].src;
    featured.classList.remove("d-none");
    count.innerText = images.length.toString();

    thumbs.innerHTML = "";
    images.slice(1, 4).forEach(img => {
      const thumb = document.createElement("img");
      thumb.src = img.src;
      thumb.className = "img-fluid rounded object-fit-cover";
      thumb.style.height = "75px";
      thumb.style.width = "100%";
      thumbs.appendChild(thumb);
    });
  }

  // Drag and Drop
  dropZone.addEventListener("dragover", e => {
    e.preventDefault();
    dropZone.classList.add("bg-light");
  });
  dropZone.addEventListener("dragleave", () => dropZone.classList.remove("bg-light"));
  dropZone.addEventListener("drop", e => {
    e.preventDefault();
    dropZone.classList.remove("bg-light");
    input.files = e.dataTransfer.files;
    input.dispatchEvent(new Event("change"));
  });

  // Reset
  const form = document.querySelector("form");
  if (form) {
    form.addEventListener("reset", () => {
      images = [];
      grid.innerHTML = "";
      thumbs.innerHTML = "";
      featured.src = "";
      featured.classList.add("d-none");
      count.innerText = "0";
      input.value = "";
      hidden.value = "[]";
    });
  }
});
</script>
