 <nav class="navbar navbar-default vfa-navbar" role="navigation">
  <!-- Brand and toggle get grouped for better mobile display -->
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-navbar-collapse-1">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="{{ URL::to('/') }}"><img src="{{ URL::to('img/vfa_logo_nav_white.png') }}" alt="Venture for America logo"></a>
  </div>
  <!-- Collect the nav links, forms, and other content for toggling -->
  <div class="collapse navbar-collapse" id="bs-navbar-collapse-1">
    <ul class="nav navbar-nav navbar-right">
      @if( Auth::check())
        @if(!is_null(Auth::user()->profile))
            <!-- Dynamic nav -->
            @if( Auth::user()->role == "Admin")
              <li class=""><a href="{{ URL::to('/') }}"><i class="fa fa-home"></i> Dashboard</a></li>
              <li><a href="{{ URL::to('/users') }}"><i class="fa fa-users"></i> Users</a></li>
              <li><a href="{{ URL::to('/fellows') }}"><i class="fa fa-book"></i> Fellows</a></li>
              <li><a href="{{ URL::to('/companies') }}"><i class="fa fa-building-o"></i> Companies</a></li>
              <li><a href="{{ URL::to('/opportunities') }}"><i class="fa fa-briefcase"></i> Opportunities</a></li>
              <li><a href="{{ URL::to('archive') }}"><i class="fa fa-archive"></i> Archives</a></li>
              <li><a href="{{ URL::to('reports') }}"><i class="fa fa-tachometer"></i> Reports</a></li>
            @elseif( Auth::user()->role == "Fellow")
              <li class=""><a href="{{ URL::to('/') }}"><i class="fa fa-home"></i> Dashboard</a></li>
              <li><a href="{{ URL::to('/fellows/' . Auth::user()->profile->id) }}"><i class="fa fa-user"></i> {{ Auth::user()->firstName }}</a></li>
              <li><a href="{{ URL::to('/opportunities') }}"><i class="fa fa-briefcase"></i> Opportunities</a></li>
              <li><a href="{{ URL::to('/companies') }}"><i class="fa fa-building-o"></i> Companies</a></li>
              <li><a href="{{ URL::to('reports/companies') }}"><i class="fa fa-tachometer"></i> Status</a></li>
            @elseif( Auth::user()->role == "Hiring Manager" )
              @if(Auth::user()->profile->isProfileComplete() && Auth::user()->profile->company->isProfileComplete() && Auth::user()->profile->company->hasPublishedOpportunities())
                <li class=""><a href="{{ URL::to('/') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                <li><a href="{{ URL::to('/fellows') }}"><i class="fa fa-book"></i> Fellows</a></li>
                <li><a href="{{ URL::route('opportunities.index') }}"><i class="fa fa-briefcase"></i> Opportunities</a></li>
                <li><a href="{{ URL::to('/companies/' . Auth::user()->profile->company->id) }}"><i class="fa fa-building-o"></i> {{ Auth::user()->profile->company->name }}</a></li>
                <li><a href="{{ URL::to('/hiringmanagers/' . Auth::user()->profile->id . '/edit') }}"><i class="fa fa-user"></i> {{ Auth::user()->firstName }}</a></li>
                <li><a href="{{ URL::to('reports/fellows') }}"><i class="fa fa-tachometer"></i> Status</a></li>
              @endif
            @else
              <!-- We've got problems -->
            @endif
        @endif
        <li><a href="{{ URL::to('/logout') }}">Sign out</a></li>
      @endif
    </ul>
  </div><!-- /.navbar-collapse -->
</nav>