<div class="card p-3">
  <div class="form-group">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <label class="mb-0 fw-bold">Floor Plans</label>
      <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#floorPlansModal">
        Manage
      </button>
    </div>

    <div id="floorPlansPreview" class="d-flex border rounded overflow-hidden" style="height: 250px;">
      <div class="flex-grow-1 position-relative bg-light">
        <img src="" id="floorPlansFeatured" class="w-100 h-100 object-fit-cover d-none" />
        <span class="position-absolute bottom-0 end-0 m-2 bg-dark text-white small px-2 py-1 rounded" id="floorPlansCount">0</span>
      </div>
      <div class="d-flex flex-column gap-1 p-1" style="width: 150px;" id="floorPlansThumbs"></div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="floorPlansModal" tabindex="-1" aria-labelledby="floorPlansModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Manage Floor Plans</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3 text-center border border-dashed p-4 position-relative" id="floorPlansDropZone" style="cursor: pointer;">
          <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
          <div>Drag or click to choose files</div>
          <div class="text-muted small">Maximum file size: 30MB</div>
          <input type="file" id="floor_plans" name="floor_plans[]" multiple accept="image/*"
                 style="position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;" />
        </div>
        <div id="floorPlansGrid" class="d-flex flex-wrap gap-2"></div>
      </div>
      <div class="modal-footer">
        <button type="button" id="floorPlansDone" class="btn btn-success" data-bs-dismiss="modal">Done</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const MAX_FILES = 30;
  const MAX_SIZE_MB = 5;

  const floorInput = document.getElementById("floor_plans");
  const floorGrid = document.getElementById("floorPlansGrid");
  const floorFeatured = document.getElementById("floorPlansFeatured");
  const floorCount = document.getElementById("floorPlansCount");
  const floorThumbs = document.getElementById("floorPlansThumbs");
  const floorDone = document.getElementById("floorPlansDone");
  const floorDropZone = document.getElementById("floorPlansDropZone");

  let floorImages = [];

  floorInput.addEventListener("change", () => {
    const files = Array.from(floorInput.files);

    if (files.length + floorImages.length > MAX_FILES) {
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
        floorImages.push({ name: file.name, src: e.target.result });
        renderFloorPlans();
      };
      reader.readAsDataURL(file);
    });

    floorInput.value = ""; // reset so same file can be selected again
  });

  function renderFloorPlans() {
    floorGrid.innerHTML = "";

    floorImages.forEach((img, index) => {
      const wrapper = document.createElement("div");
      wrapper.className = "position-relative border rounded overflow-hidden";
      wrapper.style.width = "100px";
      wrapper.style.height = "100px";
      wrapper.style.cursor = "move";
      wrapper.setAttribute("draggable", true);
      wrapper.dataset.index = index;

      const image = document.createElement("img");
      image.src = img.src;
      image.className = "img-fluid h-100 w-100 object-fit-cover";
      wrapper.appendChild(image);
      floorGrid.appendChild(wrapper);
    });
  }

  new Sortable(floorGrid, {
    animation: 150,
    onEnd: function () {
      const reordered = [...floorGrid.querySelectorAll("img")].map(img => img.src);
      floorImages = reordered.map(src => floorImages.find(i => i.src === src));
    }
  });

  floorDone.addEventListener("click", () => {
    if (floorImages.length === 0) return;

    floorFeatured.src = floorImages[0].src;
    floorFeatured.classList.remove("d-none");
    floorCount.innerText = floorImages.length;

    floorThumbs.innerHTML = "";
    floorImages.slice(1, 4).forEach((img) => {
      const thumb = document.createElement("img");
      thumb.src = img.src;
      thumb.className = "img-fluid rounded object-fit-cover";
      thumb.style.height = "75px";
      thumb.style.width = "100%";
      floorThumbs.appendChild(thumb);
    });
  });

  // Drag-and-drop support
  floorDropZone.addEventListener("dragover", e => {
    e.preventDefault();
    floorDropZone.classList.add("bg-light");
  });

  floorDropZone.addEventListener("dragleave", () => {
    floorDropZone.classList.remove("bg-light");
  });

  floorDropZone.addEventListener("drop", e => {
    e.preventDefault();
    floorDropZone.classList.remove("bg-light");
    const dtFiles = e.dataTransfer.files;
    if (floorInput) {
      floorInput.files = dtFiles;
      floorInput.dispatchEvent(new Event("change"));
    }
  });

  // Reset logic
  document.querySelector("form").addEventListener("reset", () => {
    floorImages = [];
    floorGrid.innerHTML = "";
    floorThumbs.innerHTML = "";
    floorFeatured.src = "";
    floorFeatured.classList.add("d-none");
    floorCount.innerText = "0";
    floorInput.value = "";
  });
});
</script>
