{{-- resources/views/vendors/create.blade.php --}}

<form id="vendorForm" method="POST" action="#">
  {{-- no action/validation yet; this is just the landing UI --}}

  <div class="card shadow-sm border-0">
    <div class="card-header bg-white"><strong>Add Vendor</strong></div>

    <div class="card-body">
      <div class="row g-3">
        {{-- Row 1 --}}
        <div class="col-md-3">
          <label class="form-label">Title:</label>
          <input type="text" name="title" class="form-control" value="{{ old('title') }}">
        </div>

        <div class="col-md-3">
          <label class="form-label">First Name:</label>
          <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}">
        </div>

        <div class="col-md-3">
          <label class="form-label">Last Name:</label>
          <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}">
        </div>

        <div class="col-md-3">
          <label class="form-label">Telephone:</label>
          <input type="text" name="telephone" class="form-control" value="{{ old('telephone') }}">
        </div>

        {{-- Row 2 --}}
        <div class="col-md-3">
          <label class="form-label">Mobile:</label>
          <input type="text" name="mobile" class="form-control" value="{{ old('mobile') }}">
        </div>

        <div class="col-md-3">
          <label class="form-label">Email:</label>
          <input type="email" name="email" class="form-control" value="{{ old('email') }}">
        </div>

        <div class="col-md-3">
          <label class="form-label">Type:</label>
          <select name="type" class="form-select">
            <option value="Vendor"   @selected(old('type') === 'Vendor')>Vendor</option>
            <option value="Landlord" @selected(old('type') === 'Landlord')>Landlord</option>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">Source:</label>
          <select name="source" class="form-select">
            @foreach ([
              'None','Excel','Investors Show','London Olympia','MARK','Place in the Sun',
              'Property Show Rooms','Rightmove','Sunshine Luxury Villas Website','Zoopla'
            ] as $src)
              <option value="{{ $src }}" @selected(old('source') === $src)>{{ $src }}</option>
            @endforeach
          </select>
        </div>

        {{-- Notes --}}
        <div class="col-12">
          <label class="form-label">Notes:</label>
          <textarea name="notes" rows="7" class="form-control">{{ old('notes') }}</textarea>
        </div>
      </div>
    </div>
  </div>

  {{-- ===== Solicitor Details ===== --}}
  <div class="card shadow-sm border-0 mt-4">
    <div class="card-header bg-white"><strong>Solicitor Details</strong></div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label">First Name</label>
          <input type="text" name="sol_first_name" class="form-control" value="{{ old('sol_first_name') }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">Last Name</label>
          <input type="text" name="sol_last_name" class="form-control" value="{{ old('sol_last_name') }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">Telephone Day</label>
          <input type="text" name="sol_phone_day" class="form-control" value="{{ old('sol_phone_day') }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">Email</label>
          <input type="email" name="sol_email" class="form-control" value="{{ old('sol_email') }}">
        </div>

        <div class="col-12">
          <label class="form-label">Address</label>
          <textarea name="sol_address" rows="6" class="form-control">{{ old('sol_address') }}</textarea>
        </div>
      </div>
    </div>
  </div>

  {{-- ===== Bank Details ===== --}}
  <div class="card shadow-sm border-0 mt-4">
    <div class="card-header bg-white"><strong>Bank Details</strong></div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label">Bank Name</label>
          <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name') }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">Sort Code</label>
          <input type="text" name="bank_sort_code" class="form-control" value="{{ old('bank_sort_code') }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">Account Name</label>
          <input type="text" name="bank_account_name" class="form-control" value="{{ old('bank_account_name') }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">Account Number</label>
          <input type="text" name="bank_account_number" class="form-control" value="{{ old('bank_account_number') }}">
        </div>

        <div class="col-12">
          <label class="form-label">Address</label>
          <textarea name="bank_address" rows="6" class="form-control">{{ old('bank_address') }}</textarea>
        </div>
      </div>
    </div>
  </div>
</form>
