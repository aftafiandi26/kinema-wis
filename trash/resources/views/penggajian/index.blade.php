@extends('layout')

@section('title')
    Penggajian Karyawan
@stop

@section('top')
    @include('assets_css_1')
@stop

@section('navbar')
    @include('navbar_top')
    @include('navbar_left', [
        'c01' => 'active'
    ])
@stop

@section('body')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Penggajian Karyawan</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5 class="panel-title">
                        <b>Form Penggajian Karyawan</b>
                    </h5>
                </div>

                <div class="panel-body">
                    {!! Form::open(['route' => 'penggajian/preview', 'role' => 'form', 'autocomplete' => 'off']) !!}
                        <div class="row">
                            <div class="col-lg-4">
                                @if ($errors->has('bulan'))
                                    <div class="form-group has-error">
                                @else
                                    <div class="form-group">
                                @endif
                                    {!! Form::label('bulan', 'Bulan') !!}<font color="red"> (*)</font>
                                    {!! Form::select('bulan', $bulan, old('bulan'), ['class' => 'form-control', 'maxlength' => 9, 'autofocus' => true, 'required' => true]) !!}
                                    <p class="help-block">{!! $errors->first('bulan') !!}</p>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                @if ($errors->has('tahun'))
                                    <div class="form-group has-error">
                                @else
                                    <div class="form-group">
                                @endif
                                    {!! Form::label('tahun', 'Tahun') !!}<font color="red"> (*)</font>
                                    {!! Form::select('tahun', $tahun, old('tahun'), ['class' => 'form-control', 'maxlength' => 4, 'required' => true]) !!}
                                    <p class="help-block">{!! $errors->first('tahun') !!}</p>
                                </div>
                            </div>
                        </div>

                        {!! Form::submit('Proses', ['class' => 'btn btn-sm btn-success']) !!}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop

@section('bottom')
    @include('assets_script_1')
@stop