@extends('layout')

@section('title')
    Riwayat Penggajian
@stop

@section('top')
    @include('assets_css_1')
    @include('assets_css_2')
@stop

@section('navbar')
    @include('navbar_top')
    @include('navbar_left', [
        'c02' => 'active'
    ])
@stop

@section('body')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Riwayat Penggajian</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
			<table class="table table-striped table-bordered table-hover" width="100%" id="tables">
				<thead>
				    <tr>
                        <td>ID</td>
                        <td>Bulan</td>
                        <td>Tahun</td>
                        <td style="width: 52px;">Action</td>
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
            { className: "all", "searchable": false, "orderable": false, "visible": true, "targets": 3 }
        ],
    	"order": [
    		[ 1, "asc" ]
    	],
        responsive: true,
        ajax: '{!! URL::route("riwayat-penggajian/getindex") !!}'
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