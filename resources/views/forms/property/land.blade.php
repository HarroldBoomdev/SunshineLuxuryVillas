<div class="card p-3">
  <div class="form-group">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <label class="mb-0 fw-bold">Title Deed Images</label>
      <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#titleDeedModal">
        Manage
      </button>
    </div>

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
          <input type="file" id="title_deed" name="title_deed[]" multiple accept="image/*"
                 style="position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;" />
        </div>
        <div id="titleDeedGrid" class="d-flex flex-wrap gap-2"></div>
      </div>
      <div class="modal-footer">
        <button type="button" id="titleDeedDone" class="btn btn-success" data-bs-dismiss="modal">Done</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const MAX_FILES = 30;
  const MAX_SIZE_MB = 5;

  const input = document.getElementById("title_deed");
  const grid = document.getElementById("titleDeedGrid");
  const featured = document.getElementById("titleDeedFeatured");
  const count = document.getElementById("titleDeedCount");
  const thumbs = document.getElementById("titleDeedThumbs");
  const doneBtn = document.getElementById("titleDeedDone");
  const dropZone = document.getElementById("titleDeedDropZone");

  let images = [];

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
        images.push({ name: file.name, src: e.target.result });
        renderImages();
      };
      reader.readAsDataURL(file);
    });

    input.value = "";
  });

  function renderImages() {
    grid.innerHTML = "";

    images.forEach((img, index) => {
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
      grid.appendChild(wrapper);
    });
  }

  new Sortable(grid, {
    animation: 150,
    onEnd: function () {
      const reordered = [...grid.querySelectorAll("img")].map(img => img.src);
      images = reordered.map(src => images.find(i => i.src === src));
    }
  });

  doneBtn.addEventListener("click", () => {
    if (images.length === 0) return;

    featured.src = images[0].src;
    featured.classList.remove("d-none");
    count.innerText = images.length;

    thumbs.innerHTML = "";
    images.slice(1, 4).forEach((img) => {
      const thumb = document.createElement("img");
      thumb.src = img.src;
      thumb.className = "img-fluid rounded object-fit-cover";
      thumb.style.height = "75px";
      thumb.style.width = "100%";
      thumbs.appendChild(thumb);
    });
  });

  // Drag and Drop
  dropZone.addEventListener("dragover", e => {
    e.preventDefault();
    dropZone.classList.add("bg-light");
  });

  dropZone.addEventListener("dragleave", () => {
    dropZone.classList.remove("bg-light");
  });

  dropZone.addEventListener("drop", e => {
    e.preventDefault();
    dropZone.classList.remove("bg-light");
    input.files = e.dataTransfer.files;
    input.dispatchEvent(new Event("change"));
  });

  // Reset
  document.querySelector("form").addEventListener("reset", () => {
    images = [];
    grid.innerHTML = "";
    thumbs.innerHTML = "";
    featured.src = "";
    featured.classList.add("d-none");
    count.innerText = "0";
    input.value = "";
  });
});
</script>
