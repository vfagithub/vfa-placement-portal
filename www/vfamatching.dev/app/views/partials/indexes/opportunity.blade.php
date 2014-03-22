<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3>@include('partials.links.opportunity', array('opportunity' => $opportunity))</h3>
        </div>
        <div class="panel-body">
            <h4><strong><em>{{ $opportunity->teaser }}</em></strong></h4>
            @include('partials.components.tags', array('tags' => $opportunity->opportunityTags))
            <div class="row list-summary">
                <div class="col-md-3"><h4>@include('partials.links.company', array('company' => $opportunity->company))</h4></div>
                <div class="col-md-3"><strong>City: </strong>{{ $opportunity->city }}</div>
                <div class="col-md-3"><strong>Date Added: </strong>{{ Carbon::createFromFormat('Y-m-d H:i:s', $opportunity->created_at)->diffForHumans() }}</div>
                <div class="col-md-3">@include('partials.components.pitch-button')</div>
            </div>
            @if(Auth::user()->role == "Admin" || Auth::user()->role == "Hiring Manager")
                <div class="pull-right admin-controls">
                    @if( $opportunity->isPublished )
                        {{ Form::open(array('url' => 'opportunities/'.$opportunity->id.'/unpublish', 'method' => 'PUT', 'class'=>'publishable-form')) }}
                            <a href="#" class="btn btn-danger form-control verify-submit"><i class="fa fa-eye-slash"></i> Unpublish</a>
                        {{ Form::close() }}
                    @else
                        {{ Form::open(array('url' => 'opportunities/'.$opportunity->id.'/publish', 'method' => 'PUT', 'class'=>'publishable-form')) }}
                            <a href="#" class="btn btn-danger form-control publishable"><i class="fa fa-eye"></i> Publish</a>
                        {{ Form::close() }}
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<script type="text/javascript">
    $('.pitch-submit').unbind().click(function(e){
        $(this).parent().parent().find('.pitch-form').submit();
        e.preventDefault();//don't follow the actual link
    });

    $(document).ready(function() {  
        //unbind so the click only fires once
        $('.publishable').unbind().click(function(e){
            $(this).parent('.publishable-form').submit();
            e.preventDefault();//don't follow the actual link
        });

        $('.verify-submit').unbind().click(function(e){
            publishableForm = $(this).parent('.publishable-form');        
            noty({
              text: 'Fellows will no longer be able to view this Opportunity if you unpublish it. Would you like to continue?',
              buttons: [
                {addClass: 'btn btn-danger', text: '<i class="fa fa-eye-slash"></i> Unpublish', onClick: function($noty) {
                    publishableForm.submit();
                    e.preventDefault();//don't follow the actual link
                    $noty.close();
                  }
                },
                {addClass: 'btn btn-default', text: 'Cancel', onClick: function($noty) {
                    $noty.close();
                  }
                }
              ]
            });
        });
    });
</script>