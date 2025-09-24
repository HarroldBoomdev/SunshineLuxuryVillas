<div class="card p-3">
  <h4>Location</h4>
  <div class="row g-3">
    <div class="col-md-6">
      @php
        $regions = [
          'Paphos' => ['Paphos Town', 'Geroskipou', 'Pegeia', 'Polis Chrysochous', 'Kissonerga', 'Chloraka', 'Tala', 'Emba', 'Kouklia', 'Mandria', 'Peyia', 'Kathikas', 'Droushia', 'Latchi', 'Agia Marina Chrysochous', 'Agia Varvara', 'Acheleia', 'Timi', 'Neo Chorio', 'Milia'],
          'Limassol' => ['Limassol Town', 'Agios Athanasios', 'Ypsonas', 'Mesa Geitonia', 'Kato Polemidia', 'Germasogeia', 'Episkopi', 'Pissouri', 'Agros', 'Platres', 'Moni', 'Kolossi', 'Erimi', 'Parekklisia', 'Sotira', 'Asgata', 'Pentakomo', 'Akrounta', 'Pano Lefkara'],
          'Nicosia' => ['Nicosia Town', 'Strovolos', 'Lakatamia', 'Aglandjia', 'Engomi', 'Latsia', 'Dali', 'Pera Chorio', 'Kokkinotrimithia', 'Agia Varvara', 'Agioi Trimithias', 'Pera', 'Klirou', 'Agia Marina Xyliatou', 'Agios Sozomenos', 'Agios Epifanios Oreinis', 'Agios Georgios Kafkallou'],
          'Larnaca' => ['Larnaca Town', 'Aradippou', 'Oroklini', 'Kiti', 'Pervolia', 'Mazotos', 'Alethriko', 'Anglisides', 'Tersefanou', 'Pyla', 'Agia Anna', 'Avdellero', 'Alaminos', 'Agioi Vavatsinias', 'Klavdia', 'Kornos', 'Lympia', 'Xylotymvou'],
          'Famagusta' => ['Paralimni', 'Ayia Napa', 'Deryneia', 'Sotira', 'Frenaros', 'Avgorou', 'Liopetri', 'Achna', 'Acheritou', 'Xylofagou', 'Xylophagou', 'Vrysoulles', 'Dasaki Achnas', 'Agios Nikolaos'],
          'Kyrenia' => ['Kyrenia Town', 'Lapithos', 'Karavas', 'Karmi', 'Bellapais', 'Ozanköy', 'Catalköy', 'Alsancak', 'Karaoğlanoğlu', 'Zeytinlik', 'Karmi', 'Karakum', 'Doğanköy', 'Esentepe', 'Bahçeli']
        ];
      @endphp

      <div class="form-group">
        <label for="region">Region</label>
        <select id="region" name="region" class="form-control">
          <option value="">Select Region</option>
          @foreach(array_keys($regions) as $region)
            <option value="{{ $region }}">{{ $region }}</option>
          @endforeach
        </select>
      </div>

      <div class="form-group">
        <label for="town">Town/City</label>
        <select id="town" name="town" class="form-control">
          <option value="">Select Town/City</option>
        </select>
      </div>
    </div>

    <div class="col-md-6">
      <div class="form-group">
        <label for="address">Address</label>
        <input type="text" id="address" name="address" class="form-control">
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const regions = @json($regions);
  const regionSelect = document.getElementById('region');
  const townSelect = document.getElementById('town');

  function populateTowns(region) {
    townSelect.innerHTML = '<option value="">Select Town/City</option>';
    if (regions[region]) {
      regions[region].forEach(town => {
        const option = document.createElement('option');
        option.value = town;
        option.textContent = town;
        townSelect.appendChild(option);
      });
    }
  }

  // Run once on load (for edit page scenarios)
  if (regionSelect.value) {
    populateTowns(regionSelect.value);
  }

  // Run on region change
  regionSelect.addEventListener('change', function () {
    populateTowns(this.value);
  });
});
</script>
