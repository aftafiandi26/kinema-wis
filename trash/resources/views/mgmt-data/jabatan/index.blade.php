@extends('layout')

@section('title')
    Data Jabatan
@stop

@section('top')
    @include('assets_css_1')
    @include('assets_css_2')
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
            <h1 class="page-header">Data Jabatan</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
			<div align="right">
				<a style="margin-bottom: 15px;" class="btn btn-sm btn-primary" data-original-title="Tambah" data-toggle="tooltip" data-placement="top" href="{!! URL::route('mgmt-data/jabatan/create') !!}"><span class="fa fa-plus"></span></a>
			</div>

			<table class="table table-striped table-bordered table-hover" width="100%" id="tables">
				<thead>
				    <tr>
                        <td>ID</td>
                        <td>Jabatan</td>
                        <td style="width: 52px;">Action</td>
				    </tr>
				</thead>
			</table>
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
            { className: "all", "searchable": false, "orderable": false, "visible": true, "targets": 2 }
        ],
    	"order": [
    		[ 1, "asc" ]
    	],
        responsive: true,
        ajax: '{!! URL::route("mgmt-data/jabatan/getindex") !!}'
    });
@stop