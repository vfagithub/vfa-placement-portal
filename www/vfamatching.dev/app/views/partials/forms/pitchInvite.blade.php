{{-- Requries $fellow --}}
{{-- TODO: If the current user is a hiring manager whose company's open opportunities have not all been pitched by $fellow, then display a select box filled with the unpitched opportunities and a button to submit a pitch invite. --}}
<?php $unpitchedOpportunities = array("" => ""); ?>
@if(Auth::user()->role == "Hiring Manager")
	@foreach(Auth::user()->profile->company->opportunities()->where('isPublished', '=', true)->get() as $opportunity)
		@if((!PitchInvite::hasPitchInvite($fellow, $opportunity)) && (!Pitch::hasPitch($fellow, $opportunity)))
			<?php $unpitchedOpportunities[$opportunity->id] = $opportunity->title; ?>
		@endif
	@endforeach

	@if(count($unpitchedOpportunities) > 1)
	<h3>Invite this fellow to pitch for one of your Opportunities:</h3>
		{{ Form::open(array('url' => 'pitchinvites', 'method' => 'POST')) }}
		    <fieldset>
		        {{ Form::hidden('fellow_id', $fellow->id) }}
		        <div class="form-group">
		            {{ Form::label('opportunity', 'Opportunity') }}
		            {{ Form::select('opportunity', $unpitchedOpportunities, "", array('class'=>'form-control required')) }}
		        </div>
		        <div class="form-group">
	                {{ Form::submit('Invite to Pitch', array('class'=>'btn btn-primary')) }}
	            </div>
		    </fieldset>
		{{ Form::close() }}
	@endif
@endif