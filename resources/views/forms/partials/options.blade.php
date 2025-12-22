<div class="card shadow-sm border-0 mb-4">
  <div class="card-header bg-white"><strong>Property Options</strong></div>
  <div class="card-body">
    <div class="row g-4">

      <div class="col-lg-3">
        <h6 class="text-muted mb-2">Display As</h6>
        <div class="vstack gap-2">
          @foreach (['Featured','Direct','Excluded in MLS','Excluded on Websites'] as $opt)
            <label class="opt-tile">
              <span class="opt-label">{{ $opt }}</span>
              <span class="opt-control">
                <input class="form-check-input" type="checkbox"
                  name="display_as[]" value="{{ $opt }}"
                  @checked(in_array($opt, (array) old('display_as', $property->display_as ?? [])))>
              </span>
            </label>
          @endforeach
        </div>
      </div>

      <div class="col-lg-3">
        <h6 class="text-muted mb-2">External Feeds</h6>
        <div class="vstack gap-2">
          @foreach (['Right Move','Zoopla'] as $opt)
            <label class="opt-tile">
              <span class="opt-label">{{ $opt }}</span>
              <span class="opt-control">
                <input class="form-check-input" type="checkbox"
                  name="external[]" value="{{ $opt }}"
                  @checked(in_array($opt, (array) old('external', $property->external ?? [])))>
              </span>
            </label>
          @endforeach
        </div>
      </div>

      <div class="col-lg-3">
        <h6 class="text-muted mb-2">Website Banner</h6>
        <div class="vstack gap-2">
          @foreach ([
            'Reduced','Reserved','Under Offer','Sold Subject to Contract',
            'Sold','Special offer','VIP','Exclusive','Rented',
            'New Listing','Under Construction','Turn Key','No Banner'
          ] as $opt)
            <label class="opt-tile">
              <span class="opt-label">{{ $opt }}</span>
              <span class="opt-control">
                <input class="form-check-input" type="radio"
                  name="banner" value="{{ $opt }}"
                  @checked(
                    old('banner', $property->banner ?? null) === $opt
                    || (!old('banner') && empty($property->banner) && $opt === 'No Banner')
                    )
              </span>
            </label>
          @endforeach
        </div>
      </div>

      <div class="col-lg-3">
        <h6 class="text-muted mb-2">Other</h6>
        <div class="vstack gap-2">
          @foreach ([
            'Prestige','Distressed Sale','Bargain','Rental Potential',
            'B&B Potential','Coastal Property','Inland Property','Coastal & Country Property'
          ] as $opt)
            <label class="opt-tile">
              <span class="opt-label">{{ $opt }}</span>
              <span class="opt-control">
                <input class="form-check-input" type="checkbox"
                  name="other[]" value="{{ $opt }}"
                  @checked(in_array($opt, (array) old('other', $property->other ?? [])))>
              </span>
            </label>
          @endforeach
        </div>
      </div>

    </div>
  </div>
</div>

<style>
.opt-tile{display:flex;align-items:center;justify-content:space-between;border:1px solid #dfe3e8;border-radius:.375rem;padding:.5rem .75rem;background:#fff;min-height:40px;cursor:pointer}
.opt-tile:hover{border-color:#c5ccd3;background:#f9fafb}
.opt-label{flex:1;color:#0b1320}
.opt-control{width:42px;display:flex;align-items:center;justify-content:center}
</style>
