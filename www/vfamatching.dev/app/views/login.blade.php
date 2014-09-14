@extends('layouts.default')

@section('header')
Placement Portal Login
@stop

@section('content')

<div class="container">
    <div id="login" class="well col-xs-10 col-xs-offset-1 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
        {{ Form::open(array('route' => 'login')) }}
            <fieldset>
                <!-- email field -->
                <div class="form-group">
                    {{ Form::label('email', 'Email') }}<br/>
                    {{ Form::text('email', Input::old('email'), array('class'=>'form-control required requires-email')) }}
                </div>

                <!-- password field -->
                <div class="form-group">
                    {{ Form::label('password', 'Password') }}<br/>
                    {{ Form::password('password', array('class'=>'form-control required')) }}
                </div>
                <p><a href = "{{URL::to('password/reset') }}">Forgot password?</a></p>

                <!-- submit button -->
                <p>{{ Form::submit('Login',  array('class'=>'btn btn-primary form-control')) }}</p>
            </fieldset>
        {{ Form::close() }}
                
    </div>
</div>

@stop