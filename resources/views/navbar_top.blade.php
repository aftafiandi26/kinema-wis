<style type="text/css">
    .anic {
  -webkit-animation: fade-in 1.7s linear infinite alternate;
  -moz-animation: fade-in 1.7s linear infinite alternate;
  animation: fade-in 1.7s linear infinite alternate;
}
  .anic2 {
  -webkit-animation: fade-in 2.3s linear infinite alternate;
  -moz-animation: fade-in 2.3s linear infinite alternate;
  animation: fade-in 2.3s linear infinite alternate;
}
</style>
<div class="navbar-header" style="height: 80px;">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="{!! URL::route('index') !!}">
      <!--   <img width="60px" height="40px" style="margin-top: 15px;" src="{!! URL::route('assets/img/logo') !!}"> -->
      <img width="60px" height="40px" style="margin-top: 12px;" src="{{asset('assets/Graphic2.png')}}">
    </a>
    <a href="{!! URL::route('index') !!}" class="navbar-brand text-center" style=" margin-left: -30px;"><b>Wide Information System</b><br> <span style="font-size: 14px;" class="anic">Kinema Systrans Multimedia</span><br><span style="font-size: 14px;" class="anic2">Infinite Studios</span></a>
</div>

<ul class="nav navbar-top-links navbar-right" style="margin-top: 15px;">
    <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
            <i></i> {!! Auth::user()->first_name !!} {!! Auth::user()->last_name !!} &nbsp&nbsp<i class="fa fa-caret-down"></i>
        </a>

        <ul class="dropdown-menu dropdown-user">
            <li>
                <a href="{!! URL::route('get_change-password') !!}"><i class="fa fa-lock fa-fw"></i> Change Password </a>
            </li>

            <li class="divider"></li>

            <li>
                <a href="{!! URL::route('logout') !!}"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
            </li>
        </ul>
    </li>
</ul>
