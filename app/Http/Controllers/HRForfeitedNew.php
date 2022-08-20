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

class HRForfeitedNew extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'active', 'hr']);
    }

    public function index()
    {
        return view('HRDLevelAcces.forfeited.indexNew');
    }

    public function dataForfeited()
    {
        $query = User::where('active', 1)
            ->select([
                'id',
                'first_name',
                'last_name',
                'nik',
                'dept_category_id',
                'position',
                'emp_status',
                'initial_annual'
            ])
            ->where('nik', '!=', '123456789')
            ->where('nik', '!=', null)
            // ->whereIn('id', [111, 4, 1150, 69, 200, 226])
            // ->where('id', 226)
            // ->where('emp_status', 'Permanent')
            ->where('emp_status', '!=', 'Outsource')
            ->orderBy('first_name', 'asc')
            ->get();

        return Datatables::of($query)
            ->addIndexColumn()
            ->editColumn('first_name', function (User $user) {
                return $user->first_name . ' ' . $user->last_name;
            })
            ->addColumn('dept', function (User $user) {
                $dept = Dept_Category::find($user->dept_category_id);

                return $dept->dept_category_name;
            })
            ->addColumn('totalAnnual', function (User $user) {
                $return = $user->initial_annual;

                return $return;
            })
            ->addColumn('takenAnnual', function (User $user) {
                $return = $this->takenAnnual($user->id);

                return $return;
            })
            ->addColumn('remainsAnnual', function (User $user) {

                $totalAnnual = $user->initial_annual;

                $takenAnnual = $this->takenAnnual($user->id);

                $return = $totalAnnual - $takenAnnual;

                return $return;
            })
            ->addColumn('newForfeited', function (User $user) {
                $return = $this->newForfeited($user->id);

                return $return;
            })
            ->addColumn('forfeited', function (User $user) {
                $return = $this->forfeited($user->id);

                return $return;
            })
            ->addColumn('availableAnnual', function (User $user) {
                $totalAnnual = $user->initial_annual;

                $takenAnnual = $this->takenAnnual($user->id);

                $remains = $totalAnnual - $takenAnnual;

                $year = $this->availableYear();

                if ($remains > $year) {
                    $count = $year;
                } else {
                    $count = 0;
                }

                $return = $count;

                $try = [
                    'remains'   => $remains,
                    'year'      => $year,
                    'return'    => $return
                ];

                return $return;
            })
            ->addColumn('balanceAnnual', '{{ $availableAnnual + $forfeited }}')
            ->addColumn('advanceAnnual', '{{ $remainsAnnual - $balanceAnnual }}')
            ->addColumn('availableExdo', function (User $user) {
                $availableExdo = $this->availableExdo($user->id);

                return $availableExdo;
            })
            ->addColumn('remainsExdo', function (User $user) {
                $return = $this->remainsExdo($user->id);

                return $return;
            })
            ->make(true);
    }

    public function enttiledLeaveView($nik)
    {
        $annual = Entitled_leave_view::where('nik', $nik)->first();

        if ($annual->annual_leave_balance <= 0) {
            $return = 0;
        } else {
            $return = $annual->annual_leave_balance;
        }

        return $return;
    }

    public function takenAnnual($id)
    {
        $return = Leave::where('leave_category_id', 1)->where('user_id', $id)->where('ap_hd', 1)->where('ver_hr', 1)->where('ap_hrd', 1)->where('ap_gm', 1)->pluck('total_day')->sum();

        return $return;
    }

    public function availableYear()
    {
        $startJoin = date_create(date('Y' . '-01-01'));
        $now = date_create(date('Y-m-d'));

        $day = date_diff($startJoin, $now)->format('%d') + 2;

        $countDay = 0;

        if ($day >= 29) {
            $countDay = 1;
        }

        $month = date_diff($startJoin, $now)->format('%m') + $countDay;

        $year = date_diff($startJoin, $now)->format('%y') * 12;

        $return = ($month + $year);

        $try = [
            'month' => $month,
            'year'  => $year,
            'return' => $return,
        ];

        return $return;
    }

    public function availableAnnual($id)
    {
        $user = User::find($id);

        $startJoin = date_create($user->join_date);
        $now = date_create(date('Y-m-d'));

        $day = date_diff($startJoin, $now)->format('%d') + 2;

        $countDay = 0;

        if ($day >= 29) {
            $countDay = 1;
        }

        $month = date_diff($startJoin, $now)->format('%m') + $countDay;

        $year = date_diff($startJoin, $now)->format('%y') * 12;

        $return = ($month + $year);

        $try = [
            'month' => $month,
            'year'  => $year,
            'return' => $return,
        ];

        return $return;
    }

    public function initialAnnual($id)
    {
        $user = User::find($id);

        $endDate = $user->end_date;

        if ($user->emp_status == "Permanent") {
            $endDate = date('Y' . '-12-31');
        }

        $startJoin = date_create($user->join_date);
        $endJoin = date_create($endDate);

        $day = date_diff($startJoin, $endJoin)->format('%d') + 2;
        $countDay = 0;

        if ($day >= 29) {
            $countDay = 1;
        }

        $month = date_diff($startJoin, $endJoin)->format('%m') + $countDay;

        $year = date_diff($startJoin, $endJoin)->format('%y') * 12;

        $return = $month + $year;

        $try = [
            'day' => $day,
            'month' => $month,
            'year'  => $year,
            'return' => $return
        ];

        return $return;
    }

    public function newForfeited($id)
    {
        $return = Forfeited::where('year', date('Y', strtotime('-1 year')))->where('user_id', $id)->pluck('countAnnual')->sum();

        return $return;
    }

    public function forfeited($id)
    {
        $new = $this->newForfeited($id);
        $old = Forfeited::where('year', '!=', date('Y', strtotime('-1 year')))->where('user_id', $id)->pluck('countAnnual')->sum();

        $taken = ForfeitedCounts::where('user_id', $id)->where('status', 1)->pluck('amount')->sum();

        if ($old == 0) {
            $taken = 0;
        }

        $count = $old - $taken;

        if ($count < 0) {
            $count = $count * -1;
        }

        $return = $count;

        if ($taken == 0 and $old == 0) {
            $return = $new;
        }

        if ($taken > $old) {
            $return = $new - $count;
        }

        $try = [
            'new'   => $new,
            'old'   => $old,
            'taken' => $taken,
            'count' => $count,
            'return' => $return
        ];

        return $return;
    }

    public function takenExdo($id)
    {
        $return = Leave::where('leave_category_id', 2)->where('user_id', $id)->where('ap_hd', 1)->where('ver_hr', 1)->where('ap_hrd', 1)->where('ap_gm', 1)->pluck('total_day')->sum();

        return $return;
    }

    public function availableExdo($id)
    {
        $exdo = Initial_Leave::where('user_id', $id)->where('expired', '=>', date('Y-m-d', strtotime('+1 month')))->where('expired', '<=', date('Y-m-d'))->pluck('initial')->sum();

        return $exdo;
    }

    public function remainsExdo($id)
    {
        $takenExdo = $this->takenExdo($id);

        $remainsExdo = Initial_Leave::where('user_id', $id)->pluck('initial')->sum();

        $return = $remainsExdo - $takenExdo;

        return $return;
    }
}
