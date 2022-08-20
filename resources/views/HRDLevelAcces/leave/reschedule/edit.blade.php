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
        <h3 class="text-center text-bold">Leave Form</h3>
    </div>
</div>

<form class="form-horizontal" method="post" action="{{ route('leave/reschedule/update', $leave->id) }}">
    {{ csrf_field() }}
    <div class="row">
       <div class="col-lg-12">
           <div class="col-lg-2">
               <label for="nik">NIK:</label>
               <input type="text" id="nik" class="form-control" readonly value="{{ $user->nik }}">
           </div>
           <div class="col-lg-2">
               <label for="employee">Employee:</label>
               <input type="text" id="employee" class="form-control" readonly value="{{ $user->first_name.' '.$user->last_name }}">
           </div>
           <div class="col-lg-2">
               <label for="department">Department:</label>
               <input type="text" id="department" class="form-control" readonly value="{{ $user->dept_category_name }}" >
           </div>
           <div class="col-lg-2">
               <label for="position">Position:</label>
               <input type="text" id="position" class="form-control" readonly value="{{ $user->position }}">
           </div>
           <div class="col-lg-2">
               <label for="joinDate">Join Date:</label>
               <input type="text" id="joinDate" class="form-control" readonly value="{{ $user->join_date }}">
           </div>
           <div class="col-lg-2">
               <label for="endDate">End Date:</label>
               <input type="text" id="endDate" class="form-control" readonly value="{{ $user->end_date }}">
           </div>
       </div>
    </div>
    <div class="row" style="margin-top: 10px;">
        <div class="col-lg-12">
            <div class="col-lg-2">
                <label for="leaveCategory">Leave Category</label>
                <select class="form-control" name="leaveCategory" id="leaveCategory" required>
                    <?php foreach ($leaveCategory as $key => $value): ?>
                        <option value="{{ $value->id }}" <?php if ($value->id === $user->leave_category_id): ?>
                        selected
                        <?php endif ?>>{{ $value->leave_category_name }}</option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-lg-2">
                <label for="address">Address</label>
                <input type="text" id="leaveCategory" class="form-control" value="{{ $user->address }}" readonly>
            </div>      
            <div class="col-lg-2">
                <label for="available">Available</label>
                <input type="text" name="available" id="available" class="form-control" value="{{ $annualAvailable }}" readonly>
            </div> 
            <div class="col-lg-2">
                <label for="exdo">Exdo</label>
                <input type="text" name="annual" id="annual" class="form-control" value="{{ $exdoAvailable }}" readonly> 
            </div>   
            <div class="col-lg-2">
                <label for="exdo">Total Annual</label>
                <input type="text" name="annual" id="annual" class="form-control" value="{{ $totalAnnual }}" readonly> 
            </div>  
        </div>
    </div>
    <div class="row" style="margin-top: 10px;">
        <div class="col-lg-12">
             <div class="col-lg-2">
                <label for="startLeave">Start Leave<span style="color: red;">*</span></label>
                <input type="date" id="startLeave" name="startLeave" class="form-control" value="{{ $user->leave_date }}" required autofocus>
            </div>
            <div class="col-lg-2">
                <label for="endLeave">End Leave<span style="color: red;">*</span></label>
                <input type="date" id="endLeave" name="endLeave" class="form-control" value="{{ $user->end_leave_date }}" required>
            </div>
            <div class="col-lg-2">
                <label for="backWork">Back To Work<span style="color: red;">*</span></label>
                <input type="date" id="backWork" name="backWork" class="form-control" value="{{ $user->back_work }}" required>
            </div>
        </div>
    </div>
    <div class="row" style="margin-top: 10px;">
        <div class="col-lg-12">
            <div class="col-lg-2">
                <label for="requestDay">Request Day</label>
                <input type="text" id="requestDay" name="requestDay" class="form-control" value="{{ $user->total_day }}">
            </div>          
        </div>
    </div>
    <div class="row" style="margin-top: 10px;">
        <div class="col-lg-12">
            <div class="col-lg-2">
                <button type="submit" class="btn btn-sm btn-success">update</button>
                <a href="{{ route('leave/reschedule/index') }}" class="btn btn-sm btn-default">back</a>
            </div>
        </div>
    </div>
</form>

@stop

@section('bottom')
    @include('assets_script_1')
    @include('assets_script_2')
    @include('assets_script_7')
@stop

