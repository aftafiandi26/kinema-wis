@extends('layout')

@section('title')
    Tambah Data Golongan Gaji
@stop

@section('top')
    @include('assets_css_1')
@stop

@section('navbar')
    @include('navbar_top')
    @include('navbar_left', [
        'c1u' => 'collape in',
        'c1' => 'active', 'c13' => 'active'
    ])
@stop

@section('body')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Tambah Data Golongan Gaji</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5 class="panel-title">
                        <b>Form Tambah Data Golongan Gaji</b>
                    </h5>
                </div>

                <div class="panel-body">
                    {!! Form::open(['route' => 'mgmt-data/golongan-gaji/store', 'role' => 'form', 'autocomplete' => 'off']) !!}
                        <div class="row">
                            <div class="col-lg-4">
                                @if ($errors->has('golongan'))
                                    <div class="form-group has-error">
                                @else
                                    <div class="form-group">
                                @endif
                                    {!! Form::label('golongan', 'Golongan') !!}<font color="red"> (*)</font>
                                    {!! Form::text('golongan', old('golongan'), ['class' => 'form-control', 'placeholder' => 'Golongan', 'maxlength' => 2, 'autofocus' => true, 'required' => true]) !!}
                                    <p class="help-block">{!! $errors->first('golongan') !!}</p>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                @if ($errors->has('jabatan'))
                                    <div class="form-group has-error">
                                @else
                                    <div class="form-group">
                                @endif
                                    {!! Form::label('jabatan', 'Jabatan') !!}<font color="red"> (*)</font>
                                    {!! Form::select('jabatan', $jabatan, old('jabatan'), ['class' => 'form-control', 'maxlength' => 30, 'required' => true]) !!}
                                    <p class="help-block">{!! $errors->first('jabatan') !!}</p>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                @if ($errors->has('gaji_pokok'))
                                    <div class="form-group has-error">
                                @else
                                    <div class="form-group">
                                @endif
                                    {!! Form::label('gaji_pokok', 'Gaji Pokok') !!}<font color="red"> (*)</font>
                                    {!! Form::number('gaji_pokok', old('gaji_pokok'), ['class' => 'form-control', 'placeholder' => 'Gaji Pokok', 'maxlength' => 8, 'min' => 500, 'max' => 99999500, 'step' => 500, 'required' => true]) !!}
                                    <p class="help-block">{!! $errors->first('gaji_pokok') !!}</p>
                                </div>
                            </div>
                        </div>

                        {!! Form::submit('Tambah', ['class' => 'btn btn-sm btn-success']) !!}
                        <a class="btn btn-sm btn-warning" href="{!! URL::route('mgmt-data/golongan-gaji') !!}">Back</a>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop

@section('bottom')
    @include('assets_script_1')
@stop