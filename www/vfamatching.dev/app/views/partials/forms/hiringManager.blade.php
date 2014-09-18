<?php $validationErrors = Session::has('validation_errors') ? Session::get('validation_errors') : null; ?>
{{-- If $hiringmanager exists, PUT to /hiringmanagers/{id} to update, otherwise POST to /hiringmanagers to store new  --}}
@if(isset($hiringManager))
    {{ Form::open(array('url' => 'hiringmanagers/' . $hiringManager->id, 'method' => 'PUT')) }}
        <fieldset>
            {{-- Get the user stuff out of the way up front --}}
            <div class="form-group @if($validationErrors){{ $validationErrors->has('firstName') ? "has-error" : ""}}@endif">
                {{ Form::label('firstName', 'First Name') }}
                {{ Form::text('firstName', Input::old('firstName') ? Input::old('firstName') : Auth::user()->firstName, array('class'=>'form-control required', 'character-limit-max' => 100)) }}
            </div>
            <div class="form-group @if($validationErrors){{ $validationErrors->has('lastName') ? "has-error" : ""}}@endif">
                {{ Form::label('lastName', 'Last Name') }}
                {{ Form::text('lastName', Input::old('lastName') ? Input::old('lastName') : Auth::user()->lastName, array('class'=>'form-control required', 'character-limit-max' => 100)) }}
            </div>
            <div class="form-group @if($validationErrors){{ $validationErrors->has('email') ? "has-error" : ""}}@endif">
                {{ Form::label('email', 'Email') }}
                {{ Form::text('email', Input::old('email') ? Input::old('email') : Auth::user()->email, array('class'=>'form-control required requires-email')) }}
            </div>
            {{-- Now for the hiring manager profile stuff --}}
            <div class="form-group @if($validationErrors){{ $validationErrors->has('phoneNumber') ? "has-error" : ""}}@endif">
                {{ Form::label('phoneNumber', 'What the best number to reach you at?') }}
                {{ Form::text('phoneNumber', Input::old('email') ? Input::old('email') : $hiringManager->phoneNumber, array('class'=>'form-control required requires-phone', 'placeholder' => '(646) 736-6460')) }}
            </div>
           	{{-- Now for the email opt out --}}
            <div class="form-group @if($validationErrors){{ $validationErrors->has('emailOptOut') ? "has-error" : ""}}@endif">
            	{{ Form::label(‘emailOptOut’, ‘Opt out of email notifications?’) }}
				{{ Form::checkbox(‘emailOptOut’, ‘true’) }}
            </div>
            <div class="form-group">
                {{ Form::submit('Save Profile', array('class'=>'btn btn-primary')) }}
            </div>
        </fieldset>
    {{ Form::close() }}
@else
    {{-- Fellow was not passed in, so send an empty form that stores new hiringmanager --}}
    {{ Form::open(array('url' => 'hiringmanagers', 'method' => 'POST')) }}
        <fieldset>
            {{-- Get the user stuff out of the way up front --}}
            <div class="form-group @if($validationErrors){{ $validationErrors->has('firstName') ? "has-error" : ""}}@endif">
                {{ Form::label('firstName', 'First Name') }}
                {{ Form::text('firstName', Input::old('firstName') ? Input::old('firstName') : Auth::user()->firstName, array('class'=>'form-control required', 'character-limit-max' => 100)) }}
            </div>
            <div class="form-group @if($validationErrors){{ $validationErrors->has('lastName') ? "has-error" : ""}}@endif">
                {{ Form::label('lastName', 'Last Name') }}
                {{ Form::text('lastName', Input::old('lastName') ? Input::old('lastName') : Auth::user()->lastName, array('class'=>'form-control required', 'character-limit-max' => 100)) }}
            </div>
            <div class="form-group @if($validationErrors){{ $validationErrors->has('email') ? "has-error" : ""}}@endif">
                {{ Form::label('email', 'Email') }}
                {{ Form::text('email', Input::old('email') ? Input::old('email') : Auth::user()->email, array('class'=>'form-control required requires-email')) }}
            </div>
            {{-- Now for the hiring manager profile stuff --}}
            <div class="form-group @if($validationErrors){{ $validationErrors->has('phoneNumber') ? "has-error" : ""}}@endif">
                {{ Form::label('phoneNumber', 'What the best number to reach you at?') }}
                {{ Form::text('phoneNumber', Input::old('phoneNumber'), array('class'=>'form-control required requires-phone', 'placeholder' => '(646) 736-6460')) }}
            </div>
            {{-- Now for the email opt out --}}
            <div class="form-group @if($validationErrors){{ $validationErrors->has('emailOptOut') ? "has-error" : ""}}@endif">
            	{{ Form::label(‘emailOptOut’, ‘Opt out of email notifications?’) }}
				{{ Form::checkbox(‘emailOptOut’, ‘true’) }}
            TODO: Add controls for selecting the Hiring Manager's Company!
            <div class="form-group">
                {{ Form::submit('Save Profile', array('class'=>'btn btn-primary')) }}
            </div>
        </fieldset>
    {{ Form::close() }}
@endif