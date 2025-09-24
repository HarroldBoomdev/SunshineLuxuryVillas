import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Labels Field Checkbox Interaction
document.addEventListener('DOMContentLoaded', function () {
    const proplabelsField = document.getElementById('proplabelsField');
    const checkboxes = document.querySelectorAll('.proplabelsField-checkbox');

    function updateSelectedproplabelsField() {
        const selected = Array.from(checkboxes)
            .filter(input => input.checked)
            .map(input => input.nextElementSibling.textContent.trim())
            .join(', ');

            proplabelsField.textContent = selected || 'Select Labels';
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedproplabelsField);
    });

    proplabelsField.textContent = 'Select Labels';
});

document.addEventListener('DOMContentLoaded', function () {
    const profacField = document.getElementById('profacField');
    const checkboxes = document.querySelectorAll('.profacField-checkbox');

    function updateSelectedprofacField() {
        const selected = Array.from(checkboxes)
            .filter(input => input.checked)
            .map(input => input.nextElementSibling.textContent.trim())
            .join(', ');

            profacField.textContent = selected || 'Select Labels';
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedprofacField);
    });

    profacField.textContent = 'Select Labels';
});

document.addEventListener('DOMContentLoaded', function () {
    const profeaField = document.getElementById('profeaField');
    const checkboxes = document.querySelectorAll('.profeaField-checkbox');

    function updateSelectedprofeaField() {
        const selected = Array.from(checkboxes)
            .filter(input => input.checked)
            .map(input => input.nextElementSibling.textContent.trim())
            .join(', ');

            profeaField.textContent = selected || 'Select Labels';
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedprofeaField);
    });

    profeaField.textContent = 'Select Labels';
});

// Area
document.addEventListener('DOMContentLoaded', function () {
    const areaField = document.getElementById('areaField');
    const checkboxes = document.querySelectorAll('.area-checkbox');

    function updateSelectedAreaField() {
        const selected = Array.from(checkboxes)
            .filter(input => input.checked) // Only checked checkboxes
            .map(input => input.value) // Use the checkbox's value directly
            .join(', '); // Join selected values

        areaField.textContent = selected || 'Select Labels';
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedAreaField);
    });

    // Initialize the dropdown with placeholder text
    areaField.textContent = 'Select Labels';
});



// Function to handle file upload
function setupUploadBox(selector, gridId, imageClass) {
    document.querySelector(selector).addEventListener('click', () => {
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.multiple = true;
        fileInput.accept = 'image/*';
        fileInput.addEventListener('change', (event) => handleFileUpload(event, gridId, imageClass));
        fileInput.click();
    });
}

// Function to handle file processing and appending images
// function handleFileUpload(event, gridId, imageClass) {
//     const files = event.target.files;
//     const grid = document.getElementById(gridId);

//     Array.from(files).forEach((file) => {
//         if (!file.type.startsWith('image/')) {
//             alert('Only image files are allowed');
//             return;
//         }

//         const reader = new FileReader();
//         reader.onload = function (e) {
//             const img = document.createElement('img');
//             img.src = e.target.result;
//             img.classList.add(imageClass);
//             grid.appendChild(img);
//         };
//         reader.readAsDataURL(file);
//     });
// }

// setupUploadBox('.gallery-upload-box', 'galleryGrid', 'gallery-image');
// setupUploadBox('.title-upload-box', 'titleGrid', 'gallery-image');
// setupUploadBox('.floorplan-upload-box', 'floorplanGrid', 'floorplan-image');

// Function to handle the "done" button click
function setupDoneButton(buttonId) {
    const button = document.getElementById(buttonId);
    if (button) {
        button.addEventListener('click', () => {
            alert('Files uploaded successfully!');
        });
    }
}

// Safe setup
setupDoneButton('doneImage');
setupDoneButton('doneTitle');
setupDoneButton('doneFloor');



// Country Dropdown Population
document.addEventListener('DOMContentLoaded', function () {
    const countryDropdown = document.getElementById('country');

    fetch('https://restcountries.com/v3.1/all')
        .then(response => response.json())
        .then(countries => {
            countries.sort((a, b) => a.name.common.localeCompare(b.name.common)); // Sort alphabetically

            countries.forEach(country => {
                const option = document.createElement('option');
                option.value = country.name.common;
                option.textContent = country.name.common;
                countryDropdown.appendChild(option);
            });
        })
        .catch(error => console.error('Error fetching countries:', error));
});

$(document).ready(function () {
    $('#movedinReady, #sold').datepicker({
        format: 'mm/dd/yyyy',
        autoclose: true,
        todayHighlight: true
    });
    console.log('Datepicker initialized');
});

// Map Initialization
document.addEventListener('DOMContentLoaded', function () {
    const map = L.map('map').setView([51.505, -0.09], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    console.log('Map initialized:', map);

    let marker = L.marker([51.505, -0.09], { draggable: true }).addTo(map);

    marker.on('dragend', function () {
        const latLng = marker.getLatLng();
        $('#latitude').val(latLng.lat);
        $('#longitude').val(latLng.lng);
    });

    $('#mapModal').on('shown.bs.modal', function () {
        map.invalidateSize();
        console.log('Map modal shown');
    });
});

// Initialize Bootstrap tooltips
document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
});


// Initialize the intl-tel-input library
document.addEventListener("DOMContentLoaded", function () {
    const input = document.querySelector("#mobile");

    if (input) {
        console.log("Initializing intl-tel-input...");
        window.intlTelInput(input, {
            initialCountry: "auto",
            geoIpLookup: function (success, failure) {
                fetch("https://ipapi.co/json/")
                    .then((res) => res.json())
                    .then((data) => success(data.country_code))
                    .catch(failure);
            },
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js",
        });
        console.log("intl-tel-input initialized!");
    } else {
        console.log("Mobile input field not found!");
    }
});


document.addEventListener("DOMContentLoaded", function () {
    const typeSelect = new Choices("#type", {
        removeItemButton: true, // Allow removing selected items
        searchPlaceholderValue: "Search...", // Placeholder for the search box
        placeholder: true,
        placeholderValue: "Select type...", // Placeholder text for the dropdown
        itemSelectText: "", // Removes the "Press Enter to select" text
    });
});

$(document).ready(function() {
    $('#typefield').select2({
        placeholder: 'Select types', // Optional placeholder
        allowClear: true            // Allows clearing selection
    });
});








