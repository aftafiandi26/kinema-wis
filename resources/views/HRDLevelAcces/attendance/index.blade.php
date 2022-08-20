@extends('layout')

@section('title')
    (hr) Attendance
@stop

@section('top')
    @include('assets_css_1')
    @include('assets_css_2')

@stop

@section('navbar')
    @include('navbar_top')
    @include('navbar_left', [
        'c30003' => 'active'
    ])
@stop
@section('body')
<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>

<link href="{{ asset('assets/assets/plugins/select2/css/select2.css') }}" rel="stylesheet" />
<script src="{{ asset('assets/assets/plugins/select2/js/select2.full.js') }}"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/datetime/1.0.2/css/dataTables.dateTime.min.css">

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Summary Attendance</h1> 
    </div>
</div>
<div class="row" style="margin-bottom: 10px;">
  <div>
        <form id="joinForm" class="form-group">
         <div class="col-lg-12" class="form-group">
            <label>Searched : Date</label>
         </div>
        <div class="col-lg-2" class="form-group">
            <input type="date" id="min" name="min" class="form-control">          
        </div>        
        <div class="col-lg-2" class="form-group">
            <input type="date" id="max" name="max" class="form-control">     
        </div>       
         <div class="col-lg-2" class="form-group">
           <button type="reset" class="btn btn-sm btn-warning" id="reseting">Reset</button>
        </div>
        </form>
  </div>
</div>
<hr>
<div class="row">
  <div class="col-lg-12">
    <form class="form-inline" method="get" action="{{ route('getListtAttendance') }}">
      {{ csrf_field() }}
          <div class="form-group">
            <label for="name">Name:</label>           
            <select class="form-control select2" id="name" required="true" name="name">
               <option value="">- select employee -</option>
             <?php foreach ($users as $user): ?>
               <option value="{{ $user->id }}">{{ $user->first_name.' '.$user->last_name }}</option>
             <?php endforeach ?>
            </select>
          </div>
          <div class="form-group">
            <label for="start_date">Date:</label>           
            <input type="date" class="form-control" name="start_date" id="start_date" required="true"></input>
            <input type="date" class="form-control" name="end_date" id="start_date" required="true"></input>
          </div>                
          <div class="form-group">
            <button class="btn btn-sm btn-info  fa fa-search"></button>
          </div>
    </form>
  </div>
</div>
<!-- <br>
<div class="row">
  <div class="col-lg-12">
    <form class="form-inline" method="get" action="#">
      {{ csrf_field() }}
          
          <div class="form-group">
            <label for="startRange">Date Range:</label>           
            <input type="date" class="form-control" name="startRange" id="startRange" required="true"></input>
            <input type="date" class="form-control" name="endRange" id="startRange" required="true"></input>
          </div>                
          <div class="form-group">
            <button class="btn btn-sm btn-info  fa fa-search"></button>
          </div>
    </form>
  </div>
</div> -->
<br>
<div class="row">
  <div class="col-lg-6">
    <form class="form-inline" method="get" action="{{ route('getDataAttendanceDepartment') }}">
      {{ csrf_field() }}
      <div class="form-group">
        <label for="dept">Department:</label>
        <select class="form-control select2" id="dept" name="dept" required="true">
          <option value="">- select department -</option>       
          <?php foreach ($dept as $department): ?>
            <option value="{{ $department->id }}">{{ $department->dept_category_name }}</option>
          <?php endforeach ?>
        </select>
      </div>
      <div class="form-group">
        <label for="startDep">Date:</label>
        <input type="date" class="form-control" name="start_date" id="startDep" required="true"></input>
      </div>
      <div class="form-group">
            <button class="btn btn-sm btn-info  fa fa-search"></button>
      </div>
    </form>
  </div>
  <div class="col-lg-6 pull-right">
    <div class="pull-right">
      <a class="btn btn-sm btn-primary" href="{{ route('createAttendanceHR') }}">Create</a>
    </div>
  </div>
</div>
<hr>
<div class="row">
  <div class="col-lg-12">
    <table class="table table-striped table-bordered table-hover" width="100%" id="tables">
      <thead>
        <tr>
          <th>No</th>         
          <th>NIK</th>
          <th style="width: 150px;">Name</th>
          <th>Department</th>
          <th>Check In</th>        
          <th>Check Out</th>
          <th>Date</th>
          <th>Total Time</th>
          <th style="width: 200px;">Remarks</th>
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

<script type="text/javascript">
  $('button#reseting').on('click', function(e){
      location.reload();
  });
</script>

@stop

@section('bottom')
    @include('assets_script_1')
    @include('assets_script_2')
    @include('assets_script_7')
@stop
@section('script')
  $.fn.dataTableExt.afnFiltering.push(
    function( oSettings, aData, iDataIndex ) {
        var iFini = document.getElementById('min').value;
        var iFfin = document.getElementById('max').value;
        var iStartDateCol = 6;
        var iEndDateCol = 6;
 
        iFini=iFini.substring(0,4) + iFini.substring(4,5)+ iFini.substring(5,10);
        iFfin=iFfin.substring(0,4) + iFfin.substring(4,5)+ iFfin.substring(5,10);
 
        var datofini=aData[iStartDateCol].substring(0,4) + aData[iStartDateCol].substring(4,5)+ aData[iStartDateCol].substring(5,10);
        var datoffin=aData[iEndDateCol].substring(0,4) + aData[iEndDateCol].substring(4,5)+ aData[iEndDateCol].substring(5,10);
 
        <!-- console.log(iFini); -->

        if ( iFini === "" && iFfin === "" )
        {
            return true;
        }
        else if ( iFini <= datofini && iFfin === "")
        {
            return true;
        }
        else if ( iFfin >= datoffin && iFini === "")
        {
            return true;
        }
        else if (iFini <= datofini && iFfin >= datoffin)
        {
            return true;
        }
        return false;
    }
  );

  $(document).ready(function() {
    var table = $('#tables').DataTable();
     
 
    $('#min, #max').keyup( function() {
        table.draw();
    } );
} );

$('[data-toggle="tooltip"]').tooltip();

    $('#tables').DataTable({
       
        processing: true,
        responsive: true,     
         "dom": 'Blfrtip', 
          "buttons": [{
                extend:    'excel',
                text:      '<i class="fa fa-download" style="font-size: 20px;"></i>',
                titleAttr: 'Attendance',
                title: 'Attendance'
            }],
        ajax: '{!! URL::route("indexDataAttendance") !!}',
        columns: [
                  { data: 'DT_Row_Index', orderable: false, searchable : false},
                  { data: 'nik'},
                  { data: 'fullname'},
                  { data: 'dept_category_id'},
                  { data: 'timeIN'},
                  { data: 'timeOUT'},
                  { data: 'dateAttendance'},
                  { data: 'time'},
                  { data: 'remarks'},
                  { data: 'action'}
                ]

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