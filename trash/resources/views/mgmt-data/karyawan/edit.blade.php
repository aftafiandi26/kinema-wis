@extends('layout')

@section('title')
    Change Data  Karyawan
@stop

@section('top')
    @include('assets_css_1')
    @include('assets_css_3')
@stop

@section('navbar')
    @include('navbar_top')
    @include('navbar_left', [
        'c1u' => 'collape in',
        'c1' => 'active', 'c14' => 'active'
    ])
@stop

@section('body')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Change Data  Karyawan</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5 class="panel-title">
                        <b>Form Change Data  Karyawan</b>
                    </h5>
                </div>

                <div class="panel-body">
                    {!! Form::open(['route' => ['mgmt-data/karyawan/update', $karyawan->id], 'role' => 'form', 'autocomplete' => 'off']) !!}
                        <div class="row">
                            <div class="col-lg-3">
                                @if ($errors->has('nk'))
                                    <div class="form-group has-error">
                                @else
                                    <div class="form-group">
                                @endif
                                    {!! Form::label('nk', 'No. Karyawan') !!}<font color="red"> (*)</font>
                                    {!! Form::text('nk', $karyawan->nk, ['class' => 'form-control', 'placeholder' => 'No. Karyawan', 'maxlength' => 8, 'readonly' => true]) !!}
                                    <p class="help-block">{!! $errors->first('nk') !!}</p>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                @if ($errors->has('nik'))
                                    <div class="form-group has-error">
                                @else
                                    <div class="form-group">
                                @endif
                                    {!! Form::label('nik', 'NIK') !!}<font color="red"> (*)</font>
                                    {!! Form::text('nik', $karyawan->nik, ['class' => 'form-control', 'placeholder' => 'NIK', 'maxlength' => 16, 'autofocus' => true, 'required' => true]) !!}
                                    <p class="help-block">{!! $errors->first('nik') !!}</p>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                @if ($errors->has('name'))
                                    <div class="form-group has-error">
                                @else
                                    <div class="form-group">
                                @endif
                                    {!! Form::label('name', 'Name') !!}<font color="red"> (*)</font>
                                    {!! Form::text('name', $karyawan->name, ['class' => 'form-control', 'placeholder' => 'Name', 'maxlength' => 50, 'required' => true]) !!}
                                    <p class="help-block">{!! $errors->first('name') !!}</p>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                @if ($errors->has('alamat'))
                                    <div class="form-group has-error">
                                @else
                                    <div class="form-group">
                                @endif
                                    {!! Form::label('alamat', 'Alamat') !!}<font color="red"> (*)</font>
                                    {!! Form::text('alamat', $karyawan->alamat, ['class' => 'form-control', 'placeholder' => 'Alamat', 'maxlength' => 100, 'required' => true]) !!}
                                    <p class="help-block">{!! $errors->first('alamat') !!}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-3">
                                @if ($errors->has('jenis_kelamin'))
                                    <div class="form-group has-error">
                                @else
                                    <div class="form-group">
                                @endif
                                    {!! Form::label('jenis_kelamin', 'Jenis Kelamin') !!}<font color="red"> (*)</font>
                                    {!! Form::select('jenis_kelamin', $jenis_kelamin, $karyawan->jenis_kelamin, ['class' => 'form-control', 'maxlength' => 9, 'required' => true]) !!}
                                    <p class="help-block">{!! $errors->first('jenis_kelamin') !!}</p>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                @if ($errors->has('tempat_lahir'))
                                    <div class="form-group has-error">
                                @else
                                    <div class="form-group">
                                @endif
                                    {!! Form::label('tempat_lahir', 'Tempat Lahir') !!}<font color="red"> (*)</font>
                                    {!! Form::text('tempat_lahir', $karyawan->tempat_lahir, ['class' => 'form-control', 'placeholder' => 'Tempat Lahir', 'maxlength' => 50, 'required' => true]) !!}
                                    <p class="help-block">{!! $errors->first('tempat_lahir') !!}</p>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                @if ($errors->has('tanggal_lahir'))
                                    <div class="form-group has-error">
                                @else
                                    <div class="form-group">
                                @endif
                                    {!! Form::label('tanggal_lahir', 'Tanggal Lahir (dd-mm-yyyy)') !!}<font color="red"> (*)</font>
                                    {!! Form::text('tanggal_lahir', $karyawan->tanggal_lahir, ['class' => 'form-control', 'placeholder' => 'Tanggal Lahir (dd-mm-yyyy)', 'maxlength' => 10, 'required' => true]) !!}
                                    <p class="help-block">{!! $errors->first('tanggal_lahir') !!}</p>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                @if ($errors->has('no_telp'))
                                    <div class="form-group has-error">
                                @else
                                    <div class="form-group">
                                @endif
                                    {!! Form::label('no_telp', 'No. Telepon') !!}<font color="red"> (*)</font>
                                    {!! Form::text('no_telp', $karyawan->no_telp, ['class' => 'form-control', 'placeholder' => 'No. Telepon', 'maxlength' => 12, 'required' => true]) !!}
                                    <p class="help-block">{!! $errors->first('no_telp') !!}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-3">
                                @if ($errors->has('status_perkawinan'))
                                    <div class="form-group has-error">
                                @else
                                    <div class="form-group">
                                @endif
                                    {!! Form::label('status_perkawinan', 'Status Perkawinan') !!}<font color="red"> (*)</font>
                                    {!! Form::select('status_perkawinan', $status_perkawinan, $karyawan->status_perkawinan, ['class' => 'form-control', 'maxlength' => 7, 'required' => true]) !!}
                                    <p class="help-block">{!! $errors->first('status_perkawinan') !!}</p>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                @if ($errors->has('tempat_tinggal'))
                                    <div class="form-group has-error">
                                @else
                                    <div class="form-group">
                                @endif
                                    {!! Form::label('tempat_tinggal', 'Tempat Tinggal') !!}<font color="red"> (*)</font>
                                    {!! Form::select('tempat_tinggal', $tempat_tinggal, $karyawan->tempat_tinggal, ['class' => 'form-control', 'maxlength' => 15, 'required' => true]) !!}
                                    <p class="help-block">{!! $errors->first('tempat_tinggal') !!}</p>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                @if ($errors->has('cabang'))
                                    <div class="form-group has-error">
                                @else
                                    <div class="form-group">
                                @endif
                                    {!! Form::label('cabang', 'Cabang') !!}<font color="red"> (*)</font>
                                    {!! Form::select('cabang', $cabang, $karyawan->cabang, ['class' => 'form-control', 'maxlength' => 30, 'required' => true]) !!}
                                    <p class="help-block">{!! $errors->first('cabang') !!}</p>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                @if ($errors->has('golongan_gaji'))
                                    <div class="form-group has-error">
                                @else
                                    <div class="form-group">
                                @endif
                                    {!! Form::label('golongan_gaji', 'Golongan Gaji') !!}<font color="red"> (*)</font>
                                    {!! Form::select('golongan_gaji', $golongan_gaji, $karyawan->golongan_gaji, ['class' => 'form-control', 'maxlength' => 2, 'required' => true]) !!}
                                    <p class="help-block">{!! $errors->first('golongan_gaji') !!}</p>
                                </div>
                            </div>
                        </div>

                        {!! Form::submit('Change', ['class' => 'btn btn-sm btn-success']) !!}
                        <a class="btn btn-sm btn-warning" href="{!! URL::route('mgmt-data/karyawan') !!}">Back</a>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop

@section('bottom')
    @include('assets_script_1')
    @include('assets_script_3')
@stop

@section('script')
    $("#tanggal_lahir").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: "1930:+0",
        dateFormat: 'dd-mm-yy',
        maxDate: 'dd-mm-yy',
        monthNamesShort: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
    });
@stop