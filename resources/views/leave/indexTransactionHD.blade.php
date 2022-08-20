@extends('layout')

@section('title')
    Leave
@stop

@section('top')
    @include('assets_css_1')
    @include('assets_css_2')
@stop

@section('navbar')
    @include('navbar_top')
    @include('navbar_left', [
        'c2' => 'active'
    ])
@stop
@section('body')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Leave Transactions</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
             <table class="table table-striped table-hover" width="100%" id="tablesIT">
                <thead>
                        <td>ID</td>
                        <td>NIK</td>
                        <td>Name</td>
                        <td>Leave Category</td>
                        <td>Leave Date</td>
                        <td>Total Day</td>
                        <td>FA Approval</td>
                        <td>GM Aprroval</td>
                        <td>HR Checking</td>
                        <td>HR Manager<br>Confirmation</td>
                        <td>resendmail</td>
                        <td style="width: 78px;">Action</td>
                        <td>Remainder</td>
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

    $('#tablesIT').DataTable({
        "columnDefs": [
            { className: "never", "searchable": false, "orderable": false, "visible": false, "targets": [0, 10] }
        ],
        "order": [
            [ 0, "des" ]
        ],
         processing: true,
        responsive: true,
        ajax: '{!! URL::route("leave/getIndexLeaveTransactionHD") !!}'
    });

    $(document).on('click','#tablesIT tr td a[title="Detail"]',function(e) {
        var id = $(this).attr('data-role');

        $.ajax({
            url: id,
            success: function(e) {
                $("#modal-content").html(e);
            }
        });
    });

@stop
