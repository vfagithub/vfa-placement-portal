@extends('layouts.default')

@section('header')
{{ $company->name }} <small><em>{{ $company->twitterPitch }}</em></small>
    @if(Auth::user()->role == "Hiring Manager")
        @if(Auth::user()->profile->company->id == $company->id)
            <span class="pull-right">
                <small><em><a href="{{ URL::route('companies.edit', $company->id) }}"><i class="fa fa-pencil-square-o"></i>Edit your Company profile</a></em></small>
            </span>
        @endif
    @endif
@stop

@section('content')

<!-- TODO: ADD COMPANY LOGO -->

<div class="container">
    @if(!empty($company->logoPath))
        <img src="{{ $company->logoPath }}" class="img-responsive" alt="Responsive image">
    @endif
	<div class="row" id="highlights">
    	<div class="col-md-3 col-md-offset-0 col-sm-6 col-sm-offset-3 col-xs-8 col-xs-offset-2"><h3 class="text-center">City</h3><p class="text-center">{{ $company->city }}</p></div>
    	<div class="col-md-3 col-md-offset-0 col-sm-6 col-sm-offset-3 col-xs-8 col-xs-offset-2"><h3 class="text-center">Founded</h3><p class="text-center">{{ $company->yearFounded }}</p></div>
    	<div class="col-md-3 col-md-offset-0 col-sm-6 col-sm-offset-3 col-xs-8 col-xs-offset-2"><h3 class="text-center">Employees</h3><p class="text-center">{{ $company->employees }}</p></div>
    	<div class="col-md-3 col-md-offset-0 col-sm-6 col-sm-offset-3 col-xs-8 col-xs-offset-2"><h3 class="text-center">Website</h3><p class="text-center"><a class="btn btn-link form-control" href="{{ $company->url }}" target="_blank">Visit <i class="fa fa-external-link"></i></a></p></div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h4><strong>About {{ $company->name }}</strong></h4>
            <p>{{ Parser::linkUrlsInText($company->bio) }}</p>
        </div>
        <div class="col-md-6">
            <h4><strong>Describe your team culture</strong></h4>
            <p>{{ $company->teamAnswer }}</p>
            <h4><strong>Does a VFA fellow currently work at {{ $company->name }}?</strong></h4>
            <p>{{ $company->hasFellow ? "Yes" : "No" }}</p>
        </div>
    </div>

    @if($company->canViewContactInfo())
        <div class="row" id="contact-info">
            <div class="col-xs-10">
                <h3>Contact Info:</h3>
                @include('partials.components.company-contacts', array('company' => $company))
            </div>
        </div>
    @endif
</div>

@if(Auth::user()->role != "Hiring Manager")
    <div class="secondary">
    	<div class="container">
    		<h3>{{ "$company->name's Opportunities <small>(<em>" . count($company->opportunities) ."</em>)</small>" }}</h3>
    	    @foreach($company->opportunities()->where('isPublished', '=', true)->get() as $opportunity)
                @include('partials.indexes.opportunity', array('opportunity' => $opportunity))
    	    @endforeach
    	</div>
    </div>
@endif

@if(Auth::user()->role == "Admin")
    @include('partials.components.adminNotes', array('adminNotes' => $company->adminNotes, 'entityType' => "Company", 'entityId' => $company->id))
@elseif(Auth::user()->role == "Fellow")
    {{-- Commented out due to VFA's request: https://github.com/lowe0292/vfa-placement-portal/issues/16 }}
    {{-- @include('partials.components.fellowNotes', array('fellowNotes' => $company->fellowNotes, 'entityType' => "Company", 'entityId' => $company->id)) --}}
@endif

@stop