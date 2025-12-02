{{-- =============  VENDOR DETAILS  ============= --}}

<div class="card shadow-sm border-0">
    <div class="card-header bg-white"><strong>Add Vendor</strong></div>

    <div class="card-body">
        <div class="row g-3">

            <div class="col-md-3">
                <label class="form-label">Title:</label>
                <input type="text" name="title" class="form-control">
            </div>

            <div class="col-md-3">
                <label class="form-label">First Name:</label>
                <input type="text" name="first_name" class="form-control">
            </div>

            <div class="col-md-3">
                <label class="form-label">Last Name:</label>
                <input type="text" name="last_name" class="form-control">
            </div>

            <div class="col-md-3">
                <label class="form-label">Telephone:</label>
                <input type="text" name="telephone" class="form-control">
            </div>

            <div class="col-md-3">
                <label class="form-label">Mobile:</label>
                <input type="text" name="mobile" class="form-control">
            </div>

            <div class="col-md-3">
                <label class="form-label">Email:</label>
                <input type="email" name="email" class="form-control">
            </div>

            <div class="col-md-3">
                <label class="form-label">Type:</label>
                <select name="type" class="form-select">
                    <option value="Vendor">Vendor</option>
                    <option value="Landlord">Landlord</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Source:</label>
                <select name="source" class="form-select">
                    @foreach ([
                        'None','Excel','Investors Show','London Olympia','MARK','Place in the Sun',
                        'Property Show Rooms','Rightmove','Sunshine Luxury Villas Website','Zoopla'
                    ] as $src)
                        <option value="{{ $src }}">{{ $src }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12">
                <label class="form-label">Notes:</label>
                <textarea name="notes" rows="6" class="form-control"></textarea>
            </div>

        </div>
    </div>
</div>

{{-- =============  SOLICITOR DETAILS  ============= --}}

<div class="card shadow-sm border-0 mt-4">
    <div class="card-header bg-white"><strong>Solicitor Details</strong></div>
    <div class="card-body">
        <div class="row g-3">

            <div class="col-md-3">
                <label class="form-label">First Name</label>
                <input type="text" name="sol_first_name" class="form-control">
            </div>

            <div class="col-md-3">
                <label class="form-label">Last Name</label>
                <input type="text" name="sol_last_name" class="form-control">
            </div>

            <div class="col-md-3">
                <label class="form-label">Telephone Day</label>
                <input type="text" name="sol_phone_day" class="form-control">
            </div>

            <div class="col-md-3">
                <label class="form-label">Email</label>
                <input type="email" name="sol_email" class="form-control">
            </div>

            <div class="col-12">
                <label class="form-label">Address</label>
                <textarea name="sol_address" rows="6" class="form-control"></textarea>
            </div>

        </div>
    </div>
</div>

{{-- =============  BANK DETAILS  ============= --}}

<div class="card shadow-sm border-0 mt-4">
    <div class="card-header bg-white"><strong>Bank Details</strong></div>
    <div class="card-body">
        <div class="row g-3">

            <div class="col-md-3">
                <label class="form-label">Bank Name</label>
                <input type="text" name="bank_name" class="form-control">
            </div>

            <div class="col-md-3">
                <label class="form-label">Sort Code</label>
                <input type="text" name="bank_sort_code" class="form-control">
            </div>

            <div class="col-md-3">
                <label class="form-label">Account Name</label>
                <input type="text" name="bank_account_name" class="form-control">
            </div>

            <div class="col-md-3">
                <label class="form-label">Account Number</label>
                <input type="text" name="bank_account_number" class="form-control">
            </div>

            <div class="col-12">
                <label class="form-label">Address</label>
                <textarea name="bank_address" rows="6" class="form-control"></textarea>
            </div>

        </div>
    </div>
</div>

{{-- =============  VENDOR ADDRESS + MAP  ============= --}}

<div class="card shadow-sm border-0 mt-4">
    <div class="card-header bg-white"><strong>Vendor Address</strong></div>

    <div class="card-body">

        <label class="form-label">Enter postcode or 1st line address and town:</label>
        <input type="text" id="addressSearch" class="form-control mb-2"
               placeholder="Start typing...">

        <div class="d-flex gap-2 mb-3">
            <button id="btnSearch" type="button" class="btn btn-warning">Search</button>
            <button id="btnSearchUpdate" type="button" class="btn btn-warning">Search & Update</button>
        </div>

        <div id="vendorMap"
             style="width:100%; height:400px; background:#e5f7ff;"
             class="mb-4">
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Building Name</label>
                <input type="text" id="building_name" name="building_name" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">Address Line 1</label>
                <input type="text" id="addr1" name="address_line1" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">Address Line 2</label>
                <input type="text" id="addr2" name="address_line2" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">Address Line 3</label>
                <input type="text" id="addr3" name="address_line3" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">Locality</label>
                <input type="text" id="locality" name="locality" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">Town</label>
                <input type="text" id="town" name="town" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">Region</label>
                <input type="text" id="region" name="region" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">Postcode</label>
                <input type="text" id="postcode" name="postcode" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">Country</label>
                <select id="country" name="country" class="form-select">
                    <option value="">- No Country -</option>
                    <option value="Cyprus">Cyprus</option>
                    <option value="UK">United Kingdom</option>
                    <option value="Greece">Greece</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Geolocation</label>
                <input type="text" id="geolocation" name="geolocation" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">Lat</label>
                <input type="text" id="lat" name="lat" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">Lng</label>
                <input type="text" id="lng" name="lng" class="form-control">
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {

    let map, marker;

    function initMap(lat = 34.707, lng = 33.022) {
        const pos = { lat: +lat, lng: +lng };
        map = new google.maps.Map(document.getElementById("vendorMap"), {
            center: pos,
            zoom: 13,
        });

        marker = new google.maps.Marker({
            position: pos,
            map,
            draggable: true,
        });

        marker.addListener("dragend", () => updateLatLng(marker.getPosition()));
    }

    function updateLatLng(pos) {
        const lat = pos.lat().toFixed(6);
        const lng = pos.lng().toFixed(6);
        document.getElementById("lat").value = lat;
        document.getElementById("lng").value = lng;
        document.getElementById("geolocation").value = `${lat}, ${lng}`;
    }

    initMap();

    document.getElementById("btnSearch").onclick = () => runSearch(false);
    document.getElementById("btnSearchUpdate").onclick = () => runSearch(true);

    function runSearch(autoFill) {
        const address = document.getElementById("addressSearch").value.trim();
        if (!address) return;

        new google.maps.Geocoder().geocode({ address }, (results, status) => {
            if (status !== "OK" || !results.length) return;

            const loc = results[0].geometry.location;
            map.setCenter(loc);
            marker.setPosition(loc);
            updateLatLng(loc);

            if (autoFill) fillFields(results[0]);
        });
    }

    function fillFields(res) {
        const c = res.address_components;
        const get = (t) => c.find((x) => x.types.includes(t))?.long_name ?? "";

        document.getElementById("addr1").value = get("route");
        document.getElementById("town").value = get("locality");
        document.getElementById("region").value = get("administrative_area_level_1");
        document.getElementById("postcode").value = get("postal_code");
        document.getElementById("country").value = get("country");
        document.getElementById("building_name").value = get("premise") || get("subpremise") || "";
    }
});
</script>
@endpush
