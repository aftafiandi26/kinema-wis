@extends('layout')

@section('title')
    Change Data  Jabatan
@stop

@section('top')
    @include('assets_css_1')
@stop

@section('navbar')
    @include('navbar_top')
    @include('navbar_left', [
        'c1u' => 'collape in',
        'c1' => 'active', 'c12' => 'active'
    ])
@stop

@section('body')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Change Data  Jabatan</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5 class="panel-title">
                        <b>Form Change Data  Jabatan</b>
                    </h5>
                </div>

                <div class="panel-body">
                    {!! Form::open(['route' => ['mgmt-data/jabatan/update', $jabatan->id], 'role' => 'form', 'autocomplete' => 'off']) !!}
                        <div class="row">
                            <div class="col-lg-4">
                                @if ($errors->has('jabatan'))
                                    <div class="form-group has-error">
                                @else
                                    <div class="form-group">
                                @endif
                                    {!! Form::label('jabatan', 'Jabatan') !!}<font color="red"> (*)</font>
                                    {!! Form::text('jabatan', $jabatan->jabatan, ['class' => 'form-control', 'placeholder' => 'Jabatan', 'maxlength' => 30, 'autofocus' => true, 'required' => true]) !!}
                                    <p class="help-block">{!! $errors->first('jabatan') !!}</p>
                                </div>
                            </div>
                        </div>

                        {!! Form::submit('Change', ['class' => 'btn btn-sm btn-success']) !!}
                        <a class="btn btn-sm btn-warning" href="{!! URL::route('mgmt-data/jabatan') !!}">Back</a>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop

@section('bottom')
    @include('assets_script_1')
@stop