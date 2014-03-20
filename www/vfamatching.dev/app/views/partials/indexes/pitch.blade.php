{{-- Requires $pitch --}}
<div class="panel panel-default">
	<div class="panel-heading">
		<strong><h3><a href="{{ URL::route('fellows.show', array('fellows'=> $pitch->fellow->id)) }}">{{ $pitch->fellow->user->firstName . ' ' . $pitch->fellow->user->lastName}}</a> <i class="fa fa-arrows-h"></i> @include('partials.links.opportunity', array('opportunity' => $pitch->opportunity))
         at @include('partials.links.company', array('company' => $pitch->opportunity->company))</h3></strong>
        <em>{{ Carbon::createFromFormat('Y-m-d H:i:s', $pitch->created_at)->diffForHumans() }}</em>
	</div>
	<div class="panel-body">
		<p>{{ Parser::linkUrlsInText($pitch->body) }}</p>
	</div>
	<div class="panel-footer">
		<div class="row">
			<div class="col-xs-12">
			@if($pitch->status != "Waitlisted")
				<span class="pull-right">
				{{ Form::open(array('url' => 'pitches/'.$pitch->id.'/waitlist', 'method' => 'PUT', 'class'=>'submittable-form')) }}
					<button type="button" class="btn btn-danger submittable">
						Waitlist
					</button>
				{{ Form::close() }}
				</span>
			@elseif(Auth::user()->role == "Admin")
				<span class="pull-right">
				{{ Form::open(array('url' => 'pitches/'.$pitch->id, 'method' => 'DELETE', 'class'=>'submittable-form')) }}
					<button type="button" class="btn btn-danger submittable">
						Delete
					</button>
				{{ Form::close() }}
				</span>
			@endif
				<span class="pull-right">
				{{ Form::open(array('url' => 'pitches/'.$pitch->id.'/approve', 'method' => 'PUT', 'class'=>'submittable-form')) }}
					<button type="button" class="btn btn-success submittable">
						Approve
					</button>
				{{ Form::close() }}
				</span>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {  
    //unbind so the click only fires once
    $('.submittable').unbind().click(function(e){
        $(this).parent('.submittable-form').submit();
        e.preventDefault();//don't follow the actual link
    });
});
</script>