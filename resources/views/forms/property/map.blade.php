<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-omnivore@0.3.4/leaflet-omnivore.min.js"></script>

<style>
#liveMap {
  height: 400px;
  width: 100%;
  border: 1px solid #ccc;
  border-radius: 5px;
}
.suggestion-box {
  position: absolute;
  z-index: 1000;
  background: white;
  border: 1px solid #ccc;
  width: 100%;
  max-height: 200px;
  overflow-y: auto;
}
.suggestion-item {
  padding: 5px 10px;
  cursor: pointer;
}
.suggestion-item:hover {
  background-color: #f0f0f0;
}
</style>

<div class="card p-3">
  <h4>Map</h4>
  
  <div class="mb-3 position-relative">
    <input type="text" id="mapSearch" class="form-control" placeholder="Search for a place (e.g., Nicosia, Larnaca)">
    <div id="suggestions" class="suggestion-box d-none"></div>
  </div>
  <div class="row g-3">
    <div class="col-md-12 d-flex justify-content-end gap-2">
      <button class="btn btn-outline-danger btn-sm" id="resetMapBtn">Reset Map</button>
    </div>
    <div class="col-md-6">
      <label for="latitude">Latitude</label>
      <input type="text" id="latitude" class="form-control" readonly>
    </div>
    <div class="col-md-6">
      <label for="longitude">Longitude</label>
      <input type="text" id="longitude" class="form-control" readonly>
    </div>
    <div class="col-md-12">
      <label for="map_address">Map Address</label>
      <input type="text" id="map_address" class="form-control" readonly>
    </div>
    <div class="col-md-12">
      <label for="accuracy">Accuracy</label>
      <select id="accuracy" class="form-control">
        <option value="pinpoint">Pinpoint</option>
        <option value="100">100 m</option>
        <option value="100">200 m</option>
        <option value="100">300 m</option>
        <option value="100">400 m</option>
        <option value="100">500 m</option>
        <option value="500">600 m</option>
        <option value="500">700 m</option>
        <option value="500">800 m</option>
        <option value="500">900 m</option>
        <option value="1000">1000 m</option>
      </select>
    </div>
    <div class="col-md-12">
      <label>Interactive Map</label>
      <div id="liveMap"></div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const defaultCoords = [35.1856, 33.3823]; // Cyprus center
  const map = L.map('liveMap').setView(defaultCoords, 13);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
  const marker = L.marker(defaultCoords, { draggable: true }).addTo(map);

  const latInput = document.getElementById('latitude');
  const lngInput = document.getElementById('longitude');
  const addrInput = document.getElementById('map_address');
  const accuracySelect = document.getElementById('accuracy');
  const suggestionsBox = document.getElementById('suggestions');

  let accuracyCircle = null;

  function updateInputs(lat, lng) {
    latInput.value = lat.toFixed(6);
    lngInput.value = lng.toFixed(6);
    fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
      .then(res => res.json())
      .then(data => addrInput.value = data.display_name || '')
      .catch(() => addrInput.value = '');
  }

  function updateAccuracy(lat, lng) {
    if (accuracyCircle) map.removeLayer(accuracyCircle);
    const value = accuracySelect.value;
    if (value !== 'pinpoint') {
      accuracyCircle = L.circle([lat, lng], {
        radius: parseInt(value, 10),
        color: 'blue',
        fillOpacity: 0.2
      }).addTo(map);
    }
  }

  function updateAll(lat, lng) {
    marker.setLatLng([lat, lng]);
    map.setView([lat, lng], 15);
    updateInputs(lat, lng);
    updateAccuracy(lat, lng);
  }

  map.on('click', e => updateAll(e.latlng.lat, e.latlng.lng));
  marker.on('dragend', e => {
    const { lat, lng } = e.target.getLatLng();
    updateAll(lat, lng);
  });
  accuracySelect.addEventListener('change', () => {
    const { lat, lng } = marker.getLatLng();
    updateAccuracy(lat, lng);
  });

  document.getElementById('resetMapBtn').addEventListener('click', () => {
    updateAll(...defaultCoords);
  });

  const searchInput = document.getElementById('mapSearch');
  let debounce;
  searchInput.addEventListener('input', function () {
    const query = this.value.trim();
    clearTimeout(debounce);
    if (query.length < 3) return suggestionsBox.classList.add('d-none');

    debounce = setTimeout(() => {
      fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
          suggestionsBox.innerHTML = '';
          if (data.length === 0) return suggestionsBox.classList.add('d-none');

          data.slice(0, 5).forEach(place => {
            const div = document.createElement('div');
            div.className = 'suggestion-item';
            div.textContent = place.display_name;
            div.onclick = () => {
              updateAll(parseFloat(place.lat), parseFloat(place.lon));
              suggestionsBox.classList.add('d-none');
              searchInput.value = place.display_name;
            };
            suggestionsBox.appendChild(div);
          });

          suggestionsBox.classList.remove('d-none');
        });
    }, 300);
  });

  document.addEventListener('click', function (e) {
    if (!suggestionsBox.contains(e.target) && e.target !== searchInput) {
      suggestionsBox.classList.add('d-none');
    }
  });

  updateAll(...defaultCoords);
});
</script>
