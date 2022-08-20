@extends('layout')

@section('title')
    Data Karyawan
@stop

@section('top')
    @include('assets_css_1')
    @include('assets_css_2')
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
            <h1 class="page-header">Data Karyawan</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div align="right">
                <a style="margin-bottom: 15px;" class="btn btn-sm btn-primary" data-original-title="Tambah" data-toggle="tooltip" data-placement="top" href="{!! URL::route('mgmt-data/karyawan/create') !!}"><span class="fa fa-plus"></span></a>
            </div>

            <table class="table table-striped table-bordered table-hover" width="100%" id="tables">
                <thead>
                    <tr>
                        <td>ID</td>
                        <td>No. Karyawan</td>
                        <td>NIK</td>
                        <td>Name</td>
                        <td>Alamat</td>
                        <td>Tanggal Lahir</td>
                        <td>Cabang</td>
                        <td>Jabatan</td>
                        <td>Golongan Gaji</td>
                        <td style="width: 78px;">Action</td>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="showModal" tabindex="-1" role="dialog" aria-labelledby="showModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" id="modal-content">
                <!--  -->
            </div>
        </div>
    </div>
@stop

@section('bottom')
    @include('assets_script_1')
    @include('assets_script_2')
@stop

@section('script')
    $('[data-toggle="tooltip"]').tooltip();

    $('#tables').DataTable({
        "columnDefs": [
            { className: "never", "searchable": false, "orderable": false, "visible": false, "targets": 0 },
            { className: "all", "searchable": false, "orderable": false, "visible": true, "targets": 9 }
        ],
        "order": [
            [6, "asc" ]
        ],
        responsive: true,
        ajax: '{!! URL::route("mgmt-data/karyawan/getindex") !!}'
    });
    
    $(document).on('click','#tables tr td a[title="Detail"]',function(e) {
        var id = $(this).attr('data-role');

        $.ajax({
            url: id, 
            success: function(e) {
                $("#modal-content").html(e);
            }
        });
    });
@stop