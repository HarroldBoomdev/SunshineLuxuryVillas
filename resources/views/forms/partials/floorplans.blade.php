<div class="card p-3">
  <div class="form-group">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <label class="mb-0 fw-bold">Floor Plans</label>
      <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#galleryModal">
        Manage Floor Plans
      </button>
    </div>

    <!-- Preview -->
    <div id="galleryPreview" class="d-flex border rounded overflow-hidden" style="height: 250px;">
      <div class="flex-grow-1 position-relative bg-light" id="featuredPhotoPreview">
        <img src="" id="featuredPhoto" class="w-100 h-100 object-fit-cover d-none" alt="Featured">
        <span class="position-absolute bottom-0 end-0 m-2 bg-dark text-white small px-2 py-1 rounded" id="photoCount">0</span>
      </div>
      <div class="d-flex flex-column gap-1 p-1" style="width: 150px;" id="sideThumbnails"></div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="galleryModal" tabindex="-1" aria-labelledby="galleryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Gallery Manager</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="dropZone" class="border border-dashed text-center p-4" style="position: relative; cursor: pointer;">
          <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
          <div>Drag or click to choose files</div>
          <div class="text-muted small">Maximum file size: 30MB</div>
          <input type="file" id="photos" name="photos[]" multiple accept="image/*"
                 style="position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;" />
        </div>
        <div id="galleryGrid" class="d-flex flex-wrap gap-2 mt-3"></div>
      </div>
      <div class="modal-footer">
        <button type="button" id="galleryDone" class="btn btn-success" data-bs-dismiss="modal">Done</button>
      </div>
    </div>
  </div>
</div>

<style>
    #galleryGrid img:hover {
        border-color: #0d6efd;
        opacity: 0.9;
    }

</style>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const MAX_FILES = 50;
  const MAX_SIZE_MB = 5;
  const watermarkSrc = '/images/slv.png';

  const fileInput = document.getElementById('photos');
  const galleryGrid = document.getElementById('galleryGrid');
  const previewContainer = document.getElementById('galleryPreview');
  const featuredPhoto = document.getElementById('featuredPhoto');
  const sideThumbnails = document.getElementById('sideThumbnails');
  const photoCount = document.getElementById('photoCount');
  const doneButton = document.getElementById('galleryDone');
  const dropZone = document.getElementById('dropZone');

  let processedImages = [];
  let selected = new Set(); // for multi-select
  let firstClickForSwap = null; // for swapping two images

  /* ---------- Helper: watermark ---------- */
  function createImageWithWatermark(base64, callback) {
    const baseImg = new Image();
    baseImg.onload = () => {
      const canvas = document.createElement('canvas');
      const ctx = canvas.getContext('2d');
      canvas.width = baseImg.width;
      canvas.height = baseImg.height;
      ctx.drawImage(baseImg, 0, 0);

      const watermark = new Image();
      watermark.onload = () => {
        const scale = 0.3;
        const w = canvas.width * scale;
        const h = watermark.height * (w / watermark.width);
        const x = (canvas.width - w) / 2;
        const y = (canvas.height - h) / 2;
        ctx.globalAlpha = 0.4;
        ctx.drawImage(watermark, x, y, w, h);
        ctx.globalAlpha = 1;
        callback(canvas.toDataURL('image/jpeg'));
      };
      watermark.src = watermarkSrc;
    };
    baseImg.src = base64;
  }

  /* ---------- File upload ---------- */
  fileInput.addEventListener('change', function () {
    galleryGrid.innerHTML = '';
    processedImages = [];
    selected.clear();

    const files = Array.from(this.files);
    if (files.length > MAX_FILES) {
      alert(`Max ${MAX_FILES} images.`);
      this.value = '';
      return;
    }

    files.forEach(file => {
      if (file.size / 1024 / 1024 > MAX_SIZE_MB) {
        alert(`${file.name} exceeds ${MAX_SIZE_MB}MB and was skipped.`);
        return;
      }

      const reader = new FileReader();
      reader.onload = function (e) {
        createImageWithWatermark(e.target.result, (watermarkedSrc) => {
          processedImages.push({ name: file.name, src: watermarkedSrc });
          renderGrid();
        });
      };
      reader.readAsDataURL(file);
    });
  });

  /* ---------- Render gallery grid ---------- */
  function renderGrid() {
    galleryGrid.innerHTML = '';
    processedImages.forEach((img, index) => {
      const wrapper = document.createElement('div');
      wrapper.className = 'position-relative';
      wrapper.style.display = 'inline-block';

      const imageEl = document.createElement('img');
      imageEl.src = img.src;
      imageEl.dataset.index = index;
      imageEl.className = 'img-fluid rounded object-fit-cover';
      imageEl.style.cssText =
        'width:120px;height:120px;object-fit:cover;border:2px solid #ccc;border-radius:6px;cursor:pointer;user-select:none;';

      // Selection highlight
      if (selected.has(index)) imageEl.style.borderColor = '#0d6efd';

      // Delete button
      const delBtn = document.createElement('button');
      delBtn.innerHTML = '×';
      delBtn.type = 'button';
      delBtn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0 m-1 p-0 px-1';
      delBtn.title = 'Delete image';
      delBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        processedImages.splice(index, 1);
        selected.clear();
        renderGrid();
      });

      // Selection / Swap behavior
      imageEl.addEventListener('click', (e) => {
        e.preventDefault();

        // Multi-select (Ctrl/Cmd)
        if (e.ctrlKey || e.metaKey) {
          if (selected.has(index)) selected.delete(index);
          else selected.add(index);
          renderGrid();
          return;
        }

        // Shift select range
        if (e.shiftKey && selected.size > 0) {
          const last = [...selected].pop();
          const [start, end] = [Math.min(last, index), Math.max(last, index)];
          for (let i = start; i <= end; i++) selected.add(i);
          renderGrid();
          return;
        }

        // Swap (if one image already selected as swap target)
        if (firstClickForSwap !== null && firstClickForSwap !== index) {
          const a = firstClickForSwap, b = index;
          [processedImages[a], processedImages[b]] = [processedImages[b], processedImages[a]];
          firstClickForSwap = null;
          selected.clear();
          renderGrid();
          return;
        }

        // Normal click: mark for swap or clear
        if (firstClickForSwap === index) {
          firstClickForSwap = null;
          selected.clear();
        } else {
          firstClickForSwap = index;
          selected.clear();
          selected.add(index);
        }
        renderGrid();
      });

      wrapper.appendChild(imageEl);
      wrapper.appendChild(delBtn);
      galleryGrid.appendChild(wrapper);
    });
  }

  /* ---------- SortableJS ---------- */
  new Sortable(galleryGrid, {
    animation: 150,
    onEnd: function () {
      processedImages = [...galleryGrid.querySelectorAll('img')].map(img => {
        return processedImages.find(i => i.src === img.src);
      });
      selected.clear();
      renderGrid();
    }
  });

  /* ---------- Done / Preview update ---------- */
  doneButton.addEventListener('click', function () {
    if (!previewContainer || !featuredPhoto || !sideThumbnails || !photoCount) return;

    featuredPhoto.classList.add('d-none');
    featuredPhoto.src = '';
    sideThumbnails.innerHTML = '';
    previewContainer.querySelectorAll('input[name="image_order[]"]').forEach(el => el.remove());

    if (processedImages.length === 0) return;

    featuredPhoto.src = processedImages[0].src;
    featuredPhoto.classList.remove('d-none');
    photoCount.innerText = processedImages.length;

    processedImages.slice(1, 4).forEach((img) => {
      const thumb = document.createElement('img');
      thumb.src = img.src;
      thumb.className = 'img-fluid rounded object-fit-cover';
      thumb.style.height = '75px';
      thumb.style.width = '100%';
      sideThumbnails.appendChild(thumb);
    });

    processedImages.forEach(img => {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'image_order[]';
      input.value = img.name;
      previewContainer.appendChild(input);
    });
  });

  /* ---------- Drag-n-Drop zone ---------- */
  dropZone.addEventListener('dragover', e => {
    e.preventDefault();
    dropZone.classList.add('bg-light');
  });
  dropZone.addEventListener('dragleave', () => dropZone.classList.remove('bg-light'));
  dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('bg-light');
    const dtFiles = e.dataTransfer.files;
    if (fileInput) {
      fileInput.files = dtFiles;
      fileInput.dispatchEvent(new Event('change'));
    }
  });
});
</script>

