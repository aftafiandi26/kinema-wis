<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="shortcut icon" href="{{asset('assets/iconic2.png')}}">
        <title>Login - WIS</title>
        @include('assets_css_1')
    </head>
<link href="https://fonts.googleapis.com/css?family=Notable&display=swap" rel="stylesheet"> 
<link href="https://fonts.googleapis.com/css?family=Lobster+Two&display=swap" rel="stylesheet"> 
<link href="https://fonts.googleapis.com/css?family=Sniglet&display=swap" rel="stylesheet"> 
<style type="text/css">
    body {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  width: 100%;
  height: 100%;
  background-color: skyblue;
  background-image: -webkit-linear-gradient(90deg, skyblue 10%, steelblue 100%);
  background-attachment: fixed;
  background-size: 100% 100%;
  overflow: hidden;
  font-family: "Georgia", cursive;
  -webkit-font-smoothing: antialiased;
}

::selection {
  background: transparent;
}
/* CLOUDS */
body:before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  width: 0;
  height: 0;
  margin: auto;
  border-radius: 100%;
  background: transparent;
  display: block;
  /*box-shadow: 0 0 150px 100px rgba(255, 255, 255, 0.6),
    200px 0 200px 150px rgba(255, 255, 255, 0.6),
    -250px 0 300px 150px rgba(255, 255, 255, 0.6),
    550px 0 300px 200px rgba(255, 255, 255, 0.6),
    -550px 0 300px 200px rgba(255, 255, 255, 0.6);*/
}
/* JUMP */
h1 {
  cursor: default;
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  width: 100%;
  height: 100px;
  margin: auto;
  display: block;
  text-align: center;
}

h1 span {
  position: relative;
  top: 20px;
  display: inline-block;
  -webkit-animation: bounce 0.7s ease infinite alternate;
  font-size: 24px;
  color: #fff;
  text-shadow: 0 1px 0 #ccc, 0 2px 0 #ccc, 0 3px 0 #ccc, 0 4px 0 #ccc,
    0 5px 0 #ccc, 0 6px 0 transparent, 0 7px 0 transparent, 0 8px 0 transparent,
    0 9px 0 transparent, 0 10px 10px rgba(0, 0, 0, 0.4);
}

h1 span:nth-child(2) {
  -webkit-animation-delay: 0.1s;
}

h1 span:nth-child(3) {
  -webkit-animation-delay: 0.2s;
}

h1 span:nth-child(4) {
  -webkit-animation-delay: 0.3s;
}

h1 span:nth-child(5) {
  -webkit-animation-delay: 0.4s;
}

h1 span:nth-child(6) {
  -webkit-animation-delay: 0.5s;
}

h1 span:nth-child(7) {
  -webkit-animation-delay: 0.6s;
}

h1 span:nth-child(8) {
  -webkit-animation-delay: 0.2s;
}

h1 span:nth-child(9) {
  -webkit-animation-delay: 0.3s;
}

h1 span:nth-child(10) {
  -webkit-animation-delay: 0.4s;
}

h1 span:nth-child(11) {
  -webkit-animation-delay: 0.5s;
}

h1 span:nth-child(12) {
  -webkit-animation-delay: 0.6s;
}

h1 span:nth-child(13) {
  -webkit-animation-delay: 0.7s;
}

h1 span:nth-child(14) {
  -webkit-animation-delay: 0.8s;
}
h1 span:nth-child(15) {
  -webkit-animation-delay: 0.4s;
}
h1 span:nth-child(16) {
  -webkit-animation-delay: 0.6s;
}
h1 span:nth-child(17) {
  -webkit-animation-delay: 0.8s;
}
h1 span:nth-child(18) {
  -webkit-animation-delay: 1s;
}
h1 span:nth-child(19) {
  -webkit-animation-delay: 0.7s;
}
h1 span:nth-child(20) {
  -webkit-animation-delay: 0.4s;
}
h1 span:nth-child(21) {
  -webkit-animation-delay: 0.1s;
}

/* ANIMATION */
@-webkit-keyframes bounce {
  100% {
    top: -20px;
    text-shadow: 0 1px 0 #ccc, 0 2px 0 #ccc, 0 3px 0 #ccc, 0 4px 0 #ccc,
      0 5px 0 #ccc, 0 6px 0 #ccc, 0 7px 0 #ccc, 0 8px 0 #ccc, 0 9px 0 #ccc,
      0 50px 25px rgba(0, 0, 0, 0.2);
  }
}
/* ////////////////////// */
</style>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-md-offset-4" style="margin-top: 10%;"> 
                 <!--  <div class="col-md-12">
                   <img src="{{asset('assets/Infinite_Studios_kinema')}}">
                  </div> -->
                  <div class="col-md-12">
                    <!-- <a href=""><center> <img width="60px" height="40px" style="margin-top: 15px;" src="{!! URL::route('assets/img/logo') !!}"></center></a> -->
                    <a href=""><center> <img width="100px" height="60px"  src="{{asset('assets/Graphic2.png')}}"></center></a>
                  </div>                
                  <div class="col-md-12" style="margin-bottom: 60px;">
                  <center>
                   <h1 style="margin-bottom: -90px;">
                            <span>W</span><span>i</span><span>d</span><span>e</span> <span style="margin-left: 5px;">I</span><span>n</span><span>f</span><span>o</span><span>r</span><span>m</span><span>a</span><span>t</span><span>i</span><span>o</span><span>n</span> <span>S</span><span>y</span><span>s</span><span>t</span><span>e</span><span>m</span>
                         <!--    <span>Wide</span><span style="margin-left: 7px;">Information</span><span style="margin-left: 7px;">System</span> -->

                    </h1>  
                </center>
               </div>
                    <div class="col-md-12">
                       @if (Session::get('getError'))
                        <div class="alert alert-danger alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {!! Session::get('getError') !!}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {!! implode('', $errors->all('<li>:message</li>')) !!}                          
                        </div>
                    @endif

                    @if (Session::has('message'))
                        <div class="alert alert-info alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {!! Session::get('message') !!}                            
                        </div>
                    @endif
                    </div>
                 

               <!--  <div class="row">
                    <div class="col-md-4 col-md-offset-4">
                          <img style="width: 400px;  margin-bottom: 15px; margin-left:-25px; position: static;" src="{!! URL::route('assets/img/opening') !!}">
                    </div>
                </div> -->         
               
                <div class="col-md-12">
                   <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5 class="panel-title">
                                <b>Login - WIS</b>
                            </h5>
                        </div>

                        <div class="panel-body">
                            {!! Form::open(['route' => 'login', 'role' => 'form', 'autocomplete' => 'off']) !!}
                                <fieldset>
                                    @if ($errors->has('username'))
                                        <div class="form-group input-group has-error">
                                    @else
                                        <div class="form-group input-group">
                                    @endif
                                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                        {!! Form::text('username', old('username'), ['class' => 'form-control', 'placeholder' => 'Username', 'maxlength' => 20, 'autofocus' => true, 'required' => true]) !!}
                                    </div>

                                    @if ($errors->has('password'))
                                        <div class="form-group input-group has-error">
                                    @else
                                        <div class="form-group input-group">
                                    @endif
                                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                        {!! Form::input('password', 'password', old('password'), ['class' => 'form-control' , 'placeholder' => 'Password', 'maxlength' => 30, 'required' => true]) !!}
                                    </div>
                                    {!! Form::submit('Login', ['class' => 'btn btn-lg btn-success btn-block']) !!}
                                </fieldset>
                          
                            {!! Form::close() !!}

                        </div>

                    </div>
                </div>
                      <!--     <p><b  style="color: white; font-size: 18px;"><center>PT. KINEMA SYSTRANS MULTIMEDIA <br> INFINITE STUDIOS</b></center></p> -->
                <a href=""><center> <img width="300px" height="60px"  src="{{asset('assets/Infinite_Studios_kinema.png')}}"></center></a>
                <br>
                <a href=""><center> <img width="250px" height="60px"  src="{{asset('assets/Infinite_Studios_Logo-03.png')}}"></center></a>
                </div>

            </div>

        </div>

        @include('assets_script_1')
    </body>
</html>

