<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3><strong><a href="{{ URL::route('fellows.show', array('fellows'=>$fellow->id)) }}">{{ $fellow->firstName . ' ' . $fellow->lastName }}</a></strong></h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-3">
                @if(!empty($fellow->displayPicturePath))                    
                    <div class="row">
                        <div class="col-xs-8 col-xs-offset-2">
                            <a href="{{ URL::route('fellows.show', array('fellows'=>$fellow->id)) }}"><img src="{{ $fellow->displayPicturePath }}" class="img-responsive fellow-display-pic" alt="Fellow display pic"></a>
                        </div>
                    </div>
                @endif
                </div>
                <div class="col-sm-9" id="fellow-list-bio">
                    @include('partials.components.skills', array('skills' => $fellow->fellowSkills))
                    <strong>Bio</strong>: 
                    {{ $fellow->bio }}
                    <div class="row list-summary">
                        <div class="col-md-4"><strong>School: </strong>{{ $fellow->school }}</div>
                        <div class="col-md-4"><strong>Major: </strong>{{ $fellow->degree }} in {{ $fellow->major }}</div>
                        <div class="col-md-4"><strong>Hometown: </strong>{{ $fellow->hometown }}</div>
                        <div class="col-md-4"><a class="btn btn-primary form-control" href="{{ $fellow->resumePath }}" target="_blank"><i class="fa fa-cloud-download"></i> View Résumé</a></div>
                    </div>
                </div>
            </div>
            @if(Auth::user()->role == "Admin")
            <div class="pull-right admin-controls">
                @if( $fellow->isPublished )
                    {{ Form::open(array('url' => 'fellows/'.$fellow->id.'/unpublish', 'method' => 'PUT', 'class'=>'publishable-form')) }}
                        <a href="#" class="btn btn-danger form-control publishable"><i class="fa fa-eye-slash"></i> Unpublish</a>
                    {{ Form::close() }}
                @else
                    {{ Form::open(array('url' => 'fellows/'.$fellow->id.'/publish', 'method' => 'PUT', 'class'=>'publishable-form')) }}
                        <a href="#" class="btn btn-danger form-control publishable"><i class="fa fa-eye"></i> Publish</a>
                    {{ Form::close() }}
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {  
    //unbind so the click only fires once
    $('.publishable').unbind().click(function(e){
        $(this).parent('.publishable-form').submit();
        e.preventDefault();//don't follow the actual link
    });
});
</script>