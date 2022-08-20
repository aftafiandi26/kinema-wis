@extends('layout')

@section('title')
    (hr) Index Employee
@stop

@section('top')
    @include('assets_css_1')
    @include('assets_css_2')
    @include('assets_css_4')
@stop

@section('navbar')
    @include('navbar_top')
    @include('navbar_left', [
        'c19' => 'active'
    ])
@stop
@section('body')

  <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Reschedule Leave</h1>           
        </div>
    </div>

     <div class="row">
        <div class="col-lg-12">
            <div>
            <table class="table table-striped table-bordered table-hover" width="100%" id="tablesReschedule">
                <thead>
                    <tr style="white-space:nowrap">
                        <td>No</td>
                        <td>NIK</td>                        
                        <td>Employee</td>                      
                        <td>Department</td>
                        <td>Position</td>        
                        <td>Category</td>              
                        <td>Start leave Date</td> 
                        <td>End leave Date</td>
                        <td>Back Work</td>
                        <td>Status</td>
                        <td>Action</td>
                    </tr>
                </thead>
            </table>
            </div>
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
    @include('assets_script_7')
@stop
@section('script')

    $('#tablesReschedule').DataTable({
                processing: true,
                responsive: true,
                ajax: '{{ route('leave/reschedule/index/data') }}',
                columns: [
                    { data: 'DT_Row_Index', orderable: false, searchable : false},  
                    { data: 'nik'},
                    { data: 'fullname'},
                    { data: 'dept_category_name'},
                    { data: 'position'},
                    { data: 'leave_category_name'},
                    { data: 'leave_date'},
                    { data: 'end_leave_date'},
                    { data: 'back_work'},
                    { data: 'status'},
                    { data: 'actions'}
                ],
                dom: 'Bfrtip',
                buttons: [
                     'excel'
                ]                 
    });


     $(document).on('click','#tablesReschedule tr td a[title="Detail"]',function(e) {
        e.preventDefault();
        var id = $(this).attr('data-role');
       
        $.ajax({
            url: id,            
            success: function(e) {
                $("#modal-content").html(e);
            }
        });
    }); 
@endsection
