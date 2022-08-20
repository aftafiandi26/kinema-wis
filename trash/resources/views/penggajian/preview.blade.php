@extends('layout')

@section('title')
    Laporan Penggajian Karyawan ({!! $data->bulan1 !!}-{!! $data->tahun !!}) - {!! date('d-m-Y') !!}
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
            <h1 class="page-header">Laporan Penggajian Karyawan</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            {!! Form::open(['route' => 'penggajian/print', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'form']) !!}
                <div style="width: 910px;" class="table-responsive">
                    <table border="1" style="border: 1px medium #000000;">
                        <tbody>
                            <tr>
                                <td style="border: 1px medium #000000; text-align: center; vertical-align: middle; font-weight: bold; background-color: #D9D9D9;">No.</td>
                                <td style="border: 1px medium #000000; text-align: center; vertical-align: middle; font-weight: bold; background-color: #D9D9D9;">No. Karyawan</td>
                                <td style="border: 1px medium #000000; text-align: center; vertical-align: middle; font-weight: bold; background-color: #D9D9D9;">Name</td>
                                <td style="border: 1px medium #000000; text-align: center; vertical-align: middle; font-weight: bold; background-color: #D9D9D9;">Tanggal Lahir</td>
                                <td style="border: 1px medium #000000; text-align: center; vertical-align: middle; font-weight: bold; background-color: #D9D9D9;">Jenis Kelamin</td>
                                <td style="border: 1px medium #000000; text-align: center; vertical-align: middle; font-weight: bold; background-color: #D9D9D9;">Tanggal Gabung</td>
                                <td style="border: 1px medium #000000; text-align: center; vertical-align: middle; font-weight: bold; background-color: #D9D9D9;">Jabatan</td>
                                <td style="border: 1px medium #000000; text-align: center; vertical-align: middle; font-weight: bold; background-color: #D9D9D9;">Cabang</td>
                                <td style="border: 1px medium #000000; text-align: center; vertical-align: middle; font-weight: bold; background-color: #D9D9D9;">Gaji Pokok</td>
                                <td style="border: 1px medium #000000; text-align: center; vertical-align: middle; font-weight: bold; background-color: #D9D9D9;">Tunj. Jabatan</td>
                                <td style="border: 1px medium #000000; text-align: center; vertical-align: middle; font-weight: bold; background-color: #D9D9D9;">Tunj. Rumah</td>
                            </tr>

                            @foreach($karyawan as $key => $value)
                                <tr>
                                    <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">{!! $key + 1 !!}</td>
                                    <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">{!! $value->nk !!}</td>
                                    <td style="border: 1px solid #000000; text-align: center; vertical-align: left;">{!! $value->name !!}</td>
                                    <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">{!! $value->tanggal_lahir !!}</td>
                                    <td style="border: 1px solid #000000; text-align: center; vertical-align: left;">{!! $value->jenis_kelamin !!}</td>
                                    <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">{!! date_format(date_create($value->created_at), "d-m-Y") !!}</td>
                                    <td style="border: 1px solid #000000; text-align: center; vertical-align: left;">{!! $value->jabatan !!}</td>
                                    <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">{!! $value->cabang !!}</td>
                                    <td style="border: 1px solid #000000; text-align: center; vertical-align: left;">Rp. {!! number_format($value->gaji_pokok, 0, ",", ".") !!}</td>
                                    <td style="border: 1px solid #000000; text-align: center; vertical-align: left;">Rp. @if ($value->jabatan === "Kepala Mekanik") 100,000 @else 0 @endif</td>
                                    <td style="border: 1px solid #000000; text-align: center; vertical-align: left;">Rp. @if ($value->tempat_tinggal === "Rumah Pribadi") 100,000 @else 0 @endif</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <br>
                </div>

                {!! Form::hidden('bulan', $data->bulan) !!}
                {!! Form::hidden('tahun', $data->tahun) !!}
                {!! Form::submit('Cetak Laporan', ['class' => 'btn btn-sm btn-primary', 'id' => 'cetak']) !!}
                {!! Form::submit('Back', ['class' => 'btn btn-sm btn-success', 'id' => 'back']) !!}
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('bottom')
    @include('assets_script_1')
@stop

@section('script')
    $('#cetak').click(function() {
        if(confirm('Anda Yakin Mencetak Laporan Penggajian Karyawan ({!! $data->bulan1 !!}-{!! $data->tahun !!}) ?')) {
            $('#form').submit();
        }
        return false;
    });

    $('#back').click(function() {
        $('#form').append('{!! Form::hidden('redirect', 1) !!}').submit();
    });
@stop