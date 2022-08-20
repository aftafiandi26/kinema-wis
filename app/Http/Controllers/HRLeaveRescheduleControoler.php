<?php

namespace App\Http\Controllers;

use App\Entitled_leave_view;

use App;
use App\Dept_Category;
use App\Http\Controllers\Controller;
use App\Initial_Leave;
use App\Leave;
use App\Meeting;
use App\Leave_backup;
use App\Leave_Category;
use App\NewUser;
use App\Project_Category;
use App\Forfeited;
use App\ForfeitedCounts;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class HRLeaveRescheduleControoler extends Controller
{
   public function __construct()
    {
        $this->middleware(['auth', 'active', 'hr']);
    }

    public function index()
    {
       return view('HRDLevelAcces.leave.reschedule.index');
    }

    public function dataIndex()
    {
        $date1 = date('m' ,strtotime('-1 month')) ;
        $date2 = date('m' ,strtotime('+1 month')) ;

        $modal = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_transaction.leave_date', 'leave_transaction.end_leave_date', 'leave_transaction.back_work',
            'users.nik', 'users.first_name', 'users.last_name', 'users.position', 
            'dept_category.dept_category_name',
            'leave_category.leave_category_name'
        ])
        ->where('users.active', 1)
        ->whereYear('leave_transaction.leave_date', date('Y'))
        ->whereMonth('leave_transaction.leave_date', '>=', $date1)
        ->whereMonth('leave_transaction.leave_date', '<=', $date2)
        ->get();

        return Datatables::of($modal)
                ->addIndexColumn()
                ->addColumn('fullname', '{{ $first_name }} {{ $last_name }}')
                ->addColumn('actions', function(Leave $leave){
                    $id = $leave->id;

                    $edit = '<a class="btn btn-xs btn-warning" href="'.route('leave/reschedule/edit', $id).'"><i class="fa fa-pencil"></i></a>';

                    return $edit;
                })
                ->addColumn('status', function(Leave $leave){
                    $stat = Leave::find($leave->id);
                    $status = '--';

                    if ($stat->ap_koor === 0) {
                        $status = 'Pending Coordinator';
                    }else{
                        if ($stat->ap_spv === 0) {
                           $status = 'Pending SPV';
                        }else{
                            if ($stat->ap_pm === 0) {
                                $status = 'Pending PM';
                            }else{
                                if ($stat->ap_producer === 0) {
                                    $status = 'Pending Producer';
                                }else{
                                    if ($stat->ap_hd === 0) {
                                        $status = 'Pending Head Of Department';
                                    }else{
                                        if ($stat->ver_hr === 0) {
                                            $status = 'Pending HR';
                                        }else{
                                            if ($stat->ap_hrd === 0) {
                                                $status = 'Pending HR Manager';
                                            }else{
                                                $status = 'Complete';
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    return $status;
                })
                ->make(true);
    }

    public function editReschedule($id)
    {
       $leave = Leave::find($id);
       $user = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->find($id);

       $leaveCategory = Leave_Category::all();

       $annualAvailable = $this->dataAnnual($id);

       $totalAnnual = $this->dataTotalAnnual($id);

       $exdoAvailable = $this->dataExdo($id);  
     
        return view('HRDLevelAcces.leave.reschedule.edit', compact(['leave', 'user', 'annualAvailable', 'exdoAvailable', 'leaveCategory', 'totalAnnual']));
    }

    public function dataAnnual($id)
    {
      $user = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->find($id);

       $annual = DB::table('users')
        ->leftJoin('initial_leave', 'initial_leave.user_id', '=', 'users.id')
        ->leftJoin('leave_category', 'leave_category.id', '=', 'initial_leave.leave_category_id')
        ->leftJoin('leave_transaction', 'leave_transaction.user_id', '=', 'users.id')->select([
            DB::raw('
            (
                select (
                    select COALESCE(sum(total_day), 0) from leave_transaction where user_id='.$user->user_id.' and leave_category_id=1
                    and ap_hd  = 1
                    and ap_gm  = 1                    
                    and ver_hr = 1
                    and ap_hrd = 1
                )
            ) as transactionAnnual')
        ])
        ->first();
      

        $startDate = date_create($user->join_date);
        $endDate = date_create($user->end_date);

        $startYear = date('Y', strtotime($user->join_date));
        $endYear = date('Y', strtotime($user->end_date));

        if ($user->emp_status === "Permanent") {
           $yearEnd = date('Y');
        } else {
            $yearEnd = $endYear;
        }

        $now = date_create(date('Y-m-d'));
        $now1 = date_create(date('Y').'-01-01');
        $now2 = date_create(date('Y').'-12-31');
            
       // date_create('2021-05-15') penambahan bulan terjadi
        // dd($now);
       
        if ($now <= $endDate) {
            $sekarang = $now;
        } else {
            $sekarang = $endDate;
        }
            
        $interval = date_diff(date_create($user->join_date),  date_create(date('Y-m-d')));

        $pass = $interval->y * 12;

        $passs = $pass + $interval->m; 

        $daffPermanent = date_diff($now1, $now)->format('%m')+(12*date_diff($now1, $now->modify('+5 day'))->format('%y'));

        $daffPermanent2 = date_diff($now1, $now2)->format('%m')+(12*date_diff($now1, $now2->modify('+5 day'))->format('%y'));

        $daffPermanent1 = 12 - $daffPermanent; 

        if ($passs <= $annual->transactionAnnual) {
            $newAnnual =  $annual->transactionAnnual;
        }else{
            $newAnnual = $passs;
        }       

        $totalAnnual = $newAnnual - $annual->transactionAnnual;

        $totalAnnualPermanent = $user->initial_annual - $annual->transactionAnnual;

        $totalAnnualPermanent1 = $totalAnnualPermanent - $daffPermanent1;

        $return = null;

        if ($user->emp_status === "Permanent") {
             $return = $totalAnnualPermanent1;
        } else {
             $return = $totalAnnual;
        }
        

        return $return;
       
    }

    public function dataExdo($id)
    {
        $user = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->find($id);

        $exdo = Initial_Leave::where('user_id', $user->user_id)->pluck('initial');      

        $w = Initial_Leave::where('user_id', $user->user_id)
                ->whereDATE('expired', '<', date('Y-m-d'))
                ->pluck('initial');

        $expiredExdo = $w;

        $minusExdo = Leave::where('user_id', $user->user_id)->where('leave_category_id', 2)->where('ap_hd', 1)->where('ap_gm', 1)->where('ver_hr', 1)->where('ap_hrd', 1)->pluck('total_day');        

        $goingExdo = 0;

        if ($expiredExdo->sum() >= $minusExdo->sum()) {
            $goingExdo = $expiredExdo->sum() - $minusExdo->sum();
        } 

        $sisaExdo = $exdo->sum() - $minusExdo->sum() - $goingExdo;
       

        return $sisaExdo;

      
    }

    public function dataTotalAnnual($id)
    {
        $user = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->find($id);

       $annual = DB::table('users')
        ->leftJoin('initial_leave', 'initial_leave.user_id', '=', 'users.id')
        ->leftJoin('leave_category', 'leave_category.id', '=', 'initial_leave.leave_category_id')
        ->leftJoin('leave_transaction', 'leave_transaction.user_id', '=', 'users.id')->select([
            DB::raw('
            (
                select (
                    select COALESCE(sum(total_day), 0) from leave_transaction where user_id='.$user->user_id.' and leave_category_id=1
                    and ap_hd  = 1
                    and ap_gm  = 1                    
                    and ver_hr = 1
                    and ap_hrd = 1
                )
            ) as transactionAnnual')
        ])
        ->first();
      

        $startDate = date_create($user->join_date);
        $endDate = date_create($user->end_date);

        $startYear = date('Y', strtotime($user->join_date));
        $endYear = date('Y', strtotime($user->end_date));

        if ($user->emp_status === "Permanent") {
           $yearEnd = date('Y');
        } else {
            $yearEnd = $endYear;
        }

        $now = date_create(date('Y-m-d'));
        $now1 = date_create(date('Y').'-01-01');
        $now2 = date_create(date('Y').'-12-31');
            
       // date_create('2021-05-15') penambahan bulan terjadi
        // dd($now);
       
        if ($now <= $endDate) {
            $sekarang = $now;
        } else {
            $sekarang = $endDate;
        }
            
        $interval = date_diff(date_create($user->join_date),  date_create(date('Y-m-d')));

        $pass = $interval->y * 12;

        $passs = $pass + $interval->m; 

        $daffPermanent = date_diff($now1, $now)->format('%m')+(12*date_diff($now1, $now->modify('+5 day'))->format('%y'));

        $daffPermanent2 = date_diff($now1, $now2)->format('%m')+(12*date_diff($now1, $now2->modify('+5 day'))->format('%y'));

        $daffPermanent1 = 12 - $daffPermanent; 

        if ($passs <= $annual->transactionAnnual) {
            $newAnnual =  $annual->transactionAnnual;
        }else{
            $newAnnual = $passs;
        }       

        $totalAnnual = $newAnnual - $annual->transactionAnnual;

        $totalAnnualPermanent = $user->initial_annual - $annual->transactionAnnual;

        $totalAnnualPermanent1 = $totalAnnualPermanent - $daffPermanent1;   

        $forfeited = Forfeited::where('user_id', auth::user()->id)->pluck('countAnnual');
        $forfeitedCounts = ForfeitedCounts::where('user_id', auth::user()->id)->where('status', 1)->pluck('amount');    
        $countAmount = $forfeited->sum() - $forfeitedCounts->sum();     

        
        $bla = 0;
        if ($countAmount >= 0) {
              $bla = $countAmount;
           } else {
               $bla = 0;  
           } 

        $renewPermanet = $totalAnnualPermanent1 - $bla; 
        $renewContract = $totalAnnual - $bla; 
        

        return $totalAnnualPermanent;

    }

    public function updateRescheduleAnnual(Request $request, $id)
    {
        $leave = Leave::joinUsers()->find($id);


        $startLeave = $request->input('startLeave');
        $endLeave   = $request->input('endLeave');
        $backWork   = $request->input('backWork');
        $requestDay = $request->input('requestDay');

       $interval = date_diff(date_create('2021-01-01'),  date_create('2021-01-31'));
        $pass = $interval->y * 12;
        $passs = $pass + $interval->m + 1;     

        $rules = [
            'leaveCategory' => 'required',
            'startLeave'    => 'required|date',
            'endLeave'      => 'required|date',
            'backWork'      => 'required|date',
            'requestDay'    => 'required|numeric'
        ];

        $data = [
            'leave_category_id' => $request->input('leaveCategory'),
            'leave_date'        => $request->input('startLeave'),
            'end_leave_date'    => $request->input('endLeave'),
            'back_work'         => $request->input('backWork'),
            'total_day'         => $request->input('requestDay'),
        ];

        if ($startLeave > $endLeave) {
           $alert = 'Opss!!, date is not valid, please check again!!';
           return redirect()->back()->with('getError', $alert);
        }
        if ($endLeave > $backWork) {
            $alert = 'Opss!!, date is not valid, please check again!!';
            return redirect()->back()->with('getError', $alert);
        } 

        $validator = Validator::make($request->all(), $rules);

         if ($validator->fails()) {
                return Redirect::route('leave/reschedule/edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            } else {
                Session::flash('message', Lang::get('messages.data_updated', ['data' => 'Data leave transaction '.$leave->first_name.' '.$leave->last_name]));
                Leave::where('id', $id)->update($data);
                return redirect()->route('leave/reschedule/index');
            }
    }

   
}
