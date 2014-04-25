@extends('layouts.default')

@section('header')
    {{ $fellow->user->firstName . ' ' . $fellow->user->lastName }}
    @if(Auth::user()->id == $fellow->user->id)
        <span class="pull-right">
            <small><em><a href="{{ URL::route('fellows.edit', $fellow->id) }}"><i class="fa fa-pencil-square-o"></i>Edit your profile</a></em></small>
        </span>
    @endif
@stop

@section('content')
<div class="container">
    @if(!empty($fellow->displayPicturePath))
    	<div class="row">
            <div class="col-sm-6 col-sm-offset-3">
                <img src="{{ $fellow->displayPicturePath }}" class="img-responsive fellow-display-pic" alt="Responsive image">
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <h3>Interested In</h3>
            <p>@include('partials.components.skills', array('skills' => $fellow->fellowSkills))</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12" id="fellow-list-bio">
            <h3>Professional Bio</h3>
            <p>{{ Parser::linkUrlsInText($fellow->bio) }}</p>
        </div>
        <div class="col-md-12">
            @if(!empty($fellow->resumePath))
                <span class="pull-left">
                    <a class="btn btn-primary form-control" href="{{ $fellow->resumePath }}" target="_blank"><i class="fa fa-cloud-download"></i> View Résumé</a>
                </span>
            @endif
        </div>
    </div>
    <div class="row" id="highlights">
        <div class="col-md-3 col-md-offset-0 col-sm-6 col-sm-offset-3 col-xs-8 col-xs-offset-2"><h3>School</h3><p>{{ $fellow->school }}</p></div>
        <div class="col-md-3 col-md-offset-0 col-sm-6 col-sm-offset-3 col-xs-8 col-xs-offset-2"><h3>Degree(s)</h3><p>{{ $fellow->degree . " in " . $fellow->major }}</p></div>
        <div class="col-md-3 col-md-offset-0 col-sm-6 col-sm-offset-3 col-xs-8 col-xs-offset-2"><h3>Hometown</h3><p>{{ $fellow->hometown }}</p></div>
        <div class="col-md-3 col-md-offset-0 col-sm-6 col-sm-offset-3 col-xs-8 col-xs-offset-2"><h3>Graduated</h3><p>{{ $fellow->graduationYear }}</p></div>
    </div>
    @if($fellow->canViewContactInfo())
        <div class="row" id="contact-info">
            <div class="col-xs-10">
                <h3>Contact Info:</h3>
                @include('partials.components.contact-info', array('name' => $fellow->user->firstName . ' ' . $fellow->user->lastName, 'email' => $fellow->user->email, 'phoneNumber' => $fellow->phoneNumber))
            </div>
        </div>
    @endif
</div>

@if(Auth::user()->role == "Admin")
    {{-- Display average feedback --}}
    <div class="row">
        <div class="center">
            <h3>Average Feedback Score:<br/> {{ $fellow->averagePlacementStatusFeedbackScore() }} out of 5</h3>
        </div>
    </div>
    {{-- Display feedback commentary --}}
    <div class="container">
        <div class="row">
        @foreach($fellow->placementStatuses()->where('fromRole', 'Hiring Manager')->orderBy('created_at', 'DESC')->get() as $placementStatus)
            <h2>Feedback on Fellow</h2>
            <div class="col-xs-12 comment well">
                <span>
                    <strong><i class="fa fa-comments-o"></i> @include('partials.links.opportunity', array('opportunity'=>  $placementStatus->opportunity))</strong>
                    <em>{{ Carbon::createFromFormat('Y-m-d H:i:s', $placementStatus->created_at)->diffForHumans() }}</em>
                </span>
                <p>{{ Parser::linkUrlsInText($placementStatus->message) }}</p>
                <p><strong>Feedback Score: {{ $placementStatus->score }}</strong></p>
            </div>
        @endforeach
        </div>
        <div class="row">
        @foreach($fellow->placementStatuses()->where('fromRole', 'Fellow')->orderBy('created_at', 'DESC')->get() as $placementStatus)
            <h2>Feedback from Fellow</h2>
            <div class="col-xs-12 comment well">
                <span>
                    <strong><i class="fa fa-comments-o"></i> @include('partials.links.opportunity', array('opportunity'=>  $placementStatus->opportunity))</strong>
                    <em>{{ Carbon::createFromFormat('Y-m-d H:i:s', $placementStatus->created_at)->diffForHumans() }}</em>
                </span>
                <p>{{ Parser::linkUrlsInText($placementStatus->message) }}</p>
                <p><strong>Feedback Score: {{ $placementStatus->score }}</strong></p>
            </div>
        @endforeach
        </div>
    </div>
    {{-- Display a Admin waitlisted pitches to admins --}}
    <div class="container">
        {{-- Pending Admin approval --}}
        @if(Pitch::where("fellow_id","=",$fellow->id)->where("hasAdminApproval","=",false)->where('status','=','Under Review')->count())
            <div class="row" id="pending-pitches">
                <div class="col-xs-12">
                    <h3>Pitches pending Admin approval:</h3>
                    @foreach(Pitch::where("fellow_id","=",$fellow->id)->where("hasAdminApproval","=",false)->where('status','=','Under Review')->get() as $pitch)
                        @include('partials.indexes.pitch', array('pitch' => $pitch))
                    @endforeach
                </div>
            </div>
        @endif
        {{-- Waitlisted by Admin --}}
        @if(Pitch::where("fellow_id","=",$fellow->id)->where("hasAdminApproval","=",false)->where("status","=","Waitlisted")->count())
            <div class="row" id="waitlisted-pitches">
                <div class="col-xs-12">
                    <h3>Waitlisted Pitches:</h3>
                    @foreach(Pitch::where("fellow_id","=",$fellow->id)->where("hasAdminApproval","=",false)->where("status","=","Waitlisted")->get() as $pitch)
                        @include('partials.indexes.pitch', array('pitch' => $pitch))
                    @endforeach
                </div>
            </div>
        @endif
        {{-- Pending Company approval --}}
        @if(Pitch::where("fellow_id","=",$fellow->id)->where('status','=','Under Review')->where("hasAdminApproval","=",true)->count())
            <div class="row" id="pending-pitches">
                <div class="col-xs-12">
                    <h3>Pitches pending Company approval:</h3>
                    @foreach(Pitch::where("fellow_id","=",$fellow->id)->where('status','=','Under Review')->where("hasAdminApproval","=",true)->get() as $pitch)
                        @include('partials.indexes.pitch', array('pitch' => $pitch))
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    <?php $count = 0; ?>
    <div class="container">
        @include('partials.components.placementStatuses', array('placementStatuses' => $fellow->placementStatuses()->where('isRecent','=',true)->get(), 'heading'=>"Fellow's Placement Progress"))
    </div>
    @include('partials.components.adminNotes', array('adminNotes' => $fellow->adminNotes, 'entityType' => "Fellow", 'entityId' => $fellow->id))
@elseif(Auth::user()->role == "Hiring Manager")
    {{-- Display company waitlisted pitches to hiring managers --}}
    @foreach(Auth::user()->profile->company->opportunities as $opportunity)
        @foreach(Pitch::where("fellow_id","=",$fellow->id)->where('opportunity_id','=',$opportunity->id)->where("hasAdminApproval","=",true)->where('status','<>', 'Approved')->get() as $pitch)
                <div class="container">
                    <div class="row" id="waitlisted-pitches">
                        <div class="col-xs-12">
                            <h3>Waitlisted Pitches for {{ $opportunity->title }}:</h3>
                                @include('partials.indexes.pitch', array('pitch' => $pitch))
                        </div>
                    </div>
                </div>
        @endforeach
    @endforeach
    <div class="container">
        <div class="row" id="waitlisted-pitches">
            <div class="col-xs-12 col-md-6">
                @include('partials.forms.pitchInvite', array("fellow" => $fellow))
            </div>
        </div>
    </div>
@endif
@stop
