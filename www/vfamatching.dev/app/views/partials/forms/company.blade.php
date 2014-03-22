<?php $validationErrors = Session::has('validation_errors') ? Session::get('validation_errors') : null; ?>
{{-- If $company exists, PUT to /company/{id} to update, otherwise POST to /companies to store new  --}}
@if(isset($company))
    {{ Form::model($company, array('url' => 'companies/' . $company->id, 'method' => 'PUT', 'files' => true)) }}
        <fieldset>
            <div class="form-group @if($validationErrors){{ $validationErrors->has('name') ? "has-error" : ""}}@endif">
                {{ Form::label('name', 'Name') }}
                {{ Form::text('name', Input::old('name'), array('class'=>'form-control required', 'character-limit-max' => 280)) }}
            </div>
            <div class="form-group @if($validationErrors){{ $validationErrors->has('city') ? "has-error" : ""}}@endif">
                {{ Form::label('city', 'City') }}
                {{ Form::text('city', Input::old('city'), array('class'=>'form-control required', 'character-limit-max' => 280)) }}
            </div>
            <div class="form-group @if($validationErrors){{ $validationErrors->has('url') ? "has-error" : ""}}@endif">
                {{ Form::label('url', 'Website URL') }}
                {{ Form::text('url', Input::old('url'), array('class'=>'form-control required requires-url')) }}
                <span><small>Example: http://ventureforamerica.org</small></span>
            </div>
            <div class="form-group @if($validationErrors){{ $validationErrors->has('twitterPitch') ? "has-error" : ""}}@endif">
                {{ Form::label('twitterPitch', 'Twitter Pitch') }}
                <span><small>A Twitter Pitch is an elevator pitch in 140 characters or less</small></span>
                {{ Form::text('twitterPitch', Input::old('twitterPitch'), array('class'=>'form-control required character-limit', 'character-limit-max' => 140)) }}
            </div>
            <div class="form-group @if($validationErrors){{ $validationErrors->has('bio') ? "has-error" : ""}}@endif">
                {{ Form::label('bio', 'About ' . $company->name) }} {{--Company name should always be set, since they're exclusively created by admins with names --}}
                {{ Form::textarea('bio', Input::old('bio'), array('class'=>'form-control character-limit required', 'character-limit-max'=>1400, 'character-limit-min'=>140)) }}
                <span><small>140 characters minimum</small></span>
            </div>
            <div class="form-group @if($validationErrors){{ $validationErrors->has('teamAnswer') ? "has-error" : ""}}@endif">
                {{ Form::label('teamAnswer', 'Describe your team culture') }}
                {{ Form::text('teamAnswer', Input::old('teamAnswer'), array('class'=>'form-control required character-limit', 'character-limit-max'=>280)) }}
            </div>
            <div class="form-group @if($validationErrors){{ $validationErrors->has('employees') ? "has-error" : ""}}@endif">
                {{ Form::label('employees', 'How many employees does ' . $company->name . ' have?') }}
                {{ Form::text('employees', Input::old('employees'), array('class'=>'form-control required requires-int')) }}
            </div>
            <div class="form-group @if($validationErrors){{ $validationErrors->has('yearFounded') ? "has-error" : ""}}@endif">
                {{ Form::label('yearFounded', 'In what year was ' . $company->name . ' founded?') }}
                {{ Form::text('yearFounded', Input::old('yearFounded'), array('class'=>'form-control required requires-year')) }}
            </div>
            <div class="form-group @if($validationErrors){{ $validationErrors->has('twitterHandle') ? "has-error" : ""}}@endif">
                {{ Form::label('twitterHandle', 'Where is ' . $company->name . "'s Twitter handle?") }}
                <div class="input-group">
                    <span class="input-group-addon">@</span>
                    {{ Form::text('twitterHandle', Input::old('twitterHandle'), array('class'=>'form-control')) }}
                </div>
            </div>
            <div class="form-group">
                {{ Form::label('hasFellow', 'Does a VFA fellow currently work at ' . $company->name . '?') }}
                <?php $hasFellow = $company->hasFellow ? "Yes" : "No"; ?>
                {{ Form::select('hasFellow', array(""=>"","Yes"=>"Yes","No"=>"No"), $hasFellow, array('class'=>'form-control required')) }}
            </div>
            {{ Form::label('logo', 'Select a logo to upload') }}
            <p>
                @if(!empty($company->logoPath))
                    <img src="{{ $company->logoPath }}" class="img-responsive" alt="Responsive image">
                @endif
                <span class="btn btn-link btn-file">
                    <span class="btn-file-label">Browse for image...</span>{{ Form::file('logo', array('accept'=>'image/*')) }}
                </span>
            </p>
            <div class="form-group">
                {{ Form::submit('Save Profile', array('class'=>'btn btn-primary')) }}
            </div>
        </fieldset>
    {{ Form::close() }}
@else
{{-- Company was not passed in, so send an empty form that stores new company --}}
    <?php throw new Exception("New Company form not implemented."); ?>
@endif

<script src="{{ URL::to('js/pretty-file-upload.js') }}"></script>

<script></script>