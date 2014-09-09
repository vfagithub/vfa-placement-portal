@extends('layouts.default')

@section('header')
    Reports
@stop

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-4"><a href="{{ URL::to('reports') . '/fellows' }}" class="btn btn-primary form-control"><i class="fa fa-tachometer"></i> Unplaced Fellows</a></div>
			<div class="col-md-4"><a href="{{ URL::to('reports') . '/companies' }}" class="btn btn-primary form-control"><i class="fa fa-tachometer"></i> Unfilled Opportunities</a></div>
			<div class="col-md-4"><a href="{{ URL::to('reports') . '/placementStatuses' }}" class="btn btn-primary form-control"><i class="fa fa-tachometer"></i> Recent Placement Status Updates</a></div>
			<div class="col-md-4"><a href="{{ URL::to('reports') . '/onSites' }}" class="btn btn-primary form-control"><i class="fa fa-tachometer"></i> Upcoming Site Visits</a></div>
			<div class="col-md-4"><a href="{{ URL::to('reports') . '/phoneInterviews' }}" class="btn btn-primary form-control"><i class="fa fa-tachometer"></i> Upcoming Phone Interviews</a></div>

		</div>
	</div>
@stop
