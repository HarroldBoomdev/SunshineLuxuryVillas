
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
.select2-selection__choice {
  background-color: #0d6efd;  /* Bootstrap primary */
  color: #000;
  border: none;
  padding: 2px 8px;
  margin-top: 5px;
}
.select2-selection__choice__remove {
  color: #fff;
  margin-left: 5px;
  cursor: pointer;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__display {
    cursor: default;
    padding-left: 2px;
    padding-right: 5px;
    color: #000;
}
</style>


<!-- Create Event Modal -->
<div class="modal fade" id="createEventModal" tabindex="-1" aria-labelledby="createEventModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-sm">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Schedule an Activity</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <form method="POST" action="{{ route('diary.store') }}">
        @csrf
        <div class="modal-body">

          <!-- Activity Type Selection -->
          <div class="btn-group w-100 mb-3" role="group">
            <input type="hidden" name="activity_type" id="activity_type" value="Viewing">
            <button type="button" class="btn btn-outline-success active" onclick="setActivityType('Viewing', this)">Viewing</button>
            <button type="button" class="btn btn-outline-primary" onclick="setActivityType('Take On', this)">Take On</button>
            <button type="button" class="btn btn-outline-warning" onclick="setActivityType('Misc', this)">Misc</button>
          </div>

          <!-- Title -->
          <div class="form-group mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" placeholder="Meeting, Call, Viewing..." required>
          </div>

          <!-- Date / Time -->
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">Date</label>
              <input type="date" name="date" id="eventDate" class="form-control" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">Start Time</label>
              <input type="time" name="start_time" id="start_time" class="form-control" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">End Time</label>
              <input type="time" name="end_time" id="end_time" class="form-control" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">Duration</label>
              <input type="text" name="duration" id="duration" class="form-control" readonly placeholder="Auto">
            </div>
          </div>

          <!-- Participants -->
          <div class="form-group mt-3">
            <label class="form-label">Participants</label>
            <select name="assigned_to" class="form-select" required>
              <option value="">Select participant</option>
              @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
              @endforeach
            </select>
          </div>

          <div class="form-group mt-3">
            <label for="clients-multi" class="form-label">Clients</label>
            <select name="clients[]" id="clients-multi" class="form-select" multiple="multiple" style="width: 100%" required>
                <!-- Will load via AJAX -->
            </select>
        </div>

        <div class="form-group mt-3">
            <label for="properties-multi" class="form-label">Properties</label>
            <select name="properties[]" id="properties-multi" class="form-select" multiple="multiple" style="width: 100%">
                <!-- AJAX will populate -->
            </select>
        </div>



          <!-- Notes -->
          <div class="form-group mt-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Add any notes or context here..."></textarea>
          </div>
        </div>

        <!-- Footer -->
        <div class="modal-footer justify-content-between">
          <!-- <div>
            <input type="checkbox" name="mark_done" id="mark_done">
            <label for="mark_done" class="ms-1">Mark as done</label>
          </div> -->
          <div>
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">Save</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Load Select2 CSS and JS once -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {
  // Client Select2
  $('#clients-multi').select2({
    dropdownParent: $('#createEventModal'),
    placeholder: 'Search and select clients...',
    width: '100%',
    minimumInputLength: 2,
    ajax: {
      url: '/api/search',
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          q: params.term,
          type: 'client' // 👈 specify type
        };
      },
      processResults: function (data) {
        return {
          results: data.map(client => ({
            id: client.id,
            text: client.text
          }))
        };
      },
      cache: true
    }
  });

  // Property Select2
  $('#properties-multi').select2({
    dropdownParent: $('#createEventModal'),
    placeholder: 'Search and select properties...',
    width: '100%',
    minimumInputLength: 2,
    ajax: {
      url: '/api/search',
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          q: params.term,
          type: 'property' // 👈 specify type
        };
      },
      processResults: function (data) {
        return {
          results: data.map(property => ({
            id: property.id,
            text: property.text
          }))
        };
      },
      cache: true
    }
  });
});
</script>




