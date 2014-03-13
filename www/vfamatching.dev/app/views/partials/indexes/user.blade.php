<div class="col-md-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-12">
                    <h3><strong>{{ $user->firstName . ' ' . $user->lastName }}</strong></h3>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <h4><i class="fa fa-user"></i> {{ $user->role }}</h4>
            <h4><small><strong><em>{{ $user->email }}</em></strong></small></h4>
            @if(isset($user->lastLogin))
            <strong>Last login: </strong>{{ Carbon::createFromFormat('Y-m-d H:i:s', $user->lastLogin)->diffForHumans(); }}
            @else
            <strong>Last login: </strong>Never
            @endif
            <div class="pull-right admin-controls">
                @if($user->role != "Admin")
                    <a href="{{ URL::to('users/'.$user->id.'/backdoor') }}" class="btn btn-danger form-control"><i class="fa fa-sign-in"></i> Login as User</a>
                @endif
                <a href="{{ URL::to('users/'.$user->id.'/password-reset') }}" class="btn btn-danger form-control verify-{{ $user->id }}"><i class="fa fa-lock"></i> Reset Password</a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {  

    $('.verify-{{ $user->id }}').unbind().click(function(e){  
        e.preventDefault();
        noty({
          text: 'Reseting a User\'s password cannot be undone. Would you like to continue?',
          buttons: [
            {addClass: 'btn btn-danger', text: '<i class="fa fa-lock"></i> Reset Password', onClick: function($noty) {
                window.location.href = "{{ URL::to('users/'.$user->id.'/password-reset') }}";
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