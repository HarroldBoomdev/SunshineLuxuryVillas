@extends('layouts.app')

@section('content')
<div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 border-end vh-100 overflow-auto bg-light">
        <h5 class="p-3">API Reference</h5>
        <ul class="nav flex-column px-3">
            <li class="nav-item"><a class="nav-link" href="#" data-section="home">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-section="about">About Us</a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-section="fees">Buying Fees</a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-section="repossessions">Repossessions</a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-section="residency">Residency</a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-section="sell">Sell With Us</a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-section="investor">Investor CLub</a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-section="legal">Lawyers</a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-section="blogs">Blogs</a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-section="property-list">Property List</a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-section="property-detail">Property Detail</a></li>

        </ul>
    </div>

    <!-- Main Content -->
    <div class="col-md-9 py-4 px-5" id="api-content">
        <div id="home" class="api-section">
            @include('api.home')
        </div>
        <div id="about" class="api-section d-none">
            @include('api.about')
        </div>
        <div id="fees" class="api-section d-none">
            @include('api.fees')
        </div>
        <div id="repossessions" class="api-section d-none">
            @include('api.repossessions')
        </div>
        <div id="residency" class="api-section d-none">
            @include('api.residency')
        </div>
        <div id="sell" class="api-section d-none">
            @include('api.sell')
        </div>
        <div id="investor" class="api-section d-none">
            @include('api.investor')
        </div>
        <div id="legal" class="api-section d-none">
            @include('api.legal')
        </div>
        <div id="blogs" class="api-section d-none">
            @include('api.blogs')
        </div>
        <div id="property-list" class="api-section d-none">
            @include('api.property-list')
        </div>
        <div id="property-detail" class="api-section d-none">
            @include('api.property-detail')
        </div>




    </div>

</div>


<script>
    document.querySelectorAll('.nav-link[data-section]').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();

            // Hide all sections
            document.querySelectorAll('.api-section').forEach(sec => sec.classList.add('d-none'));

            // Show selected section
            const sectionId = link.getAttribute('data-section');
            document.getElementById(sectionId).classList.remove('d-none');

            // Update URL without reloading
            history.replaceState(null, '', `#${sectionId}`);
        });
    });

    // Auto-show section on page load based on URL hash
    window.addEventListener('DOMContentLoaded', () => {
        const hash = window.location.hash.replace('#', '') || 'home';
        const section = document.getElementById(hash);
        if (section) {
            document.querySelectorAll('.api-section').forEach(sec => sec.classList.add('d-none'));
            section.classList.remove('d-none');
        }
    });
</script>

@endsection
