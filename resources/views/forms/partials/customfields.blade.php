@php
use Illuminate\Support\Str;
@endphp


@php
  /*
   * type: 'check' (default), 'check+input', 'text'
   * for 'check+input', provide 'suffix' (unit) or 'placeholder'
   * name: base input name (we’ll use name or name_value for the extra input)
   */

  $sections = [

    'Views' => [
      ['label'=>'Sea and Mountain Views', 'name'=>'views_sea_mountain'],
      ['label'=>'Sea Views',               'name'=>'views_sea'],
      ['label'=>'Panoramic Views',         'name'=>'views_panoramic'],
      ['label'=>'Views of Town and Sea',   'name'=>'views_town_sea'],
      ['label'=>'Overlooking the Marina',  'name'=>'views_marina'],
      ['label'=>'Overlooking the Golf Course', 'name'=>'views_golf'],
    ],

    'Orientation' => [
      ['label'=>'East',      'name'=>'orient_e'],
      ['label'=>'South',     'name'=>'orient_s'],
      ['label'=>'Southwest', 'name'=>'orient_sw'],
      ['label'=>'Southeast', 'name'=>'orient_se'],
      ['label'=>'West',      'name'=>'orient_w'],
      ['label'=>'North',     'name'=>'orient_n'],
      ['label'=>'Northwest', 'name'=>'orient_nw'],
      ['label'=>'Northeast', 'name'=>'orient_ne'],
    ],

    'Plot' => [
      ['label'=>'CTZ Adjacent', 'name'=>'plot_ctz_adjacent'],
      ['label'=>'ETE Avenue',   'name'=>'plot_ete_ave'],
      ['label'=>'NU/TE',        'name'=>'plot_nu_te'],
      ['label'=>'New Development', 'name'=>'plot_new_dev'],
      ['label'=>'Enclosed Lot',    'name'=>'plot_enclosed_lot'],
      ['label'=>'For Flat Plot',   'name'=>'plot_flat'],
      ['label'=>'Sea View',        'name'=>'plot_sea_view'],
      ['label'=>'Walk to amenities', 'name'=>'plot_walk_amenities', 'type'=>'check+input', 'placeholder'=>'mins'],
      ['label'=>'Distance to Sea',   'name'=>'plot_distance_sea',   'type'=>'check+input', 'suffix'=>'m'],
    ],

    'Garden & Terraces' => [
      ['label'=>'Veranda',              'name'=>'garden_veranda', 'type'=>'check+input', 'suffix'=>'m²'],
      ['label'=>'Terrace',              'name'=>'garden_terrace', 'type'=>'check+input', 'suffix'=>'m²'],
      ['label'=>'Community terrace',    'name'=>'garden_comm_terrace'],
      ['label'=>'Landscape Irrigation', 'name'=>'garden_irrigation'],
      ['label'=>'Lawn Sprinklers',      'name'=>'garden_sprinklers'],
      ['label'=>'Courtyard',            'name'=>'garden_courtyard'],
      ['label'=>'Pergola',              'name'=>'garden_pergola'],
      ['label'=>'Gazebo',               'name'=>'garden_gazebo'],
      ['label'=>'Garden Security Lights','name'=>'garden_security_lights'],
      ['label'=>'Garden Lights',        'name'=>'garden_lights'],
      ['label'=>'Tennis Court',         'name'=>'garden_tennis'],
      ['label'=>'Paddle Court',         'name'=>'garden_paddle'],
      ['label'=>'Landscaped Garden',    'name'=>'garden_landscaped'],
      ['label'=>'Open Terrace',         'name'=>'garden_open_terrace'],
    ],

    'Pool Description' => [
      ['label'=>'Pool Description', 'name'=>'pool_description', 'type'=>'text', 'placeholder'=>'e.g. Infinity, Heated, Shared...'],
    ],

    'Pool' => [
      ['label'=>'Private Pool',        'name'=>'pool_private'],
      ['label'=>'Infinity Pool',       'name'=>'pool_infinity'],
      ['label'=>'Communal Pool',       'name'=>'pool_communal'],
      ['label'=>'Internal Pool',       'name'=>'pool_internal'],
      ['label'=>'Saltwater Pool',      'name'=>'pool_salt'],
      ['label'=>'Indoor Pool',         'name'=>'pool_indoor'],
      ['label'=>'Heated Pool',         'name'=>'pool_heated'],
      ['label'=>'Pool Cover',          'name'=>'pool_cover'],
      ['label'=>'Pool Cleaning System','name'=>'pool_cleaning_system'],
    ],

    'Private Parking' => [
      ['label'=>'Covered Parking',     'name'=>'park_covered'],
      ['label'=>'Uncovered Parking',   'name'=>'park_uncovered'],
      ['label'=>'Automatic Gate',      'name'=>'park_auto_gate'],
      ['label'=>'Assisted Garage Door','name'=>'park_assist_door'],
      ['label'=>'Garage N° Cars',      'name'=>'park_garage_cars',  'type'=>'check+input', 'suffix'=>'cars'],
      ['label'=>'Carport N° Cars',     'name'=>'park_carport_cars', 'type'=>'check+input', 'suffix'=>'cars'],
      ['label'=>'Parking Spaces',      'name'=>'park_spaces',       'type'=>'check+input', 'suffix'=>'spaces'],
      ['label'=>'Heating for Garage',  'name'=>'park_heated'],
    ],

    'Main Living Area' => [
      ['label'=>'Apartment From m²',   'name'=>'mla_apartment_from', 'type'=>'check+input', 'suffix'=>'m²'],
      ['label'=>'Built-in Closet',     'name'=>'mla_built_in_closet'],
      ['label'=>'Living/Dining Area',  'name'=>'mla_living_dining', 'type'=>'check+input', 'suffix'=>'m²'],
      ['label'=>'W/C',                 'name'=>'mla_wc'],
      ['label'=>'Kitchen',             'name'=>'mla_kitchen'],
      ['label'=>'Ensuite',             'name'=>'mla_ensuite'],
      ['label'=>'Dressing Room',       'name'=>'mla_dressing'],
      ['label'=>'Floor Levels',        'name'=>'mla_floor_levels', 'type'=>'check+input', 'suffix'=>'levels'],
      ['label'=>'Bedrooms',            'name'=>'mla_bedrooms',     'type'=>'check+input', 'suffix'=>'beds'],
      ['label'=>'Toilet',              'name'=>'mla_toilet'],
    ],

    'Guest Apartment' => [
      ['label'=>'Under Ceiling (low)', 'name'=>'guest_under_ceiling_low'],
      ['label'=>'Separate Apartment',  'name'=>'guest_separate'],
      ['label'=>'En-Suite',            'name'=>'guest_ensuite'],
      ['label'=>'Kitchen',             'name'=>'guest_kitchen'],
      ['label'=>'Bedrooms',            'name'=>'guest_bedrooms', 'type'=>'check+input', 'suffix'=>'beds'],
      ['label'=>'Toilet',              'name'=>'guest_toilet',   'type'=>'check+input', 'suffix'=>'pcs'],
      ['label'=>'Kitchenette',         'name'=>'guest_kitchenette'],
      ['label'=>'Bathrooms',           'name'=>'guest_bathrooms','type'=>'check+input', 'suffix'=>'pcs'],
    ],

    'Kitchen' => [
      ['label'=>'Open Kitchen',       'name'=>'kitchen_open'],
      ['label'=>'Fitted Kitchen',     'name'=>'kitchen_fitted'],
      ['label'=>'Kitchenette',        'name'=>'kitchen_kitchenette'],
      ['label'=>'Breakfast Bar',      'name'=>'kitchen_breakfast_bar'],
      ['label'=>'L-shaped Kitchen',   'name'=>'kitchen_lshape'],
      ['label'=>'Pantry',             'name'=>'kitchen_pantry'],
      ['label'=>'Marble Countertop',  'name'=>'kitchen_marble'],
      ['label'=>'Granite Countertop', 'name'=>'kitchen_granite'],
      ['label'=>'Silestone Countertop','name'=>'kitchen_silestone'],
      ['label'=>'Cedar Countertop',   'name'=>'kitchen_cedar'],
    ],

    'Flooring' => [
      ['label'=>'Tile Floors',            'name'=>'floor_tile'],
      ['label'=>'Raised Floor',           'name'=>'floor_raised'],
      ['label'=>'Mezzanine',              'name'=>'floor_mezzanine'],
      ['label'=>'Marble Floors',          'name'=>'floor_marble'],
      ['label'=>'Terracotta Floors',      'name'=>'floor_terracotta'],
      ['label'=>'Parquet/Hardwood Floors','name'=>'floor_parquet'],
      ['label'=>'Wall-to-Wall Carpet',    'name'=>'floor_carpet'],
    ],

    'Extras' => [
      ['label'=>'Master Bedroom',      'name'=>'extra_master_bedroom'],
      ['label'=>'Maid’s Room',         'name'=>'extra_maids_room'],
      ['label'=>'Storage/Room',        'name'=>'extra_storage_room'],
      ['label'=>'Sauna',               'name'=>'extra_sauna'],
      ['label'=>'Laundry Room',        'name'=>'extra_laundry'],
      ['label'=>'Cinema/TV',           'name'=>'extra_cinema'],
      ['label'=>'Wine Cellar',         'name'=>'extra_wine_cellar'],
      ['label'=>'Bar',                 'name'=>'extra_bar'],
      ['label'=>'Private Security',    'name'=>'extra_private_security'],
      ['label'=>'Security Room',       'name'=>'extra_security_room'],
      ['label'=>'Smoke Detectors',     'name'=>'extra_smoke_detectors'],
      ['label'=>'Security Shutters',   'name'=>'extra_security_shutters'],
      ['label'=>'Security Windows',    'name'=>'extra_security_windows'],
      ['label'=>'Double Glazed Windows','name'=>'extra_double_glazed'],
      ['label'=>'Electric Blinds',     'name'=>'extra_electric_blinds'],
      ['label'=>'Manual Shutters',     'name'=>'extra_manual_shutters'],
      ['label'=>'Safe',                'name'=>'extra_safe'],
      ['label'=>'Alarm',               'name'=>'extra_alarm'],
      ['label'=>'CCTV',                'name'=>'extra_cctv'],
      ['label'=>'Concierge',           'name'=>'extra_concierge'],
      ['label'=>'Concierge Services',  'name'=>'extra_concierge_services'],
      ['label'=>'Playroom',            'name'=>'extra_playroom'],
      ['label'=>'Tennis/Basket court', 'name'=>'extra_sport_court'],
      ['label'=>'Gym/Fitness',         'name'=>'extra_gym'],
      ['label'=>'Jacuzzi',             'name'=>'extra_jacuzzi'],
      ['label'=>'BBQ',                 'name'=>'extra_bbq'],
      ['label'=>'Fireplace',           'name'=>'extra_fireplace'],
      ['label'=>'Gas Fireplace',       'name'=>'extra_gas_fireplace'],
    ],

    'Heating' => [
      ['label'=>'Central Diesel Heating', 'name'=>'heat_central_diesel'],
      ['label'=>'Storage Heaters',        'name'=>'heat_storage'],
      ['label'=>'Central Gas Heating',    'name'=>'heat_central_gas'],
      ['label'=>'Central Oil Heating',    'name'=>'heat_central_oil'],
      ['label'=>'Floor Heating',          'name'=>'heat_floor'],
      ['label'=>'Solar Panels',           'name'=>'heat_solar'],
      ['label'=>'Fireplace',              'name'=>'heat_fireplace'],
      ['label'=>'Radiators',              'name'=>'heat_radiators'],
      ['label'=>'Gas Fireplace',          'name'=>'heat_gas_fireplace'],
      ['label'=>'Hydronic (boiler)',      'name'=>'heat_hydronic'],
    ],

    'Air Conditioning' => [
      ['label'=>'Pre-Installation',       'name'=>'ac_preinstall'],
      ['label'=>'VRV Air Conditioning',   'name'=>'ac_vrv'],
      ['label'=>'Room Unit Air Conditioning', 'name'=>'ac_room_units'],
      ['label'=>'Central Air Conditioning','name'=>'ac_central'],
    ],

    'Inclusions' => [
      ['label'=>'Ceramic Cook Top', 'name'=>'incl_ceramic_cooktop'],
      ['label'=>'Hob',              'name'=>'incl_hob'],
      ['label'=>'Structured Cabling','name'=>'incl_structured_cabling'],
      ['label'=>'Suspended Ceiling','name'=>'incl_suspended_ceiling'],
      ['label'=>'Gas Cook Top',     'name'=>'incl_gas_cooktop'],
      ['label'=>'Oven',             'name'=>'incl_oven'],
      ['label'=>'Microwave',        'name'=>'incl_microwave'],
      ['label'=>'Extractor Fan',    'name'=>'incl_extractor'],
      ['label'=>'Dishwasher',       'name'=>'incl_dishwasher'],
      ['label'=>'Refrigerator',     'name'=>'incl_refrigerator'],
      ['label'=>'Ice Maker',        'name'=>'incl_icemaker'],
      ['label'=>'Washing Machine',  'name'=>'incl_washing_machine'],
      ['label'=>'Dryer',            'name'=>'incl_dryer'],
      ['label'=>'Carpet',           'name'=>'incl_carpet'],
      ['label'=>'Television',       'name'=>'incl_tv'],
      ['label'=>'Water Filtration', 'name'=>'incl_water_filter'],
      ['label'=>'Satellite Dish',   'name'=>'incl_satellite'],
      ['label'=>'Water Softener',   'name'=>'incl_water_softener'],
    ],

    'Services' => [
      ['label'=>'Mains Water',  'name'=>'svc_mains_water'],
      ['label'=>'Agricultural Water','name'=>'svc_agri_water'],
      ['label'=>'Mains Sewer',  'name'=>'svc_mains_sewer'],
      ['label'=>'Septic Tank',  'name'=>'svc_septic'],
      ['label'=>'Well',         'name'=>'svc_well'],
      ['label'=>'Electricity',  'name'=>'svc_electricity'],
      ['label'=>'Telephone',    'name'=>'svc_telephone'],
      ['label'=>'Internet',     'name'=>'svc_internet'],
    ],

    'Furnished' => [
      ['label'=>'Unfurnished',     'name'=>'furn_unfurnished'],
      ['label'=>'Partly Furnished','name'=>'furn_partly'],
      ['label'=>'Furnished',       'name'=>'furn_furnished'],
    ],

    'Guest House' => [
      ['label'=>'Living/Dining Area', 'name'=>'guesthouse_living'],
      ['label'=>'Kitchen',            'name'=>'guesthouse_kitchen'],
      ['label'=>'Kitchenette',        'name'=>'guesthouse_kitchenette'],
      ['label'=>'Bedrooms',           'name'=>'guesthouse_bedrooms', 'type'=>'check+input', 'suffix'=>'beds'],
      ['label'=>'Bathrooms',          'name'=>'guesthouse_bathrooms','type'=>'check+input', 'suffix'=>'pcs'],
      ['label'=>'Toilets',            'name'=>'guesthouse_toilets',  'type'=>'check+input', 'suffix'=>'pcs'],
      ['label'=>'En-Suite',           'name'=>'guesthouse_ensuite'],
    ],
  ];
@endphp

<div class="card shadow-sm border-0 mb-4">
  <div class="card-header bg-white"><strong>Custom Fields</strong></div>
  <div class="card-body">
    <p class="text-muted small mb-3">
      This is the Custom Field selection. Tick all that apply; numeric fields allow values (e.g., “Distance to Sea (m)”).
    </p>

    <div class="accordion" id="customFieldsAcc">
      @foreach($sections as $section => $items)
        @php $secId = Str::slug($section).'-'.$loop->index; @endphp
        <div class="accordion-item">
          <h2 class="accordion-header" id="h-{{ $secId }}">
            <button class="accordion-button custom-acc-btn {{ $loop->first ? '' : 'collapsed' }}"
                    type="button"
                    data-target="#c-{{ $secId }}"
                    aria-expanded="{{ $loop->first ? 'true':'false' }}">
                {{ $section }}
            </button>

          </h2>
          <div id="c-{{ $secId }}" class="accordion-panel {{ $loop->first ? 'cf-open' : '' }}">
            <div class="accordion-body">
                <div class="row g-2">
                @foreach($items as $it)
                  @php
                    $type = $it['type'] ?? 'check';
                    $name = $it['name'];
                    $checked = old($name) ? true : false;
                  @endphp

                  @if($type === 'text')
                    <div class="col-12">
                      <label class="form-label small mb-1">{{ $it['label'] }}</label>
                      <input type="text" name="{{ $name }}" class="form-control"
                             value="{{ old($name) }}" placeholder="{{ $it['placeholder'] ?? '' }}">
                    </div>
                  @else
                    <div class="col-md-6 col-lg-4">
                      <label class="d-flex align-items-center justify-content-between border rounded p-2">
                        <span class="me-2">{{ $it['label'] }}</span>
                        <span class="d-flex align-items-center gap-2">
                          @if($type === 'check+input')
                            <input type="text" name="{{ $name }}_value"
                                   value="{{ old($name.'_value') }}"
                                   class="form-control form-control-sm" style="width: 110px"
                                   placeholder="{{ $it['placeholder'] ?? '' }}">
                            @if(!empty($it['suffix']))
                              <span class="text-muted small">{{ $it['suffix'] }}</span>
                            @endif
                          @endif
                          <input class="form-check-input" type="checkbox"
                                 name="{{ $name }}" value="1" @checked($checked)>
                        </span>
                      </label>
                    </div>
                  @endif
                @endforeach
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>

{{-- minimal styling to match tiles --}}
<style>
  .accordion-button { background:#f8fafc; }
  .accordion-button:not(.collapsed){ background:#eef5ff; }
  .border.rounded.p-2:hover{ background:#f9fbff; border-color:#cfd7df; }

  .accordion-panel { display: none; }
  .accordion-panel.cf-open { display: block; }

  .accordion-button { background:#f8fafc; }
  .accordion-button:not(.collapsed){ background:#eef5ff; }
  .border.rounded.p-2:hover{ background:#f9fbff; border-color:#cfd7df; }
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('customFieldsAcc');
  if (!container) return;

  const buttons = container.querySelectorAll('.custom-acc-btn');

  buttons.forEach(btn => {
    btn.addEventListener('click', () => {
      const targetSelector = btn.getAttribute('data-target');
      const target = targetSelector ? document.querySelector(targetSelector) : null;
      if (!target) return;

      const isOpen = target.classList.contains('cf-open');

      // Close all panels
      container.querySelectorAll('.accordion-panel').forEach(el => el.classList.remove('cf-open'));
      container.querySelectorAll('.custom-acc-btn').forEach(b => b.classList.add('collapsed'));

      // If this panel was closed, open it
      if (!isOpen) {
        target.classList.add('cf-open');
        btn.classList.remove('collapsed');
      }
    });
  });
});
</script>
@endpush


