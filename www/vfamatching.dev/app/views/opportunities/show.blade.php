@extends('layouts.default')

@section('header')
    {{ $opportunity->title }}
    <small><em><a href="{{ URL::to('/companies/' . $opportunity->company->id) }}">{{ $opportunity->company->name }},</a> {{ $opportunity->city }}</em></small>
    @if(Auth::user()->role == "Hiring Manager")
        @if(Auth::user()->profile->company->id == $opportunity->company->id)
            <span class="pull-right">
                <small><em><a href="{{ URL::route('opportunities.edit', $opportunity->id) }}"><i class="fa fa-pencil-square-o"></i>Edit this Opportunity</a></em></small>
            </span>
        @endif
    @endif
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                @include('partials.components.tags', array('tags' => $opportunity->opportunityTags))
            </div>
            <div class="col-md-3">@include('partials.components.pitch-button')</div>
        </div>
        <div class="row">
            <div class="col-md-12">
            	<h4><strong>Opportunity Description</strong></h4>
            	<p>{{ Parser::linkUrlsInText($opportunity->description) }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-10 col-xs-offset-1 col-md-4 col-md-offset-0">
            	<h4><strong><em>What will be some of the Fellow's initial responsibilities?</em></strong></h4>
            	<p>{{ $opportunity->responsibilitiesAnswer	}}</p>
            </div>
            <div class="col-xs-10 col-xs-offset-1 col-md-4 col-md-offset-0">
            	<h4><strong><em>What are the skills and attributes of a Fellow likely to succeed in this role and at this company?</em></strong></h4>
            	<p>{{ $opportunity->skillsAnswer }}</p>
            </div>
            <div class="col-xs-10 col-xs-offset-1 col-md-4 col-md-offset-0">
                <h4><strong><em>Which skills will the Fellow develop in this role?</em></strong></h4>
                <p>{{ $opportunity->developmentAnswer }}</p>
            </div>
        </div>
    </div>

    @if(Auth::user()->role == "Admin")
        {{-- Display average feedback --}}
        <div class="row">
            <div class="center">
                <h3>Average Feedback Score:<br/> {{ $opportunity->averagePlacementStatusFeedbackScore() }} out of 5</h3>
            </div>
        </div>
        {{-- Display feedback commentary --}}
        <div class="container">
            @if($opportunity->placementStatuses()->where('fromRole', 'Fellow')->orderBy('created_at', 'DESC')->count())
                <div class="row">
                <h2>Feedback from Fellows</h2>
                @foreach($opportunity->placementStatuses()->where('fromRole', 'Fellow')->orderBy('created_at', 'DESC')->get() as $placementStatus)
                    <div class="col-xs-12 comment well">
                        <span>
                            <strong><i class="fa fa-comments-o"></i> <a href="{{ URL::to('fellows/' . $placementStatus->fellow->id)}}">{{ $placementStatus->fellow->user->firstName . " " . $placementStatus->fellow->user->lastName}}</a></strong>
                            <em>{{ Carbon::createFromFormat('Y-m-d H:i:s', $placementStatus->created_at)->diffForHumans() }}</em>
                        </span>
                        <p>{{ Parser::linkUrlsInText($placementStatus->message) }}</p>
                        <p><strong>Feedback Score: {{ $placementStatus->score }}</strong></p>
                    </div>
                @endforeach
                </div>
            @endif
            @if($opportunity->placementStatuses()->where('fromRole', 'Hiring Manager')->orderBy('created_at', 'DESC')->count())
                <div class="row">
                <h2>Feedback on Fellows</h2>
                @foreach($opportunity->placementStatuses()->where('fromRole', 'Hiring Manager')->orderBy('created_at', 'DESC')->get() as $placementStatus)
                    <div class="col-xs-12 comment well">
                        <span>
                            <strong><i class="fa fa-comments-o"></i> <a href="{{ URL::to('fellows/' . $placementStatus->fellow->id)}}">{{ $placementStatus->fellow->user->firstName . " " . $placementStatus->fellow->user->lastName}}</a></strong>
                            <em>{{ Carbon::createFromFormat('Y-m-d H:i:s', $placementStatus->created_at)->diffForHumans() }}</em>
                        </span>
                        <p>{{ Parser::linkUrlsInText($placementStatus->message) }}</p>
                        <p><strong>Feedback Score: {{ $placementStatus->score }}</strong></p>
                    </div>
                @endforeach
                </div>
            @endif
        </div>
        {{-- Display a Admin waitlisted pitches to admins --}}
        <div class="container">
            {{-- Pending Admin approval --}}
            @if(Pitch::where("opportunity_id","=",$opportunity->id)->where('status','=','Under Review')->where("hasAdminApproval","=",false)->count())
                <div class="row" id="pending-pitches">
                    <div class="col-xs-12">
                        <h3>Pitches pending Admin approval:</h3>
                        @foreach(Pitch::where("opportunity_id","=",$opportunity->id)->where('status','=','Under Review')->where("hasAdminApproval","=",false)->get() as $pitch)
                            @include('partials.indexes.pitch', array('pitch' => $pitch))
                        @endforeach
                    </div>
                </div>
            @endif
            {{-- Waitlisted by Admin --}}
            @if(Pitch::where("opportunity_id","=",$opportunity->id)->where("hasAdminApproval","=",false)->where("status","=","Waitlisted")->count())
            <div class="row" id="waitlisted-pitches">
                <div class="col-xs-12">
                    <h3>Waitlisted Pitches:</h3>
                    @foreach(Pitch::where("opportunity_id","=",$opportunity->id)->where("hasAdminApproval","=",false)->where("status","=","Waitlisted")->get() as $pitch)
                        @include('partials.indexes.pitch', array('pitch' => $pitch))
                    @endforeach
                </div>
            </div>
            @endif
            {{-- Pending Company approval --}}
            @if(Pitch::where("opportunity_id","=",$opportunity->id)->where('status','=','Under Review')->where("hasAdminApproval","=",true)->count())
                <div class="row" id="pending-pitches">
                    <div class="col-xs-12">
                        <h3>Pitches pending Company approval:</h3>
                        @foreach(Pitch::where("opportunity_id","=",$opportunity->id)->where('status','=','Under Review')->where("hasAdminApproval","=",true)->get() as $pitch)
                            @include('partials.indexes.pitch', array('pitch' => $pitch))
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        <div class="container">
            @include('partials.components.placementStatuses', array('placementStatuses' => $opportunity->placementStatuses()->where('isRecent','=',true)->get(), 'heading'=>"Candidate Progress"))
        </div>
        @include('partials.components.adminNotes', array('adminNotes' => $opportunity->adminNotes, 'entityType' => "Opportunity", 'entityId' => $opportunity->id))
    @elseif(Auth::user()->role == "Fellow")
        {{-- Commented out due to VFA's request: https://github.com/lowe0292/vfa-placement-portal/issues/16 }}
        {{-- @include('partials.components.fellowNotes', array('fellowNotes' => $opportunity->fellowNotes, 'entityType' => "Opportunity", 'entityId' => $opportunity->id)) --}}
    @elseif(Auth::user()->role == "Hiring Manager")
        {{-- Display company waitlisted pitches to hiring managers --}}
        <div class="container">
            @foreach(Pitch::where('opportunity_id','=',$opportunity->id)->where("hasAdminApproval","=",true)->where('status','=', 'Under Review')->get() as $pitch)
                <div class="row" id="pending-pitches">
                    <div class="col-xs-12">
                        <h3>Pending Pitches for this Opportunity</h3>
                        @include('partials.indexes.pitch', array('pitch' => $pitch))
                    </div>
                </div>
            @endforeach
            @foreach(Pitch::where('opportunity_id','=',$opportunity->id)->where("hasAdminApproval","=",true)->where('status','=', 'Waitlisted')->get() as $pitch)
                <div class="row" id="waitlisted-pitches">
                    <div class="col-xs-12">
                        <h3>Waitlisted Pitches for this Opportunity</h3>
                        @include('partials.indexes.pitch', array('pitch' => $pitch))
                    </div>
                </div>
            @endforeach
        </div>
        {{-- Display Placement Progress for this opportunity --}}
        <div class="container">
            @include('partials.components.placementStatuses', array('placementStatuses' => $opportunity->placementStatuses()->where('isRecent','=',true)->get(), 'heading'=>"Candidate Progress"))
        </div>
    @endif

    <script type="text/javascript">
        $('.pitch-submit').unbind().click(function(e){
            $(this).parent().parent().find('.pitch-form').submit();
            e.preventDefault();//don't follow the actual link
        });
    </script>
@stop