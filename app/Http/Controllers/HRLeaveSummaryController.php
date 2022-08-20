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

class HRLeaveSummaryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'active', 'hr']);
    }

    public function dataProvinsi()
    {
        $provinsi = file_get_contents('https://dev.farizdotid.com/api/daerahindonesia/provinsi', 200);
        // $provinsi = file_get_contents('https://ibnux.github.io/data-indonesia/provinsi.json', 200);
        $provinsi = json_decode($provinsi, true);
        $provinsi = $provinsi['provinsi'];

        return $provinsi;
    }

    public function dataNameProvinsi($id)
    {
        $provinsi = file_get_contents('https://dev.farizdotid.com/api/daerahindonesia/provinsi/' . $id, 200);
        // $provinsi = file_get_contents('https://ibnux.github.io/data-indonesia/kabupaten/'.$id.'.json', 200);
        $provinsi = json_decode($provinsi, true);

        return $provinsi['nama'];
    }

    public function dataNameHometown($id)
    {
        $provinsi = file_get_contents('https://dev.farizdotid.com/api/daerahindonesia/kota/' . $id, 200);
        // $provinsi = file_get_contents('https://ibnux.github.io/data-indonesia/kabupaten/'.$id.'.json', 200);
        $provinsi = json_decode($provinsi, true);

        return  $provinsi['nama'];
    }

    public function indexSummaryLeave()
    {
        $leaveCategory = Leave_Category::all();

        $provinsi = $this->dataProvinsi();

        $users = User::where('active', 1)->where('nik', '!=', Null)->where('nik', '!=', '123456789')->orderBy('first_name', 'asc')->get();

        $january = Leave::joinUsers()->joinLeaveCategory()->select(['leave_transaction.id', 'leave_transaction.leave_date', 'users.active', 'leave_category.leave_category_name'])->whereYear('leave_transaction.leave_date', date('Y'))->whereMonth('leave_transaction.leave_date', '1')->where('leave_transaction.ap_hrd', 1)->where('users.active', 1)->get();

        $februari = Leave::joinUsers()->joinLeaveCategory()->select(['leave_transaction.id', 'leave_transaction.leave_date', 'users.active', 'leave_category.leave_category_name'])->whereYear('leave_transaction.leave_date', date('Y'))->whereMonth('leave_transaction.leave_date', '2')->where('leave_transaction.ap_hrd', 1)->where('users.active', 1)->get();

        $march = Leave::joinUsers()->joinLeaveCategory()->select(['leave_transaction.id', 'leave_transaction.leave_date', 'users.active', 'leave_category.leave_category_name'])->whereYear('leave_transaction.leave_date', date('Y'))->whereMonth('leave_transaction.leave_date', '3')->where('leave_transaction.ap_hrd', 1)->where('users.active', 1)->get();

        $april = Leave::joinUsers()->joinLeaveCategory()->select(['leave_transaction.id', 'leave_transaction.leave_date', 'users.active', 'leave_category.leave_category_name'])->whereYear('leave_transaction.leave_date', date('Y'))->whereMonth('leave_transaction.leave_date', '4')->where('leave_transaction.ap_hrd', 1)->where('users.active', 1)->get();

        $mei = Leave::joinUsers()->joinLeaveCategory()->select(['leave_transaction.id', 'leave_transaction.leave_date', 'users.active', 'leave_category.leave_category_name'])->whereYear('leave_transaction.leave_date', date('Y'))->whereMonth('leave_transaction.leave_date', '5')->where('leave_transaction.ap_hrd', 1)->where('users.active', 1)->get();

        $june = Leave::joinUsers()->joinLeaveCategory()->select(['leave_transaction.id', 'leave_transaction.leave_date', 'users.active', 'leave_category.leave_category_name'])->whereYear('leave_transaction.leave_date', date('Y'))->whereMonth('leave_transaction.leave_date', '6')->where('leave_transaction.ap_hrd', 1)->where('users.active', 1)->get();

        $july = Leave::joinUsers()->joinLeaveCategory()->select(['leave_transaction.id', 'leave_transaction.leave_date', 'users.active', 'leave_category.leave_category_name'])->whereYear('leave_transaction.leave_date', date('Y'))->whereMonth('leave_transaction.leave_date', '7')->where('leave_transaction.ap_hrd', 1)->where('users.active', 1)->get();

        $august = Leave::joinUsers()->joinLeaveCategory()->select(['leave_transaction.id', 'leave_transaction.leave_date', 'users.active', 'leave_category.leave_category_name'])->whereYear('leave_transaction.leave_date', date('Y'))->whereMonth('leave_transaction.leave_date', '8')->where('leave_transaction.ap_hrd', 1)->where('users.active', 1)->get();

        $september = Leave::joinUsers()->joinLeaveCategory()->select(['leave_transaction.id', 'leave_transaction.leave_date', 'users.active', 'leave_category.leave_category_name'])->whereYear('leave_transaction.leave_date', date('Y'))->whereMonth('leave_transaction.leave_date', '9')->where('leave_transaction.ap_hrd', 1)->where('users.active', 1)->get();

        $october = Leave::joinUsers()->joinLeaveCategory()->select(['leave_transaction.id', 'leave_transaction.leave_date', 'users.active', 'leave_category.leave_category_name'])->whereYear('leave_transaction.leave_date', date('Y'))->whereMonth('leave_transaction.leave_date', '10')->where('leave_transaction.ap_hrd', 1)->where('users.active', 1)->get();

        $november = Leave::joinUsers()->joinLeaveCategory()->select(['leave_transaction.id', 'leave_transaction.leave_date', 'users.active', 'leave_category.leave_category_name'])->whereYear('leave_transaction.leave_date', date('Y'))->whereMonth('leave_transaction.leave_date', '11')->where('leave_transaction.ap_hrd', 1)->where('users.active', 1)->get();

        $december = Leave::joinUsers()->joinLeaveCategory()->select(['leave_transaction.id', 'leave_transaction.leave_date', 'users.active', 'leave_category.leave_category_name'])->whereYear('leave_transaction.leave_date', date('Y'))->whereMonth('leave_transaction.leave_date', '12')->where('leave_transaction.ap_hrd', 1)->where('users.active', 1)->get();

        return view('HRDLevelAcces.leave.summary.index', compact(['leaveCategory', 'provinsi', 'leave', 'january', 'februari', 'march', 'april', 'mei', 'june', 'july', 'august', 'september', 'october', 'november', 'december', 'users']));
    }

    public function dataSummaryLeave()
    {
        $modal = Leave::joinUsers()->joinDeptCategory()->select([
            'leave_transaction.id',
            'leave_transaction.leave_category_id',
            'users.nik', 'users.last_name', 'users.first_name', 'users.position',
            'dept_category.dept_category_name',
            'leave_transaction.leave_date',
            'leave_transaction.end_leave_date',
            'leave_transaction.back_work',
            'leave_transaction.ap_hrd',
            'leave_transaction.r_after_leaving'
        ])
            // ->whereNotNull('leave_transaction.r_after_leaving')
            ->where('leave_transaction.ap_hrd', 1)
            ->whereYear('leave_transaction.leave_date', date('Y'))
            ->where('users.active', 1)
            ->where('users.id', 226)
            ->orderBy('leave_transaction.id', 'desc')
            ->get();

        return Datatables::of($modal)
            ->addIndexColumn()
            ->editColumn('leave_category_id', function (Leave $leave) {
                $return = Leave_Category::find($leave->leave_category_id);

                return $return->leave_category_name;
            })
            ->editColumn('r_after_leaving', '@if ($r_after_leaving) {{ $r_after_leaving }} @else {{ "--" }} @endif')
            ->editColumn('ap_hrd', '@if($ap_hrd === 1){{"Confirmed"}} @endif')
            ->editColumn('leave_date', '{{ date("d-M-Y", strtotime($leave_date)) }}')
            ->editColumn('end_leave_date', '{{ date("d-M-Y", strtotime($end_leave_date)) }}')
            ->editColumn('back_work', '{{ date("d-M-Y", strtotime($back_work)) }}')
            ->addColumn('fullName', '{{$first_name}} {{$last_name}}')
            ->addColumn('actions', function (Leave $leave) {
                $view = '<a class="btn btn-xs btn-success" title="Detail" data-toggle="modal" data-target="#showModal" data-role="' . route('hrd/summary/leave/index/view', [$leave->id]) . '"><span class="fa fa-eye"></span></a>';
                $delete = '<a class="btn btn-xs btn-danger" title="Delete" data-toggle="modal" data-target="#showModal" data-role="' . route('hrd/summary/leave/destroy', [$leave->id]) . '"><span class="fa fa-trash"></span></a>';
                $edit = "<a class='btn btn-xs btn-warning' title='Change Data' href=" . route('hrd/summary/leave/edit', [$leave->id]) . "><span class='fa fa-pencil'></span>";


                return $view . ' ' . $edit . ' ' . $delete;
            })
            ->rawColumns(['actions'])
            ->make(True);
    }

    public function viewSummaryLeave($id)
    {
        $leave = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_transaction.leave_date', 'leave_transaction.end_leave_date', 'leave_transaction.back_work', 'leave_transaction.leave_category_id',
            'leave_transaction.period', 'leave_transaction.pending', 'leave_transaction.total_day', 'leave_transaction.remain', 'leave_transaction.r_departure',
            'leave_transaction.r_after_leaving',
            'users.first_name', 'users.last_name', 'users.nik', 'users.join_date', 'users.position', 'users.address',
            'dept_category.dept_category_name',
            'leave_category.leave_category_name',
        ])->where('leave_transaction.id', $id)->first();


        $return   = "
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <h4 class='modal-title' id='showModalLabel'>eForm Leave <b>$leave->first_name $leave->last_name</b></h4>
            </div>
            <div class='modal-body'>
                <div class='well'>
                    <strong>Request by :</strong> $leave->first_name $leave->last_name<br>
                    <strong>Period :</strong> $leave->period <br>
                    <strong>Join Date :</strong> " . date('d-M-Y', strtotime($leave->join_date)) . " <br>
                    <strong>NIK :</strong> $leave->nik <br>
                    <strong>Position :</strong> $leave->position <br>
                    <strong>Department :</strong> $leave->dept_category_name <br>
                    <strong>Contact Address :</strong> $leave->address <br>
                    <strong>Leave Category :</strong> $leave->leave_category_name <br>
                    <strong>Start Leave :</strong> " . date('d-M-Y', strtotime($leave->leave_date)) . " <br>
                    <strong>End Leave :</strong> " . date('d-M-Y', strtotime($leave->end_leave_date)) . " <br>
                    <strong>Back to Work :</strong> " . date('d-M-Y', strtotime($leave->back_work)) . " <br>
                    <strong>Total Annual :</strong> $leave->pending <br>
                    <strong>Request Day :</strong> $leave->total_day <br>
                    <strong>Total Balance :</strong> $leave->remain <br>
                    <strong>Hometown :</strong> $leave->r_departure -> $leave->r_after_leaving <br>
                    <strong>Status : Confirmed <br>
                </div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
            </div>
        ";

        return $return;
    }

    public function detailLeaveSummary(Request $request)
    {
        $category = $request->input('category');
        $dateFrom = $request->input('fromDate');
        $dateToo  = $request->input('toDate');
        $hometown = $request->input('hometown');
        $townn    = $request->input('town');

        if ($townn === null) {
            $townn = 'all';
        }

        if ($category === "all") {
            return redirect()->route('hrd/summary/leave/index/view/detail/all', compact(['dateFrom', 'dateToo', 'category', 'hometown', 'townn']));
        } else {
            return redirect()->route('hrd/summary/leave/index/view/detail/category', compact(['dateFrom', 'dateToo', 'category', 'hometown', 'townn']));
        }
    }

    public function detailAllSummaryLeaeve($dateFrom, $dateToo, $category, $hometown, $townn)
    {

        if ($hometown !== 'all') {
            $namaProvinsi = $this->dataNameProvinsi($hometown);
        }

        if ($townn !== 'all') {
            $namaHometown = $this->dataNameHometown($townn);
        }

        if ($hometown === 'all') {
            $leave = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                'leave_transaction.id', 'leave_transaction.leave_date', 'leave_transaction.end_leave_date', 'leave_transaction.back_work', 'leave_transaction.leave_category_id',
                'leave_transaction.period', 'leave_transaction.pending', 'leave_transaction.total_day', 'leave_transaction.remain', 'leave_transaction.r_departure',
                'leave_transaction.ap_hrd',
                'leave_transaction.r_after_leaving',
                'users.first_name', 'users.last_name', 'users.nik', 'users.join_date', 'users.position', 'users.address',
                'dept_category.dept_category_name',
                'leave_category.leave_category_name',
            ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                ->where('leave_transaction.leave_date', '<=', $dateToo)
                ->where('leave_transaction.ap_hrd', 1)
                ->where('users.active', 1)
                ->paginate(10);
        } else {
            if ($townn === 'all') {
                $leave = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id', 'leave_transaction.leave_date', 'leave_transaction.end_leave_date', 'leave_transaction.back_work', 'leave_transaction.leave_category_id',
                    'leave_transaction.period', 'leave_transaction.pending', 'leave_transaction.total_day', 'leave_transaction.remain', 'leave_transaction.r_departure',
                    'leave_transaction.ap_hrd',
                    'leave_transaction.r_after_leaving',
                    'users.first_name', 'users.last_name', 'users.nik', 'users.join_date', 'users.position', 'users.address',
                    'dept_category.dept_category_name',
                    'leave_category.leave_category_name',
                ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                    ->where('leave_transaction.leave_date', '<=', $dateToo)
                    ->where('leave_transaction.r_departure', $namaProvinsi)
                    ->where('leave_transaction.ap_hrd', 1)
                    ->where('users.active', 1)
                    ->paginate(10);
            } else {
                $leave = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id', 'leave_transaction.leave_date', 'leave_transaction.end_leave_date', 'leave_transaction.back_work', 'leave_transaction.leave_category_id',
                    'leave_transaction.period', 'leave_transaction.pending', 'leave_transaction.total_day', 'leave_transaction.remain', 'leave_transaction.r_departure',
                    'leave_transaction.ap_hrd',
                    'leave_transaction.r_after_leaving',
                    'users.first_name', 'users.last_name', 'users.nik', 'users.join_date', 'users.position', 'users.address',
                    'dept_category.dept_category_name',
                    'leave_category.leave_category_name',
                ])
                    ->where('leave_transaction.leave_date', '>=', $dateFrom)
                    ->where('leave_transaction.leave_date', '<=', $dateToo)
                    ->where('leave_transaction.r_after_leaving', $namaHometown)
                    ->where('leave_transaction.ap_hrd', 1)
                    ->where('users.active', 1)
                    ->paginate(10);
            }
        }

        if ($hometown === 'all') {
            $etc = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.leave_date',
            ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                ->where('leave_transaction.leave_date', '<=', $dateToo)
                ->where('leave_transaction.ap_hrd', 1)
                ->whereNotIn('leave_category.leave_category_name', ['Annual', 'Exdo'])
                ->where('users.active', 1)
                ->get();
        } else {
            if ($townn === 'all') {
                $etc = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.leave_date',
                ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                    ->where('leave_transaction.leave_date', '<=', $dateToo)
                    ->where('leave_transaction.ap_hrd', 1)
                    ->whereNotIn('leave_category.leave_category_name', ['Annual', 'Exdo'])
                    ->where('leave_transaction.r_departure', $namaProvinsi)
                    ->where('users.active', 1)
                    ->get();
            } else {
                $etc = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.leave_date',
                ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                    ->where('leave_transaction.leave_date', '<=', $dateToo)
                    ->where('leave_transaction.ap_hrd', 1)
                    ->whereNotIn('leave_category.leave_category_name', ['Annual', 'Exdo'])
                    ->where('leave_transaction.r_after_leaving', $namaHometown)
                    ->where('users.active', 1)
                    ->get();
            }
        }


        if ($hometown === 'all') {
            $annual = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.leave_date',
            ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                ->where('leave_transaction.leave_date', '<=', $dateToo)
                ->where('leave_transaction.ap_hrd', 1)
                ->where('leave_category.leave_category_name', 'Annual')
                ->where('users.active', 1)
                ->get();
        } else {
            if ($townn === 'all') {
                $annual = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.leave_date',
                ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                    ->where('leave_transaction.leave_date', '<=', $dateToo)
                    ->where('leave_transaction.ap_hrd', 1)
                    ->where('leave_category.leave_category_name', 'Annual')
                    ->where('leave_transaction.r_departure', $namaProvinsi)
                    ->where('users.active', 1)
                    ->get();
            } else {
                $annual = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.leave_date',
                ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                    ->where('leave_transaction.leave_date', '<=', $dateToo)
                    ->where('leave_transaction.ap_hrd', 1)
                    ->where('leave_category.leave_category_name', 'Annual')
                    ->where('leave_transaction.r_after_leaving', $namaHometown)
                    ->where('users.active', 1)
                    ->get();
            }
        }


        if ($hometown === 'all') {
            $exdo = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.leave_date',
            ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                ->where('leave_transaction.leave_date', '<=', $dateToo)
                ->where('leave_transaction.ap_hrd', 1)
                ->where('leave_category.leave_category_name', 'Exdo')
                ->where('users.active', 1)
                ->get();
        } else {
            if ($townn === 'all') {
                $exdo = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.leave_date',
                ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                    ->where('leave_transaction.leave_date', '<=', $dateToo)
                    ->where('leave_transaction.ap_hrd', 1)
                    ->where('leave_category.leave_category_name', 'Exdo')
                    ->where('leave_transaction.r_departure', $namaProvinsi)
                    ->where('users.active', 1)
                    ->get();
            } else {
                $exdo = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.leave_date',
                ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                    ->where('leave_transaction.leave_date', '<=', $dateToo)
                    ->where('leave_transaction.ap_hrd', 1)
                    ->where('leave_category.leave_category_name', 'Exdo')
                    ->where('leave_transaction.r_after_leaving', $namaHometown)
                    ->where('users.active', 1)
                    ->get();
            }
        }


        $total = $annual->count() + $exdo->count() + $etc->count();

        if ($hometown === 'all') {
            $sick = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                'leave_transaction.id', 'leave_category.leave_category_name',
            ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                ->where('leave_transaction.leave_date', '<=', $dateToo)
                ->where('leave_transaction.ap_hrd', 1)
                ->where('leave_category.id', 3)
                ->where('users.active', 1)
                ->get();
        } else {
            if ($townn === 'all') {
                $sick = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id', 'leave_category.leave_category_name',
                ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                    ->where('leave_transaction.leave_date', '<=', $dateToo)
                    ->where('leave_transaction.ap_hrd', 1)
                    ->where('leave_category.id', 3)
                    ->where('leave_transaction.r_departure', $namaProvinsi)
                    ->where('users.active', 1)
                    ->get();
            } else {
                $sick = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id', 'leave_category.leave_category_name',
                ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                    ->where('leave_transaction.leave_date', '<=', $dateToo)
                    ->where('leave_transaction.ap_hrd', 1)
                    ->where('leave_category.id', 3)
                    ->where('leave_transaction.r_after_leaving', $namaHometown)
                    ->where('users.active', 1)
                    ->get();
            }
        }


        if ($hometown === 'all') {
            $wedding = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                'leave_transaction.id', 'leave_category.leave_category_name',
            ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                ->where('leave_transaction.leave_date', '<=', $dateToo)
                ->where('leave_transaction.ap_hrd', 1)
                ->where('leave_category.id', 4)
                ->where('users.active', 1)
                ->get();
        } else {
            if ($townn === 'all') {
                $wedding = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id', 'leave_category.leave_category_name',
                ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                    ->where('leave_transaction.leave_date', '<=', $dateToo)
                    ->where('leave_transaction.ap_hrd', 1)
                    ->where('leave_category.id', 4)
                    ->where('leave_transaction.r_departure', $namaProvinsi)
                    ->where('users.active', 1)
                    ->get();
            } else {
                $wedding = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id', 'leave_category.leave_category_name',
                ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                    ->where('leave_transaction.leave_date', '<=', $dateToo)
                    ->where('leave_transaction.ap_hrd', 1)
                    ->where('leave_category.id', 4)
                    ->where('leave_transaction.r_after_leaving', $namaHometown)
                    ->where('users.active', 1)
                    ->get();
            }
        }


        if ($hometown === 'all') {
            $bod = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                'leave_transaction.id', 'leave_category.leave_category_name',
            ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                ->where('leave_transaction.leave_date', '<=', $dateToo)
                ->where('leave_transaction.ap_hrd', 1)
                ->where('leave_category.id', 5)
                ->where('users.active', 1)
                ->get();
        } else {
            if ($townn === 'all') {
                $bod = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id', 'leave_category.leave_category_name',
                ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                    ->where('leave_transaction.leave_date', '<=', $dateToo)
                    ->where('leave_transaction.ap_hrd', 1)
                    ->where('leave_category.id', 5)
                    ->where('leave_transaction.r_departure', $namaProvinsi)
                    ->where('users.active', 1)
                    ->get();
            } else {
                $bod = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id', 'leave_category.leave_category_name',
                ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                    ->where('leave_transaction.leave_date', '<=', $dateToo)
                    ->where('leave_transaction.ap_hrd', 1)
                    ->where('leave_category.id', 5)
                    ->where('leave_transaction.r_after_leaving', $namaHometown)
                    ->where('users.active', 1)
                    ->get();
            }
        }


        if ($hometown === 'all') {
            $unpaid = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                'leave_transaction.id', 'leave_category.leave_category_name',
            ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                ->where('leave_transaction.leave_date', '<=', $dateToo)
                ->where('leave_transaction.ap_hrd', 1)
                ->where('leave_category.id', 6) //
                ->where('users.active', 1)
                ->get();
        } else {
            if ($townn === 'all') {
                $unpaid = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id', 'leave_category.leave_category_name',
                ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                    ->where('leave_transaction.leave_date', '<=', $dateToo)
                    ->where('leave_transaction.ap_hrd', 1)
                    ->where('leave_category.id', 6)
                    ->where('leave_transaction.r_departure', $namaProvinsi)
                    ->where('users.active', 1)
                    ->get();
            } else {
                $unpaid = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id', 'leave_category.leave_category_name',
                ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                    ->where('leave_transaction.leave_date', '<=', $dateToo)
                    ->where('leave_transaction.ap_hrd', 1)
                    ->where('leave_category.id', 6)
                    ->where('leave_transaction.r_after_leaving', $namaHometown)
                    ->where('users.active', 1)
                    ->get();
            }
        }


        if ($hometown === 'all') {
            $son = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                'leave_transaction.id', 'leave_category.leave_category_name',
            ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                ->where('leave_transaction.leave_date', '<=', $dateToo)
                ->where('leave_transaction.ap_hrd', 1)
                ->where('leave_category.id', 7) //
                ->where('users.active', 1)
                ->get();
        } else {
            if ($townn === 'all') {
                $son = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id', 'leave_category.leave_category_name',
                ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                    ->where('leave_transaction.leave_date', '<=', $dateToo)
                    ->where('leave_transaction.ap_hrd', 1)
                    ->where('leave_category.id', 7)
                    ->where('leave_transaction.r_departure', $namaProvinsi)
                    ->where('users.active', 1)
                    ->get();
            } else {
                $son = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id', 'leave_category.leave_category_name',
                ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                    ->where('leave_transaction.leave_date', '<=', $dateToo)
                    ->where('leave_transaction.ap_hrd', 1)
                    ->where('leave_category.id', 7)
                    ->where('leave_transaction.r_after_leaving', $namaHometown)
                    ->where('users.active', 1)
                    ->get();
            }
        }


        if ($hometown === 'all') {
            $dof = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                'leave_transaction.id', 'leave_category.leave_category_name',
            ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                ->where('leave_transaction.leave_date', '<=', $dateToo)
                ->where('leave_transaction.ap_hrd', 1)
                ->where('leave_category.id', 8) //
                ->where('users.active', 1)
                ->get();
        } else {
            if ($townn === 'all') {
                $dof = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id', 'leave_category.leave_category_name',
                ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                    ->where('leave_transaction.leave_date', '<=', $dateToo)
                    ->where('leave_transaction.ap_hrd', 1)
                    ->where('leave_category.id', 8)
                    ->where('leave_transaction.r_departure', $namaProvinsi)
                    ->where('users.active', 1)
                    ->get();
            } else {
                $dof = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id', 'leave_category.leave_category_name',
                ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                    ->where('leave_transaction.leave_date', '<=', $dateToo)
                    ->where('leave_transaction.ap_hrd', 1)
                    ->where('leave_category.id', 8)
                    ->where('leave_transaction.r_after_leaving', $namaHometown)
                    ->where('users.active', 1)
                    ->get();
            }
        }


        if ($hometown === 'all') {
            $dofl = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                'leave_transaction.id', 'leave_category.leave_category_name',
            ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                ->where('leave_transaction.leave_date', '<=', $dateToo)
                ->where('leave_transaction.ap_hrd', 1)
                ->where('leave_category.id', 9) //
                ->where('users.active', 1)
                ->get();
        } else {
            if ($townn === 'all') {
                $dofl = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id', 'leave_category.leave_category_name',
                ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                    ->where('leave_transaction.leave_date', '<=', $dateToo)
                    ->where('leave_transaction.ap_hrd', 1)
                    ->where('leave_category.id', 9) //
                    ->where('leave_transaction.r_departure', $namaProvinsi)
                    ->where('users.active', 1)
                    ->get();
            } else {
                $dofl = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id', 'leave_category.leave_category_name',
                ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                    ->where('leave_transaction.leave_date', '<=', $dateToo)
                    ->where('leave_transaction.ap_hrd', 1)
                    ->where('leave_category.id', 9) //
                    ->where('leave_transaction.r_after_leaving', $namaHometown)
                    ->where('users.active', 1)
                    ->get();
            }
        }


        if ($hometown === 'all') {
            $matermity = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                'leave_transaction.id', 'leave_category.leave_category_name',
            ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                ->where('leave_transaction.leave_date', '<=', $dateToo)
                ->where('leave_transaction.ap_hrd', 1)
                ->where('leave_category.id', 10) //
                ->where('users.active', 1)
                ->get();
        } else {
            if ($townn === 'all') {
                $matermity = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id', 'leave_category.leave_category_name',
                ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                    ->where('leave_transaction.leave_date', '<=', $dateToo)
                    ->where('leave_transaction.ap_hrd', 1)
                    ->where('leave_category.id', 10) //
                    ->where('leave_transaction.r_departure', $namaProvinsi)
                    ->where('users.active', 1)
                    ->get();
            } else {
                $matermity = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id', 'leave_category.leave_category_name',
                ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                    ->where('leave_transaction.leave_date', '<=', $dateToo)
                    ->where('leave_transaction.ap_hrd', 1)
                    ->where('leave_category.id', 10) //
                    ->where('leave_transaction.r_after_leaving', $namaHometown)
                    ->where('users.active', 1)
                    ->get();
            }
        }


        if ($hometown === 'all') {
            $other = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                'leave_transaction.id', 'leave_category.leave_category_name',
            ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                ->where('leave_transaction.leave_date', '<=', $dateToo)
                ->where('leave_transaction.ap_hrd', 1)
                ->where('leave_category.id', 5) //
                ->where('users.active', 1)
                ->get();
        } else {
            if ($townn === 'all') {
                $other = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id', 'leave_category.leave_category_name',
                ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                    ->where('leave_transaction.leave_date', '<=', $dateToo)
                    ->where('leave_transaction.ap_hrd', 1)
                    ->where('leave_category.id', 5) //
                    ->where('leave_transaction.r_departure', $namaProvinsi)
                    ->where('users.active', 1)
                    ->get();
            } else {
                $other = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id', 'leave_category.leave_category_name',
                ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                    ->where('leave_transaction.leave_date', '<=', $dateToo)
                    ->where('leave_transaction.ap_hrd', 1)
                    ->where('leave_category.id', 5) //
                    ->where('leave_transaction.r_after_leaving', $namaHometown)
                    ->where('users.active', 1)
                    ->get();
            }
        }

        $totalEtc = $sick->count() + $wedding->count() + $bod->count() + $unpaid->count() + $son->count() + $dof->count() + $dofl->count() + $matermity->count() + $other->count();

        if ($total === 0) {
            return redirect()->back()->with('getError', 'Opsss, Data not found!!');
        }

        return view('HRDLevelAcces.leave.summary.detail.indexAll', compact([
            'leave', 'dateFrom', 'dateToo', 'category', 'hometown', 'townn', 'annual', 'exdo', 'etc', 'total',
            'sick', 'wedding', 'bod', 'unpaid', 'son', 'dof', 'dofl', 'matermity', 'other', 'totalEtc',
        ]));
    }

    public function excelDetailAllSummaryLeave($dateFrom, $dateToo, $category, $hometown, $townn)
    {
        $no = 1;

        if ($hometown !== 'all') {
            $namaProvinsi = $this->dataNameProvinsi($hometown);
        }

        if ($townn !== 'all') {
            $namaHometown = $this->dataNameHometown($townn);
        }

        if ($hometown === 'all') {
            $leave = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                'leave_transaction.id', 'leave_transaction.leave_date', 'leave_transaction.end_leave_date', 'leave_transaction.back_work', 'leave_transaction.leave_category_id',
                'leave_transaction.period', 'leave_transaction.pending', 'leave_transaction.total_day', 'leave_transaction.remain', 'leave_transaction.r_departure',
                'leave_transaction.ap_hrd',
                'leave_transaction.r_after_leaving',
                'users.first_name', 'users.last_name', 'users.nik', 'users.join_date', 'users.position', 'users.address',
                'dept_category.dept_category_name',
                'leave_category.leave_category_name',
            ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                ->where('leave_transaction.leave_date', '<=', $dateToo)
                ->where('leave_transaction.ap_hrd', 1)
                ->where('users.active', 1)
                ->get();
        } else {
            if ($townn === 'all') {
                $leave = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id', 'leave_transaction.leave_date', 'leave_transaction.end_leave_date', 'leave_transaction.back_work', 'leave_transaction.leave_category_id',
                    'leave_transaction.period', 'leave_transaction.pending', 'leave_transaction.total_day', 'leave_transaction.remain', 'leave_transaction.r_departure',
                    'leave_transaction.ap_hrd',
                    'leave_transaction.r_after_leaving',
                    'users.first_name', 'users.last_name', 'users.nik', 'users.join_date', 'users.position', 'users.address',
                    'dept_category.dept_category_name',
                    'leave_category.leave_category_name',
                ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                    ->where('leave_transaction.leave_date', '<=', $dateToo)
                    ->where('leave_transaction.ap_hrd', 1)
                    ->where('leave_transaction.r_departure', $namaProvinsi)
                    ->where('users.active', 1)
                    ->get();
            } else {
                $leave = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id', 'leave_transaction.leave_date', 'leave_transaction.end_leave_date', 'leave_transaction.back_work', 'leave_transaction.leave_category_id',
                    'leave_transaction.period', 'leave_transaction.pending', 'leave_transaction.total_day', 'leave_transaction.remain', 'leave_transaction.r_departure',
                    'leave_transaction.ap_hrd',
                    'leave_transaction.r_after_leaving',
                    'users.first_name', 'users.last_name', 'users.nik', 'users.join_date', 'users.position', 'users.address',
                    'dept_category.dept_category_name',
                    'leave_category.leave_category_name',
                ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                    ->where('leave_transaction.leave_date', '<=', $dateToo)
                    ->where('leave_transaction.ap_hrd', 1)
                    ->where('leave_transaction.r_after_leaving', $namaHometown)
                    ->where('users.active', 1)
                    ->get();
            }
        }


        Excel::create('Summary of Leave (All)', function ($excel) use ($leave, $no, $dateFrom, $dateToo, $category) {

            $excel->sheet('Summary of Leave (All)', function ($sheet) use ($leave, $no, $dateFrom, $dateToo, $category) {
                $sheet->loadView('HRDLevelAcces.leave.summary.detail.excelAll', [
                    'leave'     => $leave,
                    'no'        => $no,
                    'dateFrom'  => $dateFrom,
                    'dateToo'   => $dateToo,
                    'category'  => $category
                ]);
            });
        })->download('xls');
    }

    public function detailSummaryLeaeve($dateFrom, $dateToo, $category, $hometown, $townn)
    {

        if ($hometown !== 'all') {
            $namaProvinsi = $this->dataNameProvinsi($hometown);
        }

        if ($townn !== 'all') {
            $namaHometown = $this->dataNameHometown($townn);
        }

        if ($hometown === 'all') {
            $leave = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                'leave_transaction.id', 'leave_transaction.leave_date', 'leave_transaction.end_leave_date', 'leave_transaction.back_work', 'leave_transaction.leave_category_id',
                'leave_transaction.period', 'leave_transaction.pending', 'leave_transaction.total_day', 'leave_transaction.remain', 'leave_transaction.r_departure',
                'leave_transaction.ap_hrd',
                'leave_transaction.r_after_leaving',
                'users.first_name', 'users.last_name', 'users.nik', 'users.join_date', 'users.position', 'users.address',
                'dept_category.dept_category_name',
                'leave_category.leave_category_name',
            ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                ->where('leave_transaction.leave_date', '<=', $dateToo)
                ->where('leave_transaction.ap_hrd', 1)
                ->where('leave_transaction.leave_category_id', $category)
                ->where('users.active', 1)
                ->paginate(10);
        } else {
            if ($townn === 'all') {
                $leave = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id', 'leave_transaction.leave_date', 'leave_transaction.end_leave_date', 'leave_transaction.back_work', 'leave_transaction.leave_category_id',
                    'leave_transaction.period', 'leave_transaction.pending', 'leave_transaction.total_day', 'leave_transaction.remain', 'leave_transaction.r_departure',
                    'leave_transaction.ap_hrd',
                    'leave_transaction.r_after_leaving',
                    'users.first_name', 'users.last_name', 'users.nik', 'users.join_date', 'users.position', 'users.address',
                    'dept_category.dept_category_name',
                    'leave_category.leave_category_name',
                ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                    ->where('leave_transaction.leave_date', '<=', $dateToo)
                    ->where('leave_transaction.ap_hrd', 1)
                    ->where('leave_transaction.leave_category_id', $category)
                    ->where('leave_transaction.r_departure', $namaProvinsi)
                    ->where('users.active', 1)
                    ->paginate(10);
            } else {
                $leave = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id', 'leave_transaction.leave_date', 'leave_transaction.end_leave_date', 'leave_transaction.back_work', 'leave_transaction.leave_category_id',
                    'leave_transaction.period', 'leave_transaction.pending', 'leave_transaction.total_day', 'leave_transaction.remain', 'leave_transaction.r_departure',
                    'leave_transaction.ap_hrd',
                    'leave_transaction.r_after_leaving',
                    'users.first_name', 'users.last_name', 'users.nik', 'users.join_date', 'users.position', 'users.address',
                    'dept_category.dept_category_name',
                    'leave_category.leave_category_name',
                ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                    ->where('leave_transaction.leave_date', '<=', $dateToo)
                    ->where('leave_transaction.ap_hrd', 1)
                    ->where('leave_transaction.leave_category_id', $category)
                    ->where('leave_transaction.r_after_leaving', $namaHometown)
                    ->where('users.active', 1)
                    ->paginate(10);
            }
        }


        $leaveCate = Leave_Category::find($category);

        if ($leave->count() === 0) {
            Session::flash('reminder', Lang::get('messages.data_custom', ['data' => 'Opsss!!, Data ' . $leaveCate->leave_category_name . ' Nothing...']));
            return redirect()->route('hrd/summary/leave/index');
        }

        return view('HRDLevelAcces.leave.summary.detail.index', compact(['leave', 'dateFrom', 'dateToo', 'category', 'hometown', 'townn']));
    }

    public function excelDetailSummaryLeave($dateFrom, $dateToo, $category, $hometown, $townn)
    {
        $no = 1;

        if ($hometown === 'all') {
            $leave = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                'leave_transaction.id', 'leave_transaction.leave_date', 'leave_transaction.end_leave_date', 'leave_transaction.back_work', 'leave_transaction.leave_category_id',
                'leave_transaction.period', 'leave_transaction.pending', 'leave_transaction.total_day', 'leave_transaction.remain', 'leave_transaction.r_departure',
                'leave_transaction.ap_hrd',
                'leave_transaction.r_after_leaving',
                'users.first_name', 'users.last_name', 'users.nik', 'users.join_date', 'users.position', 'users.address',
                'dept_category.dept_category_name',
                'leave_category.leave_category_name',
            ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                ->where('leave_transaction.leave_date', '<=', $dateToo)
                ->where('leave_transaction.ap_hrd', 1)
                ->where('leave_transaction.leave_category_id', $category)
                ->where('users.active', 1)
                ->get();
        } else {
            $leave = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                'leave_transaction.id', 'leave_transaction.leave_date', 'leave_transaction.end_leave_date', 'leave_transaction.back_work', 'leave_transaction.leave_category_id',
                'leave_transaction.period', 'leave_transaction.pending', 'leave_transaction.total_day', 'leave_transaction.remain', 'leave_transaction.r_departure',
                'leave_transaction.ap_hrd',
                'leave_transaction.r_after_leaving',
                'users.first_name', 'users.last_name', 'users.nik', 'users.join_date', 'users.position', 'users.address',
                'dept_category.dept_category_name',
                'leave_category.leave_category_name',
            ])->where('leave_transaction.leave_date', '>=', $dateFrom)
                ->where('leave_transaction.leave_date', '<=', $dateToo)
                ->where('leave_transaction.ap_hrd', 1)
                ->where('leave_transaction.leave_category_id', $category)
                ->where('leave_transaction.r_departure', $hometown)
                ->where('users.active', 1)
                ->get();
        }


        Excel::create('Summary of Leave (All)', function ($excel) use ($leave, $no, $dateFrom, $dateToo, $category) {

            $excel->sheet('Summary of Leave (All)', function ($sheet) use ($leave, $no, $dateFrom, $dateToo, $category) {
                $sheet->loadView('HRDLevelAcces.leave.summary.detail.excel', [
                    'leave'     => $leave,
                    'no'        => $no,
                    'dateFrom'  => $dateFrom,
                    'dateToo'   => $dateToo,
                    'category'  => $category
                ]);
            });
        })->download('xls');
    }

    //
    public function aceh($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[0]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function sumut($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[1]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function sumbar($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[2]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function riau($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[3]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function jambi($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[4]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function sumsel($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[5]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function bengkulu($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[6]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function lampung($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[7]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function kepbanglu($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[8]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function kepri($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[9]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function dkiJakarta($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[10]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function jabar($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[11]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function jateng($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[12]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function yogyakarta($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[13]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function jatim($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[14]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function banten($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[15]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function bali($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[16]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function ntb($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[17]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function ntt($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[18]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function kalbar($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[19]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function kalteng($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[20]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function kalsel($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[21]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function kaltim($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[22]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function kalut($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[23]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function sulut($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[24]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function sulteng($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[25]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function sulsel($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[26]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function sultenggara($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[27]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function gorontalo($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[28]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function maluku($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[30]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function malut($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[31]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function papbar($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[32]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function papua($dateFrom, $dateToo)
    {
        $provinsi = $this->dataProvinsi();
        $provinsi = $provinsi[33]['nama'];

        $query = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id', 'leave_category.leave_category_name', 'leave_transaction.r_departure'
        ])->where('leave_transaction.leave_date', '>=', $dateFrom)
            ->where('leave_transaction.leave_date', '<=', $dateToo)
            ->where('leave_transaction.ap_hrd', 1)
            ->where('leave_transaction.r_departure', $provinsi)
            ->where('users.active', 1)
            ->get();

        return $query->count();
    }

    public function indexSummaryEmpoyeByEmploye(Request $request)
    {
        $id = $request->input('employee');
        $year = $request->input('year');
        $category = $request->input('category');

        $try = [$id, $year, $category];

        $user = User::findOrFail($id);
        $leaveCategory = Leave_Category::findOrFail($category);

        $data = Leave::where('user_id', $request->input('employee'))->where('period', $request->input('year'))->where('leave_category_id', $request->input('category'))->get();

        return view('HRDLevelAcces.leave.summary.detail.byEmployee', compact([
            'data', 'user', 'leaveCategory', 'id', 'year', 'category'
        ]));
    }

    public function editSummaryOfLeave($id)
    {
        $data = Leave::JoinUsers()->joinDeptCategory()->findOrFail($id);

        $category = Leave_Category::all();

        $provinsi = file_get_contents('https://dev.farizdotid.com/api/daerahindonesia/provinsi');
        $provinsi = json_decode($provinsi, true);
        $provinsi = $provinsi['provinsi'];

        $exdo = Initial_Leave::where('user_id', $data->user_id)->pluck('initial')->sum();
        $advanceExdo = $this->exdo($id, $exdo);
        $advancedLeaveAnnual = $this->advancedLeaveAnnual($data->user_id);

        return view('HRDLevelAcces.leave.summary.edit', compact(['data', 'category', 'provinsi', 'advancedLeaveAnnual', 'advanceExdo', 'id']));
    }

    public function advancedLeaveAnnual($id)
    {
        $user = NewUser::findOrFail($id);

        $annual = Leave::select([
            DB::raw('
                                (
                                    select (
                                        select COALESCE(sum(total_day), 0) from leave_transaction where user_id=' . $user->id . ' and leave_category_id=1
                                        and ap_hd  = 1
                                        and ap_gm  = 1
                                        and ver_hr = 1
                                        and ap_hrd = 1
                                    )
                                ) as transactionAnnual')
        ])->first();

        $startDate = date_create($user->join_date);
        $endDate = date_create($user->end_date);

        $startYear = date('Y', strtotime($user->join_date));
        $endYear = date('Y', strtotime($user->end_date));

        if ($user->emp_status === "Permanent") {
            $yearEnd = date('Y');
        } else {
            $yearEnd = $endYear;
        }

        $now = date_create();
        $now1 = date_create(date('Y') . '-01-01');
        $now2 = date_create(date('Y') . '-12-31');

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

        // $daff = date_diff($startDate, $sekarang)->format('%m')+(12*date_diff($startDate, $sekarang->modify('+5 day'))->format('%y'));

        $daffPermanent = date_diff($now1, $now)->format('%m') + (12 * date_diff($now1, $now->modify('+5 day'))->format('%y'));

        $daffPermanent2 = date_diff($now1, $now2)->format('%m') + (12 * date_diff($now1, $now2->modify('+5 day'))->format('%y'));

        $daffPermanent1 = 12 - $daffPermanent;



        if ($passs <= $annual->transactionAnnual) {
            $newAnnual =  $annual->transactionAnnual;
        } else {
            $newAnnual = $passs;
        }

        $totalAnnual = $newAnnual - $annual->transactionAnnual;

        $totalAnnualPermanent = $user->initial_annual - $annual->transactionAnnual;

        $totalAnnualPermanent1 = $totalAnnualPermanent - $daffPermanent1;

        if ($user->emp_status === "Permanent") {
            return $totalAnnualPermanent1;
        } else {
            return $totalAnnual;
        }
    }

    public function exdo($id, $exdo)
    {
        $w = Initial_Leave::where('user_id', $id)
            ->whereDATE('expired', '<', date('Y-m-d'))
            ->pluck('initial');

        $minusExdo = Leave::where('user_id', $id)->where('leave_category_id', 2)->where('ap_hd', 1)->where('ap_gm', 1)->where('ver_hr', 1)->where('ap_hrd', 1)->pluck('total_day');

        $goingExdo = 0;

        if ($w->sum() >= $minusExdo->sum()) {
            $goingExdo = $w->sum() - $minusExdo->sum();
        }

        $sisaExdo = $exdo - $minusExdo->sum() - $goingExdo;

        if ($sisaExdo < 0) {
            $sisaExdo = $sisaExdo * -1;
            $sisaExdo = $sisaExdo - $sisaExdo;
        }
        return $sisaExdo;
    }

    public function updateDataSummaryLeave(Request $request, $id)
    {
        $rule = [
            'startLeaveDate'    => 'required',
            'endLeaveDate'      => 'required',
            'backWork'          => 'required',
            'request_day'           => 'required|numeric',
            'reason'            => 'required|max:100',
        ];

        $data = [
            'leave_date'        => $request->input('startLeaveDate'),
            'end_leave_date'    => $request->input('endLeaveDate'),
            'back_work'         => $request->input('backWork'),
            'total_day'         => $request->input('request_day'),
            'reason_leave'      => $request->input('reason'),
            'ap_koor'           => 1,
            'ap_spv'            => 1,
            'ap_pm'             => 1,
            'ap_producer'       => 1,
            'ap_hd'             => 1,
            'ver_hr'            => 1,
            'ap_hrd'            => 1,
            'ap_infinite'       => 0,
        ];

        $validator = Validator::make($request->all(), $rule);

        if ($validator->fails()) {
            return Redirect::route('hrd/summary/leave/edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        Leave::where('id', $id)->update($data);
        Session::flash('message', Lang::get('messages.data_updated', ['data' => 'eForm']));
        return redirect()->route('hrd/summary/leave/index');
    }

    public function destroyDataSummaryLeave($id)
    {
        $return   = "
        <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
        </div>
        <div class='modal-body'>
            <div class='panel'>
                <h3>Are u sure delete this form ?</h3>
            </div>
        </div>
        <div class='modal-footer'>
            <a href=" . route('hrd/summary/leave/destroy/push', $id) . " class='btn btn-sm btn-danger'>Delete</a>
            <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
        </div>
    ";

        return $return;
    }

    public function putDestroyDataSummaryLeave($id)
    {
        Leave::where('id', $id)->delete();
        Session::flash('message', Lang::get('messages.data_deleted', ['data' => 'eForm']));
        return redirect()->route('hrd/summary/leave/index');
    }

    public function downloadDetailSummaryExdo(Request $request)
    {
        $id = $request->input('id');
        $year = $request->input('year');
        $category = $request->input('category');

        $leaveCategory = Leave_Category::find($category);

        $data = Leave::where('user_id', $id)->where('period', $year)->where('leave_category_id', $category)->get();

        Excel::create('Summary Exdo', function ($excel) use ($data, $leaveCategory) {
            $excel->sheet('Summary Exdo', function ($sheet) use ($data, $leaveCategory) {
                $sheet->setAutoSize(true);
                $sheet->loadView('HRDLevelAcces.leave.summary.detail.excelByEmployee', compact('data', 'leaveCategory'));
            });
        })->export('xls');

        Session::flash('message', Lang::get('messages.data_custom', ['data' => 'Downloaded Successfully !!']));
        return redirect()->back();
    }
}
