@extends('layouts.app')


<style>
#galleryPreview img {
  object-fit: cover;
}
#galleryPreview .img-fluid {
  width: 100%;
  max-height: 300px;
}
#galleryPreview img {
  object-fit: cover;
}
</style>



@section('content')
<div class="content-wrapper">
  <div class="container mt-4 mb-4">
    <form id="propertyForm"
        action="{{ (isset($mode) && $mode === 'edit') ? route('properties.update', $property->id) : route('properties.store') }}"
        method="POST"
        enctype="multipart/form-data">
    @csrf
    @if(isset($mode) && $mode === 'edit')
        @method('PUT')
    @endif


      @include('layouts.newButton')

    <div class="row">
        <div class="row">
            <div class="col-md-6">
                <h1>New Property Details</h1>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    <button type="reset" class="btn btn-secondary">Reset</button>
                    <button type="submit" class="btn btn-primary ms-2">+ Create</button>
                </div>
            </div>

            @php
                use Illuminate\Support\Str;

                $p = $property ?? null;

                $get = function (string $field, $default = '') use ($p) {
                    $ov = old($field);
                    if (!is_null($ov)) return $ov;

                    if ($p) {
                        if (isset($p->{$field})) return $p->{$field};
                        $snake = Str::snake($field);
                        if (isset($p->{$snake})) return $p->{$snake};
                    }
                    return $default;
                };

                $isSelected = function (string $field, $option) use ($get) {
                    $curr = $get($field, '');
                    return (string)$curr === (string)$option
                        || strtolower((string)$curr) === strtolower((string)$option);
                };
            @endphp


    </div>
     <div class="row">
        @include('forms.property.ai_fields')
      </div>

      <div class="row">

        <!-- Left Column: Details -->
        <div class="col-md-6">
            <div class="row">
                <div class="row">
                    @include('forms.property.details')
                </div>
                <div class="row">
                    @include('forms.property.areas')
                </div>
                <!-- <div class="row">
                    @include('forms.property.facilities')
                </div> -->
                <div class="row">
                    @include('forms.property.gallery')
                </div>
                <div class="row">
                    @include('forms.property.notes')
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="row">
                <div class="row">
                    @include('forms.property.owner')
                </div>
                <div class="row">
                    @include('forms.property.location')
                </div>
                <div class="row">
                    @include('forms.property.map')
                </div>
                <div class="row">
                    @include('forms.property.land')
                </div>
                <div class="row">
                    @include('forms.property.distances')
                </div>
                <div class="row">
                    @include('forms.property.features')
                </div>
                <div class="row">
                    @include('forms.property.floorplans')
                </div>

            </div>
        </div>
    </div>


@endsection
