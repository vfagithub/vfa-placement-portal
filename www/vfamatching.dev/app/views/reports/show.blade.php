@extends('layouts.default')

@section('header')
    {{ $heading }}
@stop

@section('content')
    @include('partials.components.table', array('data' => $data))
    @if(isset($limit))
        <div class="container">
            <div class="row">
                <div class="center-pagination">
                    <form class="form-inline" role="form" id="pagination-limit">
                        <div class="form-group">
                            Showing: 
                        </div>
                        <div class="form-group">
                            <input type="text" class="fo	rm-control required requires-int ignore-empty" id="limit" name="limit" value="{{$limit}}">
                        </div>
                        <button type="submit" class="btn btn-default">Update</button>
                    </form>
                </div>
            </div>
        </div>
    @endif
@stop
