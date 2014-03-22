<div class="fellow-dash">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="pull-right">
                    Looking for resources or wisdom from past fellows? Check out <a href="http://vfafellows.org/">vfafellows.org</a>
                </div>
            </div>
        </div>
        @if(count($placementStatuses) > 0)
            <div class="upcoming-events">
                    <h2><small>Upcoming Events</small></h2>
                    <?php $eventCount = 0; ?>
                    @foreach($placementStatuses as $placementStatus)
                        @if($placementStatus->eventDate != "")
                            @include('partials.indexes.upcoming-events', array('placementStatus' => $placementStatus))
                            <?php $eventCount += 1; ?>
                        @endif
                    @endforeach
                    @if($eventCount == 0)
                        <p>Based on your Placement Statuses below, you have no upcoming events.</p>
                    @endif
            </div>
            @include('partials.components.placementStatuses', array('placementStatuses' => $placementStatuses, 'heading'=>"Your Placement Progress"))
        @endif
        @if(count($pitches) > 0)
            <div class="row">
                <!-- <div class="col-md-9" id="new-pitches"> -->
                <div class="col-md-12" id="new-pitches">
                    <h2>Your Pitches 
                        <small>(<em> {{ count($pitches) }}</em>)</small>
                    </h2>
                    @foreach($pitches as $pitch)
                        @include('partials.indexes.pitch', array('pitch' => $pitch))
                    @endforeach
                </div>
            </div>
        @endif
        @if(count($placementStatuses) == 0 && count($pitches) == 0)
            <div class="row">
                <div class="col-xs-12">
                    <h4>Welcome to the Fellow Dashboard!</h4>
                    <p>This is where you'll find your upcoming events and progress with each Opportunity you're interested in. But you haven't been introduced to any companies yet... <p>
                    <p>Head on over to the list of Opportunities and start pitching! Your pitches will be reviewed by VFA team members, and you'll receive an email when they're approved.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <div class="pull-right">
                        <a href="{{ URL::route('opportunities.index') }}" class="btn btn-primary">View Opportunities</a>
                    </div>
                </div>
            </div>
        @endif
        <!-- Pitch Invites -->
        @include("partials.indexes.pitchInvites", array("fellow" => Auth::user()->profile))
        <!-- New Opportunities -->
        <div class="row">
            <h2>Newest Opportunities</h2>
            @foreach(Opportunity::where('isPublished','=',true)->orderBy("created_at", "DESC")->take(5)->get() as $opportunity)
                @include('partials.indexes.opportunity', array('opportunity' => $opportunity))
            @endforeach
            <p class="pull-right"><a href="/opportunities" class="btn btn-primary">View All Opportunities</a></p>
        </div>
        <!-- New Companies -->
        <div class="row">
            <h2>Newest Companies</h2>
            @foreach(Company::where('isPublished','=',true)->orderBy("created_at", "DESC")->take(5)->get() as $company)
                @include('partials.indexes.company', array('company' => $company))
            @endforeach
            <p class="pull-right"><a href="/companies" class="btn btn-primary">View All Companies</a></p>
        </div>
    </div><!-- End Container -->
</div><!-- End fellow dashboard -->
