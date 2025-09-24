<template>
  <div class="map-page">
    <div class="property-list">
      <h2 class="section-title">Visible Properties</h2>

      <div class="filter-bar">
        <div class="autocomplete-wrapper">
          <div class="search-with-clear">
            <input
              type="text"
              v-model="searchTerm"
              placeholder="Search by title..."
            />
            <button v-if="searchTerm" @click="clearSearch" class="clear-btn">✕</button>
          </div>

          <ul v-if="showSuggestions && suggestions.length" class="autocomplete">
            <li
              v-for="suggestion in suggestions"
              :key="suggestion"
              @click="selectSuggestion(suggestion)"
            >
              {{ suggestion }}
            </li>
          </ul>
        </div>

        <label>
          Min Price:
          <input type="range" v-model="minPrice" min="0" max="600000" step="10000" />
          <span>{{ minPrice }}€</span>
        </label>
      </div>

      <div class="property-grid">
        <div
          class="property-card"
          v-for="prop in filteredProperties"
          :key="prop.id"
        >
          <img :src="prop.photo" class="property-img" />
          <div class="property-info">
            <p class="price">{{ parseFloat(prop.price).toLocaleString() }}€</p>
            <p class="size">{{ prop.size || 0 }} sqm</p>
            <p class="title">{{ prop.title }}</p>
            <div class="buttons">
              <button class="btn-outline">Add favourite</button>
              <button class="btn" @click="selectedProperty = prop">View details</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div id="map" class="map-container"></div>

    <div v-if="selectedProperty" class="modal-overlay" @click.self="selectedProperty = null">
      <div class="modal">
        <h3>{{ selectedProperty.title }}</h3>
        <img :src="selectedProperty.photo" class="modal-img" />
        <p><strong>Price:</strong> {{ parseFloat(selectedProperty.price).toLocaleString() }}€</p>
        <p><strong>Size:</strong> {{ selectedProperty.size || 0 }} sqm</p>
        <button class="btn" @click="selectedProperty = null">Close</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import axios from 'axios'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import 'leaflet.markercluster'
import 'leaflet.markercluster/dist/MarkerCluster.css'
import 'leaflet.markercluster/dist/MarkerCluster.Default.css'

const properties = ref([])
const visibleProperties = ref([])
const searchTerm = ref('')
const suggestions = ref([])
const showSuggestions = ref(false)
const minPrice = ref(0)
const selectedProperty = ref(null)
const map = ref(null)
const defaultView = [34.8, 33.2]
const defaultZoom = 8

const filteredProperties = computed(() => {
  return visibleProperties.value.filter(p =>
    parseFloat(p.price) >= minPrice.value &&
    p.title.toLowerCase().includes(searchTerm.value.toLowerCase())
  )
})

const selectSuggestion = (title) => {
  searchTerm.value = title
  showSuggestions.value = false

  const matched = properties.value.find(p =>
    p.title.toLowerCase() === title.toLowerCase()
  )
  if (matched && matched.latitude && matched.longitude && map.value) {
    map.value.flyTo([matched.latitude, matched.longitude], 13)
  }
}

const clearSearch = () => {
  searchTerm.value = ''
  suggestions.value = []
  showSuggestions.value = false
  visibleProperties.value = properties.value
  map.value.flyTo(defaultView, defaultZoom)
}

watch(searchTerm, (val) => {
  if (val.length >= 3) {
    showSuggestions.value = true
    suggestions.value = properties.value
      .map(p => p.title)
      .filter(t => t.toLowerCase().includes(val.toLowerCase()))
      .slice(0, 5)

    const matched = properties.value.find(p =>
      p.title.toLowerCase() === val.toLowerCase()
    )
    if (matched && matched.latitude && matched.longitude && map.value) {
      map.value.flyTo([matched.latitude, matched.longitude], 13)
    }
  } else {
    showSuggestions.value = false
    suggestions.value = []
  }
})

onMounted(async () => {
  try {
    const response = await axios.get('/api/properties/map')
    properties.value = response.data
    visibleProperties.value = response.data

    map.value = L.map('map').setView(defaultView, defaultZoom)

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(map.value)

    const clusterGroup = L.markerClusterGroup()
    response.data.forEach(p => {
      if (p.latitude && p.longitude) {
        const marker = L.marker([p.latitude, p.longitude])
          .bindPopup(`<strong>${parseFloat(p.price).toLocaleString()}€</strong><br>${p.title}`)
        clusterGroup.addLayer(marker)
      }
    })
    map.value.addLayer(clusterGroup)
  } catch (error) {
    console.error('Failed to fetch map data:', error)
  }
})
</script>

<style scoped>
.map-page {
  display: flex;
  flex-direction: column;
}
@media (min-width: 1024px) {
  .map-page {
    flex-direction: row;
  }
}
.property-list {
  flex: 2;
  padding: 1rem;
  background: #f9f9f9;
  max-height: 600px;
  overflow-y: auto;
  border-right: 1px solid #e2e8f0;
}
.section-title {
  font-size: 1.4rem;
  font-weight: bold;
  margin-bottom: 1rem;
}
.filter-bar {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-bottom: 1.5rem;
  background: #f9f9f9;
  position: sticky;
  top: 0;
  z-index: 100;
  padding-bottom: 1rem;
  padding-top: 1rem;
  border-bottom: 1px solid #e2e8f0;
}

.autocomplete-wrapper {
  position: relative;
  width: 100%;
}
.search-with-clear {
  position: relative;
  display: flex;
  width: 100%;
}
.search-with-clear input {
  flex: 1;
  padding-right: 2rem;
}
.clear-btn {
  position: absolute;
  right: 4px;
  top: 5px;
  background: none;
  border: none;
  font-size: 1.1rem;
  color: #888;
  cursor: pointer;
}
.clear-btn:hover {
  color: #000;
}
.autocomplete {
  background: white;
  border: 1px solid #ccc;
  border-radius: 4px;
  max-height: 150px;
  overflow-y: auto;
  position: absolute;
  z-index: 10;
  width: 100%;
}
.autocomplete li {
  padding: 0.5rem;
  cursor: pointer;
}
.autocomplete li:hover {
  background: #f0f0f0;
}
.property-grid {
  display: grid;
  grid-template-columns: repeat(1, 1fr);
  gap: 1rem;
}
@media (min-width: 768px) {
  .property-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
.property-card {
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 6px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  overflow: hidden;
}
.property-img {
  width: 100%;
  height: 200px;
  object-fit: cover;
}
.property-info {
  padding: 0.75rem;
}
.price {
  font-weight: bold;
  font-size: 1.2rem;
  margin-bottom: 0.2rem;
}
.size {
  color: #888;
  font-size: 0.9rem;
}
.title {
  font-size: 1rem;
  margin: 0.4rem 0;
}
.buttons {
  display: flex;
  justify-content: space-between;
  margin-top: 0.5rem;
}
.btn {
  background: #007bff;
  color: white;
  border: none;
  padding: 0.4rem 0.6rem;
  font-size: 0.85rem;
  cursor: pointer;
  border-radius: 4px;
}
.btn-outline {
  background: none;
  border: 1px solid #ccc;
  color: #333;
  padding: 0.4rem 0.6rem;
  font-size: 0.85rem;
  cursor: pointer;
  border-radius: 4px;
}
.map-container {
  flex: 1.4;
  min-height: 500px;
  max-height: 800px;
  margin-top: 1rem;
}
#map {
  width: 100%;
  height: 100%;
  min-height: 500px;
  border-radius: 0.5rem;
  border: 1px solid #ccc;
}
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}
.modal {
  background: #fff;
  padding: 2rem;
  border-radius: 8px;
  width: 400px;
  max-width: 90vw;
  text-align: center;
}
.modal-img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  margin-bottom: 1rem;
}
</style>
