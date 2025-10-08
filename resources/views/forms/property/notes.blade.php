{{-- Notes --}}
<div class="card p-3">
  @php
    // Make a single text area work whether `notes` is a string or an array (JSON) in DB
    $notesVal = old('notes.0');
    if ($notesVal === null) {
        $raw = $property->notes ?? null;
        $notesVal = is_array($raw) ? ($raw[0] ?? '') : ($raw ?? '');
    }
  @endphp
  <div class="form-group">
    <label for="notes_0">Notes</label>
    <textarea id="notes_0"
              name="notes[]"
              class="form-control notes-area"
              rows="4"
              placeholder="Add notes here...">{{ $notesVal }}</textarea>
  </div>
</div>

{{-- Kuula link --}}
<div class="card p-3">
  <div class="form-group">
    <h6>Kuula</h6>
    <label for="kuula_link" class="form-label">Virtual Tour Link</label>
    <input type="url"
           id="kuula_link"
           name="kuula_link"
           class="form-control"
           placeholder="https://kuula.co/share/collection/xxxxxxxx"
           value="{{ old('kuula_link', $property->kuula_link ?? '') }}">
  </div>
</div>

{{-- One YouTube video (array-friendly) --}}
<div class="card p-3">
  @php
    $yt0 = old('youtube_links.0');
    if ($yt0 === null) {
        $rawYt = $property->youtube_links ?? null;
        $yt0 = is_array($rawYt) ? ($rawYt[0] ?? '') : ($rawYt ?? '');
    }
  @endphp
  <div class="form-group">
    <div class="d-flex justify-content-between align-items-center">
      <h6>Videos</h6>
    </div>
    <input type="url"
           name="youtube_links[]"
           class="form-control"
           placeholder="https://youtu.be/xxxxxxxxxx"
           value="{{ $yt0 }}">
  </div>
</div>

<div class="card p-3">
  <div class="form-group">
    <label for="virtual_tour_link" class="form-label">Virtual Tour Link</label>
    <input type="url"
           id="virtual_tour_link"
           name="virtual_tour_link"
           class="form-control"
           placeholder="https://my.matterport.com/xxxxxxxx"
           value="{{ old('virtual_tour_link', $property->virtual_tour_link ?? ($property->matterport_link ?? '')) }}">
  </div>
</div>
