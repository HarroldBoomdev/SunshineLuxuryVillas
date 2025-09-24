@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <form action="{{ route('clients.update', $client->id) }}" method="POST">
        @csrf
        @method('PUT')

        @include('layouts.newButton')

        <!-- Hidden ID Field -->
        <input type="hidden" name="editid" value="{{ $client->id }}">

        <!-- Main Content -->
        <div class="row">
            <!-- Left Column: Details -->
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <h1>Edit Client Details</h1>
                    </div>
                </div>

                <div class="row">
                    <div class="card p-3">
                        <h4>Details</h4>
                        <div class="row g-3"> <!-- Internal row with spacing -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="editfname">First Name *</label>
                                    <input type="text" id="editfname" name="editfname" class="form-control" value="{{ $client->fname }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="editaddress">Address</label>
                                    <input type="text" id="editaddress" name="editaddress" class="form-control" value="{{ $client->address }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="editcity">City</label>
                                    <input type="text" id="editcity" name="editcity" class="form-control" value="{{ $client->city }}">
                                </div>
                                <div class="form-group">
                                    <label for="editcountry">Country</label>
                                    <select id="editcountry" name="editcountry" class="form-control">
                                        <option value="" disabled>Select your country</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="editmobile">Mobile</label>
                                    <input id="editmobile" name="editmobile" type="tel" class="form-control" value="{{ $client->mobile }}">
                                </div>
                                <div class="form-group">
                                    <label for="editemail">Email</label>
                                    <input id="editemail" name="editemail" class="form-control" value="{{ $client->email }}">
                                </div>
                                <div class="form-group">
                                    <label for="editidcardnum">ID Card Number</label>
                                    <input id="editidcardnum" name="editidcardnum" class="form-control" value="{{ $client->idcardnum }}">
                                </div>
                                <div class="form-group">
                                    <label for="editlabelsField">Labels</label>
                                    <div class="dropdown">
                                        <div class="form-control dropdown-toggle" id="editlabelsField" data-bs-toggle="dropdown" aria-expanded="false">
                                            <!-- Selected items will appear here -->
                                        </div>
                                        <div class="dropdown-menu p-3" style="width: 100%;">
                                            <div class="form-check">
                                                <input type="checkbox" class="labels-checkbox" id="editluxury" name="editlabels[]" value="Luxury">
                                                <label class="check-label" for="editluxury">Luxury</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" class="labels-checkbox" id="editpetFriendly" name="editlabels[]" value="Pet Friendly">
                                                <label class="check-label" for="editpetFriendly">Pet Friendly</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" class="labels-checkbox" id="editbank" name="editlabels[]" value="Bank">
                                                <label class="check-label" for="editbank">Bank</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" class="labels-checkbox" id="editkml" name="editlabels[]" value="KML">
                                                <label class="check-label" for="editkml">KML</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" class="labels-checkbox" id="editresSales" name="editlabels[]" value="Residential Sales">
                                                <label class="check-label" for="editresSales">Residential Sales</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="editrefAgentCon">Referral Agent Contact</label>
                                    <input type="number" id="editrefAgentCon" name="editrefAgentCon" class="form-control" value="{{ $client->refAgentCon }}">
                                </div>
                                <div class="form-group">
                                    <label for="editprefLang">Preferred Language</label>
                                    <select id="editprefLang" name="editprefLang" class="form-control">
                                        <option value="English" {{ $client->prefLang == 'English' ? 'selected' : '' }}>English</option>
                                        <option value="Greek" {{ $client->prefLang == 'Greek' ? 'selected' : '' }}>Greek</option>
                                        <option value="British" {{ $client->prefLang == 'British' ? 'selected' : '' }}>British</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="editmanagingAgent">Managing Agent *</label>
                                    <select id="editmanagingAgent" name="editmanagingAgent" class="form-control">
                                        <option value="" disabled selected>-</option>
                                        <option value="Thomas Harrison" {{ $client->managingAgent == 'Thomas Harrison' ? 'selected' : '' }}>Thomas Harrison</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="editdob">Date of Birth</label>
                                    <input type="date" id="editdob" name="editdob" class="form-control" value="{{ $client->dob }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="editlname">Last Name *</label>
                                    <input type="text" id="editlname" name="editlname" class="form-control" value="{{ $client->lname }}">
                                </div>
                                <div class="form-group">
                                    <label for="editzipcode">Zipcode</label>
                                    <input type="text" id="editzipcode" name="editzipcode" class="form-control" value="{{ $client->zipcode }}">
                                </div>
                                <div class="form-group">
                                    <label for="editregion">Region</label>
                                    <input type="text" id="editregion" name="editregion" class="form-control" value="{{ $client->region }}">
                                </div>
                                <div class="form-group">
                                    <label for="editphone">Phone</label>
                                    <input id="editphone" name="editphone" type="tel" class="form-control" value="{{ $client->phone }}">
                                </div>
                                <div class="form-group">
                                    <label for="editfax">Fax</label>
                                    <input type="text" id="editfax" name="editfax" class="form-control" value="{{ $client->fax }}">
                                </div>
                                <div class="form-group">
                                    <label for="editnationality">Nationality</label>
                                    <select id="editnationality" name="editnationality" class="form-control">
                                        <option value="" disabled selected>-</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="editpassportNum">Passport Number</label>
                                    <input type="number" id="editpassportNum" name="editpassportNum" class="form-control" value="{{ $client->passportNum }}">
                                </div>
                                <div class="form-group">
                                    <label for="editRefAgent">Referral Agent *</label>
                                    <select id="editRefAgent" name="editRefAgentt" class="form-control">
                                        <option value="" disabled="disabled" selected="selected">-</option>
                                        <option value="Thomas Harrison">Thomas Harrison</option>
                                        <option value="Thomas Harrison">Thomas Harrison</option>
                                        <option value="Thomas Harrison">Thomas Harrison</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="editLeadSource">Lead Source</label>
                                    <select id="editLeadSource" name="editLeadSource" class="form-control">
                                        <option value="" disabled="disabled" selected="selected">-</option>
                                        <option value="resale">Resale</option>
                                        <option value="completed">Completed</option>
                                        <option value="keyready">Key Ready</option>
                                        <option value="undrconstruction">Under Construction</option>
                                        <option value="incomplete">Incomplete</option>
                                        <option value="offplan">Off Plan</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="editBranch">Branch *</label>
                                    <select id="editBranch" name="editBranch" class="form-control">
                                        <option value="" disabled="disabled" selected="selected">-</option>
                                        <option value="Ashford">Ashford</option>
                                        <option value="Longbridge">Longbridge</option>
                                        <option value="Silvergate">Silvergate</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="editSubStatus">Subscription Status</label>
                                    <select id="editSubStatus" name="editSubStatus" class="form-control">
                                        <option value="" disabled="disabled" selected="selected">-</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                                <div class="card p-3">
                                    <div class="form-group">
                                        <label for="editNotes">Notes</label>
                                        <textarea id="editNotes" name="editNotes" class="form-control notes-area" rows="4" placeholder="Add notes here..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

            <!-- Right Column: Buyer & Tenant Profile -->
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary mr-2">Update</button>
                        </div>
                    </div>
                </div>
                <div class="card p-3">
                    <h4>Buyer and Tenant Profile</h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editPropertyId">Auto suggest based on Property ID</label>
                                <input type="text" id="editPropertyId" name="editPropertyId" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="editTypeField">Type</label>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button" id="editTypeDropdownButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        Select Types
                                    </button>
                                    <ul class="dropdown-menu p-3" style="max-height: 200px; overflow-y: auto; width: 100%;" aria-labelledby="editTypeDropdownButton">
                                        <li>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="editDetachedHouses" name="editLabels[]" value="Detached House">
                                                <label class="form-check-label" for="editDetachedHouses">Detached House</label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="editSemiDetachedHouses" name="editLabels[]" value="Semi-Detached House">
                                                <label class="form-check-label" for="editSemiDetachedHouses">Semi-Detached House</label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="editLinkDetachedHouses" name="editLabels[]" value="Link-Detached House">
                                                <label class="form-check-label" for="editLinkDetachedHouses">Link-Detached House</label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="editHouses" name="editLabels[]" value="Houses">
                                                <label class="form-check-label" for="editHouses">Houses</label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="editHouseOfCharacter" name="editLabels[]" value="House of Character">
                                                <label class="form-check-label" for="editHouseOfCharacter">House of Character</label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="editApartment" name="editLabels[]" value="Apartment">
                                                <label class="form-check-label" for="editApartment">Apartment</label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="editDuplexMaisonette" name="editLabels[]" value="Duplex Maisonette">
                                                <label class="form-check-label" for="editDuplexMaisonette">Duplex Maisonette</label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="editFarm" name="editLabels[]" value="Farm">
                                                <label class="form-check-label" for="editFarm">Farm</label>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="row">
                                <h4>Bedrooms</h4>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select id="editBedrooms" name="editBedrooms" class="form-control">
                                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select id="editBedrooms1" name="editBedrooms1" class="form-control">
                                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="editPool">Pool</label>
                                <select id="editPool" name="editPool" class="form-control">
                                    <?php for ($i = 1; $i <= 10; $i++): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="editSpecifications">Specifications</label>
                                <select id="editSpecifications" name="editSpecifications" class="form-control">
                                    <option value="" disabled="disabled" selected="selected">-</option>
                                    <option value="east">East</option>
                                    <option value="eastwest">East West</option>
                                    <option value="eastmeridian">East Meredian</option>
                                    <option value="north">North</option>
                                    <option value="northeast">North East</option>
                                    <option value="northwest">North West</option>
                                    <option value="west">West</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="editOrientation">Orientation</label>
                                <select id="editOrientation" name="editOrientation" class="form-control">
                                    <option value="" disabled="disabled" selected="selected">-</option>
                                    <option value="east">East</option>
                                    <option value="eastwest">East West</option>
                                    <option value="eastmeridian">East Meredian</option>
                                    <option value="north">North</option>
                                    <option value="northeast">North East</option>
                                    <option value="northwest">North West</option>
                                    <option value="west">West</option>
                                </select>
                            </div>

                        <div class="row">
                            <h4>Covered m2</h4>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="number" id="editCoveredM" name="editCoveredM" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="number" id="editCoveredM1" name="editCoveredM1" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <h4>Year of Construction</h4>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <select id="editConstructStart" name="editConstructStart" class="form-control">
                                        @for ($i = 1995; $i <= 2025; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <select id="editConstructEnd" name="editConstructEnd" class="form-control">
                                        @for ($i = 1995; $i <= 2025; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                            <div class="form-group">
                                <label for="editFloor">Floor</label>
                                <select id="editFloor" name="editFloor" class="form-control">
                                    <?php for ($i = 1; $i <= 10; $i++): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="editAbility">Ability to Proceed</label>
                                <select id="editAbility" name="editAbility" class="form-control">
                                    <option value="" disabled="disabled" selected="selected">-</option>
                                    <option value="Mortgage Required">Mortgage Required</option>
                                    <option value="Need to sell first">Need to sell first</option>
                                    <option value="Cash Buyer">Cash Buyer</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="editPotential">Potential</label>
                                <select id="editPotential" name="editPotential" class="form-control">
                                    <option value="" disabled="disabled" selected="selected">-</option>
                                    <option value="Hot">Hot</option>
                                    <option value="Warm">Warm</option>
                                    <option value="Cold">Cold</option>
                                    <option value="Time-waster">Time-waster</option>
                                </select>
                            </div>
                        </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="editCategory">Category</label>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button" id="editTypeDropdownButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    Select Types
                                </button>
                                <ul class="dropdown-menu p-3" style="max-height: 200px; overflow-y: auto; width: 100%;" aria-labelledby="editTypeDropdownButton">
                                    <h4>RESIDENTIAL</h4>
                                    <li>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="editSales" name="editLabels[]" value="Sales">
                                            <label class="form-check-label" for="editSales">Sales</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="editRent" name="editLabels[]" value="Rent, Long-term">
                                            <label class="form-check-label" for="editRent">Rent, Long-term</label>
                                        </div>
                                    </li>
                                    <h4>COMMERCIAL</h4>
                                    <li>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="editSalesCommercial" name="editLabels[]" value="Sales">
                                            <label class="form-check-label" for="editSalesCommercial">Sales</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="editRentCommercial" name="editLabels[]" value="Rent">
                                            <label class="form-check-label" for="editRentCommercial">Rent</label>
                                        </div>
                                    </li>
                                    <h4>OTHER</h4>
                                    <li>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="editLand" name="editLabels[]" value="Land">
                                            <label class="form-check-label" for="editLand">Land</label>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                            <div class="form-group">
                                <label for="editArea">Area</label>
                                <div class="dropdown">
                                    <div class="form-control dropdown-toggle" id="editAreaField" data-bs-toggle="dropdown" aria-expanded="false">
                                        Select Labels
                                    </div>
                                    <div class="dropdown-menu p-3 w-100">
                                        <div class="form-check">
                                            <input type="checkbox" class="edit-area-checkbox" id="editCyprus" name="editArea[]" value="Cyprus">
                                            <label class="form-check-label" for="editCyprus">Cyprus</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="edit-area-checkbox" id="editSpain" name="editArea[]" value="Spain">
                                            <label class="form-check-label" for="editSpain">Spain</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="edit-area-checkbox" id="editUnitedKingdom" name="editArea[]" value="United Kingdom">
                                            <label class="form-check-label" for="editUnitedKingdom">United Kingdom</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="edit-area-checkbox" id="editItaly" name="editArea[]" value="Italy">
                                            <label class="form-check-label" for="editItaly">Italy</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                    <div class="row">
                        <h4>Bathrooms</h4>
                        <div class="col-md-6">
                            <div class="form-group">
                                <select id="editBathrooms" name="editBathrooms" class="form-control">
                                    <?php for ($i = 1; $i <= 10; $i++): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <select id="editBathrooms1" name="editBathrooms1" class="form-control">
                                    <?php for ($i = 1; $i <= 10; $i++): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                            <div class="form-group">
                                <label for="editTitleDead">Title Dead</label>
                                <select id="editTitleDead" name="editTitleDead" class="form-control">
                                    <option value="" disabled="disabled" selected="selected">-</option>
                                    <option value="yes">Yes</option>
                                    <option value="shareOfLand">Share of Land</option>
                                    <option value="finalApproval">Final Approval</option>
                                    <option value="leaseHold">Lease Hold</option>
                                    <option value="land">Land</option>
                                    <option value="no">No</option>
                                </select>
                            </div>

                    <div class="row">
                        <h4>Purchase Budget</h4>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="number" id="editPurchaseMin" name="editPurchaseMin" class="form-control" placeholder="Enter min">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="number" id="editPurchaseMax" name="editPurchaseMax" class="form-control" placeholder="Enter max">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="editFurnished">Furnished</label>
                        <select id="editFurnished" name="editFurnished" class="form-control">
                            <option value="" disabled="disabled" selected="selected">-</option>
                            <option value="east">East</option>
                            <option value="eastwest">East West</option>
                            <option value="eastmeridian">East Meredian</option>
                            <option value="north">North</option>
                            <option value="northeast">North East</option>
                            <option value="northwest">North West</option>
                            <option value="west">West</option>
                        </select>
                    </div>

                    <div class="row">
                        <h4>Plot m2</h4>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="number" id="editPlotM" name="editPlotM" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="number" id="editPlotM1" name="editPlotM1" class="form-control">
                            </div>
                        </div>
                    </div>

                            <div class="form-group">
                                <label for="editParking">Parking</label>
                                <select id="editParking" name="editParking" class="form-control">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="editLabels">Labels</label>
                                <select id="editLabels" name="editLabels" class="form-control">
                                    <option value="" disabled="disabled" selected="selected">-</option>
                                    <option value="east">East</option>
                                    <option value="eastwest">East West</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="editReasonsForBuying">Reasons for Buying</label>
                                <select id="editReasonsForBuying" name="editReasonsForBuying" class="form-control">
                                    <option value="" disabled="disabled" selected="selected">-</option>
                                    <option value="east">East</option>
                                    <option value="eastwest">East West</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="editTimeframe">Time Frame</label>
                                <select id="editTimeframe" name="editTimeframe" class="form-control">
                                    <option value="" disabled="disabled" selected="selected">-</option>
                                    <option value="Immediately">Immediately</option>
                                    <option value="Within 6 months">Within 6 months</option>
                                    <option value="Within 1 year">Within 1 year</option>
                                    <option value="Over 1 year">Over 1 year</option>
                                </select>
                            </div>
                        </div>

                    <div class="row">
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="editMatchingSystem" name="editMatchingSystem">
                                <label class="form-check-label" for="editMatchingSystem">Matching system enabled</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col">
                    <div class="row">
                        <div class="card p-3">
                            <h4>Contacts</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editName">Name</label>
                                        <input type="text" id="editName" name="editName" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="editEmailContact">Email</label>
                                        <input type="text" id="editEmailContact" name="editEmailContact" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editTitle">Title</label>
                                        <input type="text" id="editTitle" name="editTitle" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="editPhoneContact">Phone</label>
                                        <input id="editPhoneContact" name="editPhoneContact" type="tel" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card p-3">
                            <h4>Notifications</h4>
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="editEmailMatching" name="editEmailMatching">
                                    <label class="form-check-label" for="editEmailMatching">Email matching properties automatically</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Automatically set selected value for dropdowns
    document.querySelectorAll('select[data-selected]').forEach(select => {
        select.value = select.dataset.selected;
    });

    // Automatically check checkboxes with data-checked="true"
    document.querySelectorAll('input[type="checkbox"][data-checked="true"]').forEach(checkbox => {
        checkbox.checked = true;
    });

    // Update dropdown labels dynamically for checkbox groups
    document.querySelectorAll('.dropdown').forEach(dropdown => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        const checkboxes = dropdown.querySelectorAll('input[type="checkbox"]');
        
        const updateLabel = () => {
            const selected = Array.from(checkboxes)
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.nextElementSibling.textContent.trim())
                .join(', ');
            toggle.textContent = selected || 'Select Labels';
        };

        checkboxes.forEach(checkbox => checkbox.addEventListener('change', updateLabel));
        updateLabel(); // Initialize
    });
});

</script>

@endsection

                                