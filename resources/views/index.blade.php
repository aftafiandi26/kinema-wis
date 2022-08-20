@extends('layout')

@section('title')
	Home
@stop

@section('top')
    @include('assets_css_1')
@stop

@section('navbar')
	@include('navbar_top')
	@include('navbar_left')
@stop

@section('body')
<style> 

.anic {
  -webkit-animation: fade-in 1s linear infinite alternate;
  -moz-animation: fade-in 1s linear infinite alternate;
  animation: fade-in 1s linear infinite alternate;
}
@-moz-keyframes fade-in {
  0% {
    opacity: 0;
  }
  65% {
    opacity: 1;
  }
}
@-webkit-keyframes fade-in {
  0% {
    opacity: 0;
  }
  65% {
    opacity: 1;
  }
}
@keyframes fade-in {
  0% {
    opacity: 0;
  }
  65% {
    opacity: 1;
  }
}
</style>

	<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Home</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <br>
            <img style="width: 400px;" src="{!! URL::route('assets/img/globe') !!}" class="img-responsive center-block" alt="Responsive image">
            <!--  -->
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 anic">
           <!-- <img style="width: 500px;" src="{!! URL::route('assets/img/wis') !!}" class="img-responsive center-block" alt="Responsive image">-->
           <h2 style="font-family: Techno, Impact, sans-serif;"><center>Wide Information System</center></h2>
            <!--  -->
        </div>
    </div>
@stop

@section('bottom')
    @include('assets_script_1')
@stop