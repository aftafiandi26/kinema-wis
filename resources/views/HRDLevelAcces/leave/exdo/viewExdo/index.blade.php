@extends('layout')

@section('title')
    (hr) Summary Exdo
@stop

@section('top')
    @include('assets_css_1')
    @include('assets_css_2')
    @include('assets_css_4')
@stop

@section('navbar')
    @include('navbar_top')
    @include('navbar_left', [
        'c173' => 'active'
    ])
@stop
@section('body')
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Summary Exdo <sup>{{ date('Y') }}</sup>
        </h1>
    </div>
</div>
<div class="row">
    <div class="col-lg-3">
      <a href="{{ route('forfeited/index') }}" class="btn btn-sm btn-default" title="back" style="margin-bottom: 10px;">back</a>
      <table class="table table-bordered table-condensed table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>{{ $user->first_name.' '.$user->last_name }}</th>
            </tr>
            <tr>
                <th>NIK</th>
                <th>{{ $user->nik }}</th>
            </tr>
            <tr>
                <th>Department</th>
                <th>{{ $dept }}</th>
            </tr>
            <tr>
                <th>Position</th>
                <th>{{ $user->position }}</th>
            </tr>
            <tr>
                <th>Status</th>
                <th>{{ $user->emp_status }}</th>
            </tr>
        </thead>
      </table>
    </div>
</div>
<div class="row">
    <div class="col-lg-7">
        <h3>
            <b>Exdo Transaction <sup>{{ date('Y') }}</sup></b>
        </h3>
        <br>
         <form action="{{ route('hr/exdo/view/generate/excelExdoLeaveTransaction') }}" method="post" class="form-inline" style="margin-top: -15px;"> 
            {{ csrf_field() }}       
            <input type="hidden" name="id" value="{{ $user->id }}">
            <div class="form-group">
                <label for="month">Month:</label>
                <select name="month" id="month" required class="form-control">
                    <option value="0">-All-</option>
                    <option value="1">Januari</option>
                    <option value="2">February</option>
                    <option value="3">March</option>
                    <option value="4">April</option>
                    <option value="5">Mei</option>
                    <option value="6">June</option>
                    <option value="7">July</option>
                    <option value="8">August</option>
                    <option value="9">September</option>
                    <option value="10">October</option>
                    <option value="11">Novemver</option>
                    <option value="12">December</option>
                </select>
                <label for="period">Year</label>
                <input type="text" name="period" id="period" value="{{ date('Y') }}" required class="form-control">
                <button type="submit" class="btn btn-sm btn-info">Excel</button>
            </div>
         </form>
        <br>
        <table class="table-bordered table-condensed table-striped table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Leave Category</th>
                    <th>NIK</th>
                    <th>Employee</th>
                    <th>Leave Date</th>
                    <th>End Leave Date</th>
                    <th>Total Day</th>
                    <th>Status Form</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($exdoTransaction as $ex => $t)
                    <tr>
                        <td>{{ $exdoTransaction->firstItem() + $ex }}</td>
                        <td>Exdo</td>
                        <td>{{ $t->request_nik }}</td>
                        <td>{{ $t->request_by }}</td>
                        <td>{{ $t->leave_date }}</td>
                        <td>{{ $t->end_leave_date }}</td>
                        <td>{{ $t->total_day }}</td>
                        <td>
                          @if ($user->hd == 0)
                            @if ($t->ap_koor == 0)
                                Pending Coordinator
                            @else
                                @if ($t->ap_spv == 0)
                                    Pending SPV
                                @else
                                    @if ($t->ap_pm == 0)
                                        Pending PM
                                    @else
                                        @if ($t->ap_hd == 0)
                                            Pending Head of Department
                                        @else
                                            @if ($t->ver_hr == 0)
                                                Pending HR Verification
                                            @else
                                                @if ($t->ap_hrd == 0)
                                                    Pending HR Manager
                                                @else
                                                    Completed
                                                @endif
                                            @endif
                                        @endif
                                    @endif
                                @endif
                            @endif
                          @else
                            @if ($t->ap_gm == 0)
                                Pending GM
                            @else
                                @if ($t->ver_hr == 0)
                                    Pending HR Verification
                                @else
                                    @if ($t->ap_hrd == 0)
                                        Pending HR Manager
                                    @else
                                        Completed
                                    @endif
                                @endif
                            @endif
                              
                          @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        Showing  {{ $exdoTransaction->firstItem() }} to {{ $exdoTransaction->lastItem() }} from  {{ $exdoTransaction->total() }} data
       
        <br>
        {{ $exdoTransaction->links() }}
    </div>
    <div class="col-lg-5">
        <h3>
            <b>Index Exdo <sup>{{ date('Y') }}</sup></b>
            <form action="{{ route('hr/exdo/view/generate/indexExcel') }}" method="post" class="pull-right">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{ $user->id }}">
                <button type="submit" class="btn btn-sm btn-default">Excel</button>
            </form>
        </h3>
        <br>
        <table class="table table-condensed table-border">
            <thead>
                <tr>
                    <th>Exdo Will Be Forfeited <sup>{{ date('F', strtotime('+1 month')) }}</sup></th>
                    <th>Remains</th>
                    <th>Total Exdo</th>                    
                </tr>
            </thead>
            <body>
                <tr>
                    <td>{{ $sisaExdo }}</td>
                    <td>{{ $remains }}</td>
                    <td>{{ $totalExdo }}</td>
                </tr>
            </body>
        </table>
        <br>
        <table class="table table-bordered table-condensed table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Expired</th>
                    <th>Initial</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($indexExdo as $i => $e)
                    <tr>
                        <td>{{ $indexExdo->firstItem() + $i}}</td>
                        <td>{{ $e->expired }}</td>
                        <td>{{ $e->initial }}</td>                      
                        <td>{{ $e->note }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        Showing  {{ $indexExdo->firstItem() }} to {{ $indexExdo->lastItem() }} from  {{ $indexExdo->total() }} data
       
        <br>
        {{ $indexExdo->links() }}
    </div>
</div>
@stop

@section('bottom')
    @include('assets_script_1')
    @include('assets_script_2')
@stop

