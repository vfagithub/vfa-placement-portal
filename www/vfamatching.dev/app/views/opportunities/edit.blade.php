@extends('layouts.default')

@section('header')
    Opportunity Profile
@stop

@section('content')
    	<div class="container">
            <div class="row">
                <div class="col-md-6">
                    @include('partials.forms.opportunity', array('opportunity' => $opportunity))
                </div>
            </div>   
        </div>
@stop
