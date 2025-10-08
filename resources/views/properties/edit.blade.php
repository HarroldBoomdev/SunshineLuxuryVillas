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
    <form id="propertyForm" action="{{ route('properties.update', $property->id) }}" method="POST" enctype="multipart/form-data">

      @csrf
      @method('PUT')
      @include('layouts.newButton')

    <div class="row">
        <div class="row">
            <div class="col-md-6">
                <h1>Edit Property Details</h1>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    <button type="reset" class="btn btn-secondary">Reset</button>
                    <button type="submit" class="btn btn-primary ms-2">Save Changes</button>

                    <!-- Actions Menu Dropdown -->
                    <div class="dropdown ms-2">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                            id="actionsMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        Actions Menu
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="actionsMenuButton">
                        <li>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#channelManagerModal">
                            Channel Manager
                        </a>
                        </li>
                        <li>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#pdfModal">
                            PDF
                        </a>
                        </li>
                        <li><a class="dropdown-item" href="#">Poster</a></li>
                        <li>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#shareModal">
                            Share
                        </a>
                        </li>
                        <li><a class="dropdown-item" href="#">Clone</a></li>
                        <li><a class="dropdown-item" href="#">Archive</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-warning" href="#">Edit</a></li>
                        <li><a class="dropdown-item text-danger" href="#">Delete</a></li>
                    </ul>
                    </div>
                </div>
                </div>

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
