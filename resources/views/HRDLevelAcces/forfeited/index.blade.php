@extends('layout')

@section('title')
    (hr) Forfeited Leave
@stop

@section('top')
    @include('assets_css_1')
    @include('assets_css_2')
    @include('assets_css_3')
    @include('assets_css_4')

@stop

@section('navbar')
    @include('navbar_top')
    @include('navbar_left', [
        'c3' => 'active'
    ])
@stop
@section('body')
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Forfeited Leave</h1> 
    </div>
</div>

<div class="row">
    <div class="table-responsive table-sm col-11">        
         
                <table id="employeeData" class="table table-striped table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nik</th>      
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th>AL That Will Be Forfeited (2021)</th>
                            <th>Total AL That Will Be Forfeited</th>
                            <th>Available</th>
                            <th>Available + AL That Will Be Forfeteid</th>
                            <th>Advance Leave</th>
                            <th>Remains Annual</th>  
                            <th>Exdo Will be Forfeited <small>({{ date('M', strtotime('+1 month')) }})</small></th>
                            <th>Exdo</th>
                            <th>Total AL + EXDO</th>
                            <th>Action</th>
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
    @include('assets_script_3')
    @include('assets_script_7')
@stop

@section('script')

        $('#employeeData').DataTable({
                processing: true,
                responsive: true,
                ajax: '{{ route('hr/forfeited/data') }}',
                columns: [
                    { data: 'DT_Row_Index', orderable: false, searchable : false},                
                    { data: 'nik'},
                    { data: 'first_name'},   
                    { data: 'dept'},
                    { data: 'dept'},  
                    { data: 'emp_status'},
                    { data: 'year2'},
                    { data: 'forfeited'},
                    { data: 'available'},
                    { data: 'availableForfeited'},
                    { data: 'advancedLeave'},
                    { data: 'Remains'},
                    { data: 'exdo'},  
                    { data: 'advanceExdo'},  
                    { data: 'totalyALEXDO'},                
                    { data: 'actions'}            
                ],
                dom: 'Bfrtip',
                buttons: [
                     'excel'
                ]
                             
        });


     $(document).on('click','#employeeData tr td a[title="Detail"]',function(e) {
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