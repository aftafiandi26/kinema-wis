<?php

namespace App\Http\Controllers;

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
use App\User;
use App\Forfeited;
use App\ForfeitedCounts;
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

class LeaveController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth', 'active']);
    }

    public function indexLeaveApply()
    {
        $ent_exdo = DB::table('users')
            ->leftJoin('initial_leave', 'initial_leave.user_id', '=', 'users.id')
            ->leftJoin('leave_category', 'leave_category.id', '=', 'initial_leave.leave_category_id')
            ->leftJoin('leave_transaction', 'leave_transaction.user_id', '=', 'users.id')

            ->select([
                DB::raw('
            (
                (
                    select COALESCE(sum(initial), 0) from initial_leave where user_id=' . Auth::user()->id . ' and leave_category_id=2
                )
            ) as entitle_exdo')
            ])
            ->first();

        $select = DB::table('users')
            ->leftJoin('initial_leave', 'initial_leave.user_id', '=', 'users.id')
            ->leftJoin('leave_category', 'leave_category.id', '=', 'initial_leave.leave_category_id')
            ->leftJoin('leave_transaction', 'leave_transaction.user_id', '=', 'users.id')

            ->select([
                DB::raw('
            (
                select (
                    select initial_annual from users where id=' . Auth::user()->id . '
                ) - (
                    select COALESCE(sum(total_day), 0) from leave_transaction where user_id=' . Auth::user()->id . ' and leave_category_id=1
                    and ap_hd  = 1
                    and ap_gm  = 1
                    and ver_hr = 1
                    and ap_hrd = 1
                )
            ) as remainannual')
            ])
            ->first();



        $selectexdo = DB::table('users')
            ->leftJoin('initial_leave', 'initial_leave.user_id', '=', 'users.id')
            ->leftJoin('leave_category', 'leave_category.id', '=', 'initial_leave.leave_category_id')
            ->leftJoin('leave_transaction', 'leave_transaction.user_id', '=', 'users.id')
            ->select([
                DB::raw('
            (
                select (
                    select COALESCE(sum(initial), 0) from initial_leave where user_id=' . Auth::user()->id . ' and leave_category_id=2
                ) - (
                    select COALESCE(sum(total_day), 0) from leave_transaction where user_id=' . Auth::user()->id . ' and leave_category_id=2
                    and ap_hd  = 1
                    and ap_gm  = 1
                    and ver_hr = 1
                    and ap_hrd = 1
                )
            ) as remainexdo')
            ])
            ->first();

        $exp_exdo = DB::table('initial_leave')

            ->whereRaw('initial_leave.exp_date >= current_date')
            ->orderByRaw('initial_leave.exp_date asc')
            ->first();


        return View::make('leave.indexApply')->with(['select' => $select, 'selectexdo' => $selectexdo, 'ent_exdo' => $ent_exdo, 'exp_exdo' => $exp_exdo]);
    }

    public function indexNewApply()
    {
        $annual = DB::table('users')
            ->leftJoin('initial_leave', 'initial_leave.user_id', '=', 'users.id')
            ->leftJoin('leave_category', 'leave_category.id', '=', 'initial_leave.leave_category_id')
            ->leftJoin('leave_transaction', 'leave_transaction.user_id', '=', 'users.id')

            ->select([
                DB::raw('
            (
                select (
                    select COALESCE(sum(total_day), 0) from leave_transaction where user_id=' . Auth::user()->id . ' and leave_category_id=1
                    and ap_hd  = 1
                    and ap_gm  = 1
                    and ver_hr = 1
                    and ap_hrd = 1
                )
            ) as transactionAnnual')
            ])
            ->first();

        $user = User::find(auth::user()->id);

        $startDate = date_create($user->join_date);
        $endDate = date_create($user->end_date);

        $startYear = date('Y', strtotime($user->join_date));
        $endYear = date('Y', strtotime($user->end_date));

        if (auth::user()->emp_status === "Permanent") {
            $yearEnd = date('Y');
        } else {
            $yearEnd = $endYear;
        }

        $now = date_create(date('Y-m-d'));
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
        // dd($user->initial_annual);
        if (auth::user()->id === 117) {
        }

        $totalAnnualPermanent = $user->initial_annual - $annual->transactionAnnual;

        $totalAnnualPermanent1 = $totalAnnualPermanent - $daffPermanent1;


        //-------------------------------------------------

        $exdo = Initial_Leave::where('user_id', auth::user()->id)->pluck('initial');

        $w = Initial_Leave::where('user_id', auth::user()->id)
            ->whereDATE('expired', '<', date('Y-m-d'))
            ->pluck('initial');

        $expiredExdo = $w;

        $minusExdo = Leave::where('user_id', auth::user()->id)->where('leave_category_id', 2)->where('ap_hd', 1)->where('ap_gm', 1)->where('ver_hr', 1)->where('ap_hrd', 1)->pluck('total_day');

        $goingExdo = 0;

        if ($expiredExdo->sum() >= $minusExdo->sum()) {
            $goingExdo = $expiredExdo->sum() - $minusExdo->sum();
        }

        $sisaExdo = $exdo->sum() - $minusExdo->sum() - $goingExdo;

        $test = [
            'exdo' => $exdo->sum(),
            'w'    => $w->sum(),
            'minusExdo'  => $minusExdo->sum(),
            'sisaExdo'    => $sisaExdo,
            'goingExdo'   => $goingExdo
        ];

        $forfeited = Forfeited::where('user_id', auth::user()->id)->pluck('countAnnual');
        $forfeitedCounts = ForfeitedCounts::where('user_id', auth::user()->id)->where('status', 1)->pluck('amount');
        $countAmount = $forfeited->sum() - $forfeitedCounts->sum();

        $bla = 0;
        if ($countAmount >= 0) {
            $bla = $countAmount;
        } else {
            $bla = 0;
        }

        $bla = 0;
        if ($countAmount >= 0) {
            $bla = $countAmount;
        } else {
            $bla = 0;
        }

        $renewPermanet = $totalAnnualPermanent1 - $bla;
        $renewContract = $totalAnnual - $bla;

        return view('leave.NewAnnual.indexNewAnnual', [
            'annual'      => $annual,
            'totalAnnual' => $totalAnnual,
            'totalAnnualPermanent1' => $totalAnnualPermanent1,
            'remainExdo'     => $sisaExdo,
            'startYear'     => $startYear,
            'yearEnd'       => $yearEnd,
            'user'      => $user,
            'exdo'      => $exdo,
            'minusExdo' => $minusExdo,
            'w' => $w,
            'forfeited'         => $forfeited,
            'forfeitedCounts'   => $forfeitedCounts,
            'countAmount'       => $countAmount,
            'bla'               => $bla,
            'renewPermanet'     => $renewPermanet,
            'renewContract'     => $renewContract

        ]);
    }

    public function indexDataExdo()
    {
        $model = Initial_Leave::where('user_id', auth::user()->id)
            ->where('expired', '>=', date('Y-m-d', strtotime('-2 months')))
            ->orderBy('expired', 'asc')
            ->get();

        return Datatables::of($model)
            ->addIndexColumn()
            ->addColumn('difforHumans', function (Initial_Leave $initial) {
                $carbon =  Carbon::parse($initial->expired);
                return $carbon->diffForHumans();
            })
            ->setRowClass('@if ($expired <= date("Y-m-d")){{ "danger" }}@endif')
            ->make(true);
    }

    public function createLeave()
    {
        $department = dept_category::where(['id' => Auth::user()->dept_category_id])->value('dept_category_name');

        $init_annual = DB::table('users')
            ->leftJoin('initial_leave', 'initial_leave.user_id', '=', 'users.id')
            ->leftJoin('leave_category', 'leave_category.id', '=', 'initial_leave.leave_category_id')

            ->select([
                DB::raw('
            (
                select (
                    select initial_annual from users where id=' . Auth::user()->id . '
                ) - (
                    select COALESCE(sum(total_day), 0) from leave_transaction where user_id=' . Auth::user()->id . ' and leave_category_id=1
                    and ap_hd  = 1
                    and ap_gm  = 1
                    and ver_hr = 1
                    and ap_hrd = 1
                )
            ) as remainannual')
            ])
            ->first();

        $annual = DB::table('users')
            ->leftJoin('initial_leave', 'initial_leave.user_id', '=', 'users.id')
            ->leftJoin('leave_category', 'leave_category.id', '=', 'initial_leave.leave_category_id')
            ->leftJoin('leave_transaction', 'leave_transaction.user_id', '=', 'users.id')

            ->select([
                DB::raw('
            (
                select (
                    select COALESCE(sum(total_day), 0) from leave_transaction where user_id=' . Auth::user()->id . ' and leave_category_id=1
                    and ap_hd  = 1
                    and ap_gm  = 1
                    and ver_hr = 1
                    and ap_hrd = 1
                )
            ) as transactionAnnual')
            ])
            ->first();

        $user = User::find(auth::user()->id);

        $startDate = date_create($user->join_date);
        $endDate = date_create($user->end_date);

        $startYear = date('Y', strtotime($user->join_date));
        $endYear = date('Y', strtotime($user->end_date));

        if (auth::user()->emp_status === "Permanent") {
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

        $daff = date_diff($startDate, $sekarang)->format('%m') + (12 * date_diff($startDate, $sekarang->modify('+5 day'))->format('%y'));

        $daffPermanent = date_diff($now1, $now)->format('%m') + (12 * date_diff($now1, $now->modify('+5 day'))->format('%y'));

        $daffPermanent2 = date_diff($now1, $now2)->format('%m') + (12 * date_diff($now1, $now2->modify('+5 day'))->format('%y'));

        $daffPermanent1 = 12 - $daffPermanent;



        if ($daff <= $annual->transactionAnnual) {
            $newAnnual =  $annual->transactionAnnual;
        } else {
            $newAnnual = $daff;
        }

        $totalAnnual = $newAnnual - $annual->transactionAnnual;

        $totalAnnualPermanent = $user->initial_annual - $annual->transactionAnnual;

        $totalAnnualPermanent1 = $totalAnnualPermanent - $daffPermanent1;

        $getAnnualBalance = null;

        $forfeited = Forfeited::where('user_id', auth::user()->id)->pluck('countAnnual');
        $forfeitedCounts = ForfeitedCounts::where('user_id', auth::user()->id)->where('status', 1)->pluck('amount');
        $countAmount = $forfeited->sum() - $forfeitedCounts->sum();

        $bla = 0;

        if (auth::user()->forfeitcase === 1) {
            $bla = 0;
        } else {
            if ($countAmount >= 0) {
                $bla = $countAmount;
            } else {
                $bla = 0;
            }
        }


        if (auth::user()->emp_status === "Permanent") {
            $getAnnualBalance = $totalAnnualPermanent1 - $bla;
        } else {
            $getAnnualBalance = $totalAnnual - $bla;
        }

        // dd($totalAnnualPermanent1);

        $taken = DB::table('users')
            ->leftJoin('initial_leave', 'initial_leave.user_id', '=', 'users.id')
            ->leftJoin('leave_category', 'leave_category.id', '=', 'initial_leave.leave_category_id')
            ->leftJoin('leave_transaction', 'leave_transaction.user_id', '=', 'users.id')
            ->select([
                DB::raw('
            (
                (
                    select COALESCE(sum(total_day), 0) from leave_transaction where user_id=' . Auth::user()->id . ' and leave_category_id=1
                    and ap_hd  = 1
                    and ap_gm  = 1
                    and ver_hr = 1
                    and ap_hrd = 1
                )
            ) as leavetaken')
            ])
            ->first();

        $ent_annual = DB::table('users')
            ->leftJoin('initial_leave', 'initial_leave.user_id', '=', 'users.id')
            ->leftJoin('leave_category', 'leave_category.id', '=', 'initial_leave.leave_category_id')
            ->leftJoin('leave_transaction', 'leave_transaction.user_id', '=', 'users.id')
            ->select([
                DB::raw('
            (
                (
                    select COALESCE(sum(initial), 0) from initial_leave where user_id=' . Auth::user()->id . ' and leave_category_id=1
                )
            ) as entitle_ann')
            ])
            ->first();

        $leave_day = DB::table('users')
            ->leftJoin('initial_leave', 'initial_leave.user_id', '=', 'users.id')
            ->leftJoin('leave_category', 'leave_category.id', '=', 'initial_leave.leave_category_id')
            ->leftJoin('leave_transaction', 'leave_transaction.user_id', '=', 'users.id')
            ->select([
                DB::raw('
            (
                select (
                    SELECT datediff(end_leave_date, leave_date) FROM leave_transaction

                )
            ) as remainleaveday')
            ])
            ->first();

        $pro_category = User::where('users.koor', '=', 1)->where('active', 1)->where('users.dept_category_id', 6)->orderBy('first_name', 'asc')->get();
        foreach ($pro_category as $value)
            $proe[$value->email] = $value->first_name . ' ' . $value->last_name;

        $pm_category = User::where('users.pm', '=', 1)->where('active', 1)->orderBy('first_name', 'asc')->get();
        foreach ($pm_category as $value)
            $pmm[$value->email] = $value->first_name . ' ' . $value->last_name;

        $level_hrd =  User::where('level_hrd', '=', 'Senior Pipeline')->where('dept_category_id', 6)->where('active', 1)->get();
        foreach ($level_hrd as $value)
            $level[$value->email] =  $value->first_name . ' ' . $value->last_name;

        $ricky = null;

        $ghea =   User::select('email')->where('active', 1)->where('hd', '=', '1')->where('dept_category_id', 6)->pluck('email');

        $infiniteApproved = User::where('active', 1)->where('infiniteApprove', 1)->where('dept_ApprovedHOD', auth::user()->dept_category_id)->value('email');

        $johnReedel = User::select('email')->where('active', 1)->where('hd', 1)->where('dept_category_id', 7)->value('email');

        $assistGM = User::select('email')->where('active', 1)->where('assistGM', 1)->value('email');
        if (auth::user()->assistGM == 1 && auth::user()->hd == 1) {
            $assistGM = $ghea[0];
        }

        if (auth::user()->dept_category_id === 7 and auth::user()->hd === 1) {
            $assistGM = User::select('email')->where('username', 'hr')->where('hr', 1)->value('email');
        }

        if (auth::user()->hd == 1 && auth::user()->dept_category_id == 4 or auth::user()->dept_category_id == 6) {
            $assistGM = $ghea[0];
        }

        if (auth::user()->hd !== 1) {
            $assistGM = "";
        }

        $lineProducer = User::where('active', 1)->where('producer', 1)->where('dept_category_id', 6)->orderBy('first_name', 'asc')->get();
        foreach ($lineProducer as $value)
            $producer[$value->email] =  $value->first_name . ' ' . $value->last_name;

        $supervisor = User::where('active', 1)->where('spv', 1)->whereIn('dept_category_id', [4, 6])->orderBy('first_name', 'asc')->get();
        foreach ($supervisor as $value)
            $spv[$value->email] = $value->first_name . ' ' . $value->last_name;

        $coordinatorIT = User::where('active', 1)->where('koor', 1)->where('dept_category_id', 1)->get();
        foreach ($coordinatorIT as $coorIT)
            $ITcoor[$coorIT->email] = $coorIT->first_name . ' ' . $coorIT->last_name;

        // $provinsi = file_get_contents('http://dev.farizdotid.com/api/daerahindonesia/provinsi');
        $provinsi = file_get_contents('https://ibnux.github.io/data-indonesia/propinsi.json');
        $provinsii = json_decode($provinsi, true);
        // $provinsii = $provinsii['provinsi'];

        // dd($provinsii);

        $adminFacility = NewUser::where('active', 1)->where('dept_category_id', 5)->where('position', 'Admin Facility')->value('id');

        if (Auth::user()->dept_category_id !== 5) {
            $adminFacility = null;
        }

        if (auth::user()->hd === 1) {
            if (auth::user()->dept_category_id === 7) {
                $emailHoD = User::where('active', 1)->where('gm', 1)->where('sg', 1)->get();
            } elseif (auth::user()->dept_category_id === 5) {
                $emailHoD = User::where('active', 1)->where('gm', 1)->where('sg', 1)->get();
            } else {
                $emailHoD = User::where('active', 1)->where('gm', 1)->where('sg', 0)->get();
            }
            $labelEmailHOD = 'Verify by :';
        } else {
            if (auth::user()->dept_category_id === 4) {
                $emailHoD = User::where('active', 1)->where('dept_category_id', 6)->where('hd', 1)->get();
                $labelEmailHOD = 'Produciton Manager :';
            } else {
                // 1175 id Mia sinaga -> apply forward ke John Radel
                if (auth::user()->id === 1175) {
                    $emailHoD = User::where('active', 1)->where('dept_category_id', 7)->where('hd', 1)->get();
                } else {
                    $emailHoD = User::where('active', 1)->where('dept_category_id', auth::user()->dept_category_id)->where('hd', 1)->get();
                }

                $labelEmailHOD = 'Head of Department :';
            }
        }

        foreach ($emailHoD as $Hod)
            $emailHOD[$Hod->email] = $Hod->first_name . ' ' . $Hod->last_name;

        // dd($pro_category);

        if ($init_annual->remainannual) {
            return View::make('leave.create')->with([
                'leave' => $getAnnualBalance,
                'department' => $department,
                'labelEmailHOD' => $labelEmailHOD,
                'taken' => $taken->leavetaken,
                'ent_annual' => $ent_annual->entitle_ann,
                'proe' => $proe,
                'pro_category' => $pro_category,
                'pmm'       => $pmm,
                'pm_category' => $pm_category,
                'level' => $level,
                'ricky' => $ricky,
                'ghea' => $ghea,
                'provinsii' => $provinsii,
                // 'leave_day' => $leave_day->remainleaveday
                'emailHOD' => $emailHOD,
                'infiniteApproved' => $infiniteApproved,
                'johnReedel' => $johnReedel,
                'producer'  => $lineProducer,
                'spv'       => $spv,
                'supervisor'       => $supervisor,
                'ITcoor'     => $ITcoor,
                'adminFacility' => $adminFacility,
                'assistGM' => $assistGM

            ]);
        } else {
            return Redirect::route('leave/apply');
        }

        return View::make('leave.create');
    }

    public function createExdo()
    {
        $department  = dept_category::where(['id' => Auth::user()->dept_category_id])->value('dept_category_name');

        $init_annual = DB::table('users')
            ->leftJoin('initial_leave', 'initial_leave.user_id', '=', 'users.id')
            ->leftJoin('leave_category', 'leave_category.id', '=', 'initial_leave.leave_category_id')
            ->leftJoin('leave_transaction', 'leave_transaction.user_id', '=', 'users.id')
            ->select([
                DB::raw('
            (
                select (
                    select COALESCE(sum(initial), 0) from initial_leave where user_id=' . Auth::user()->id . ' and leave_category_id=2
                ) - (
                    select COALESCE(sum(total_day), 0) from leave_transaction where user_id=' . Auth::user()->id . ' and leave_category_id=2
                    and ap_hd  = 1
                    and ap_gm  = 1
                    and ver_hr = 1
                    and ap_hrd = 1
                )
            ) as remainexdo')
            ])
            ->first();

        $taken = DB::table('users')
            ->leftJoin('initial_leave', 'initial_leave.user_id', '=', 'users.id')
            ->leftJoin('leave_category', 'leave_category.id', '=', 'initial_leave.leave_category_id')
            ->leftJoin('leave_transaction', 'leave_transaction.user_id', '=', 'users.id')
            ->select([
                DB::raw('
            (
                (
                    select COALESCE(sum(total_day), 0) from leave_transaction where user_id=' . Auth::user()->id . ' and leave_category_id=2
                    and ap_hd  = 1
                    and ap_gm  = 1
                    and ver_hr = 1
                    and ap_hrd = 1
                )
            ) as leavetaken')
            ])
            ->first();

        $ent_exdo = DB::table('users')
            ->leftJoin('initial_leave', 'initial_leave.user_id', '=', 'users.id')
            ->leftJoin('leave_category', 'leave_category.id', '=', 'initial_leave.leave_category_id')
            ->leftJoin('leave_transaction', 'leave_transaction.user_id', '=', 'users.id')
            ->select([
                DB::raw('
            (
                (
                    select COALESCE(sum(initial), 0) from initial_leave where user_id=' . Auth::user()->id . ' and leave_category_id=2
                )
            ) as entitle_exdo')
            ])
            ->first();


        $pro_category = User::where('users.koor', '=', 1)->where('active', 1)->where('users.dept_category_id', 6)->orderBy('first_name', 'asc')->get();
        foreach ($pro_category as $value)
            $proe[$value->email] = $value->first_name . ' ' . $value->last_name;

        $pm_category = User::where('users.pm', '=', 1)->where('active', 1)->where('users.dept_category_id', 6)->orderBy('first_name', 'asc')->get();
        foreach ($pm_category as $value)
            $pmm[$value->email] = $value->first_name . ' ' . $value->last_name;

        $level_hrd =  User::where('level_hrd', '=', 'Senior Pipeline')->where('dept_category_id', 6)->where('active', 1)->get();
        foreach ($level_hrd as $value)
            $level[$value->email] =  $value->first_name . ' ' . $value->last_name;

        $ricky = null;

        $ghea =   User::select('email')->where('hd', '=', '1')->where('dept_category_id', 6)->pluck('email');

        $infiniteApproved = User::where('active', 1)->where('infiniteApprove', 1)->where('dept_ApprovedHOD', auth::user()->dept_category_id)->value('email');

        $johnReedel = User::select('email')->where('active', 1)->where('hd', 1)->where('dept_category_id', 7)->value('email');

        $lineProducer = User::where('active', 1)->where('producer', 1)->where('dept_category_id', 6)->orderBy('first_name', 'asc')->get();
        foreach ($lineProducer as $value)
            $producer[$value->email] =  $value->first_name . ' ' . $value->last_name;

        $supervisor = User::where('active', 1)->where('spv', 1)->whereIn('dept_category_id', [6, 4])->orderBy('first_name', 'asc')->get();

        $coordinatorIT = User::where('active', 1)->where('koor', 1)->where('dept_category_id', 1)->get();
        foreach ($coordinatorIT as $coorIT)
            $ITcoor[$coorIT->email] = $coorIT->first_name . ' ' . $coorIT->last_name;

        // $provinsi = file_get_contents('http://dev.farizdotid.com/api/daerahindonesia/provinsi');
        $provinsi = file_get_contents('https://ibnux.github.io/data-indonesia/propinsi.json');
        $provinsii = json_decode($provinsi, true);
        // $provinsii = $provinsii['provinsi'];

        // dd($infiniteApproved);

        $adminFacility = NewUser::where('active', 1)->where('dept_category_id', 5)->where('position', 'Admin Facility')->value('id');

        $assistGM = User::select('email')->where('active', 1)->where('assistGM', 1)->value('email');
        if (auth::user()->assistGM == 1 && auth::user()->hd == 1) {
            $assistGM = $ghea[0];
        }

        if (auth::user()->dept_category_id === 7 and auth::user()->hd === 1) {
            $assistGM = User::select('email')->where('username', 'hr')->where('hr', 1)->value('email');
        }

        if (auth::user()->hd == 1 && auth::user()->dept_category_id == 4 or auth::user()->dept_category_id == 6) {
            $assistGM = $ghea[0];
        }

        if (auth::user()->hd !== 1) {
            $assistGM = "";
        }

        if (Auth::user()->dept_category_id !== 5) {
            $adminFacility = null;
        }

        if (auth::user()->hd === 1) {
            if (auth::user()->dept_category_id === 7) {

                $emailHoD = User::where('active', 1)->where('gm', 1)->where('sg', 1)->get();
            } elseif (auth::user()->dept_category_id === 5) {

                $emailHoD = User::where('active', 1)->where('gm', 1)->where('sg', 1)->get();
            } else {
                $emailHoD = User::where('active', 1)->where('gm', 1)->where('sg', 0)->get();
            }

            $labelEmailHOD = 'Verify by :';
        } else {
            if (auth::user()->dept_category_id === 4) {
                $emailHoD = User::where('active', 1)->where('dept_category_id', 6)->where('hd', 1)->get();
                $labelEmailHOD = 'Produciton Manager :';
            } else {
                if (auth::user()->id === 1175) {
                    $emailHoD = User::where('active', 1)->where('dept_category_id', 7)->where('hd', 1)->get();
                } else {
                    $emailHoD = User::where('active', 1)->where('dept_category_id', auth::user()->dept_category_id)->where('hd', 1)->get();
                }

                $labelEmailHOD = 'Head of Department :';
            }
        }

        $exdo = Initial_Leave::where('user_id', auth::user()->id)->pluck('initial');

        $w = Initial_Leave::where('user_id', auth::user()->id)
            ->whereDATE('expired', '<', date('Y-m-d'))
            ->pluck('initial');

        $minusExdo = Leave::where('user_id', auth::user()->id)->where('leave_category_id', 2)->where('ap_hd', 1)->where('ap_gm', 1)->where('ver_hr', 1)->where('ap_hrd', 1)->pluck('total_day');

        $goingExdo = 0;

        if ($w->sum() >= $minusExdo->sum()) {
            $goingExdo = $w->sum() - $minusExdo->sum();
        }

        $sisaExdo = $exdo->sum() - $minusExdo->sum() - $goingExdo;
        // $sisaExdo = $exdo->sum() - $minusExdo->sum();

        foreach ($emailHoD as $Hod)
            $emailHOD[$Hod->email] = $Hod->first_name . ' ' . $Hod->last_name;
        // dd($init_annual->remainexdo);
        if ($sisaExdo > 0) {
            return View::make('leave.createExdo')->with([
                // 'leave' => $init_annual->remainexdo,
                'leave' => $sisaExdo,
                'department' => $department,
                'labelEmailHOD' => $labelEmailHOD,
                'taken' => $taken->leavetaken,
                'ent_exdo' => $ent_exdo->entitle_exdo,
                'proe' => $proe,
                'pro_category' => $pro_category,
                'pmm' => $pmm,
                'pm_category' => $pm_category,
                'level' => $level,
                'ghea' => $ghea,
                'provinsii' => $provinsii,
                'emailHOD' => $emailHOD,
                'ricky' => $ricky,
                'infiniteApproved' => $infiniteApproved,
                'johnReedel' => $johnReedel,
                'producer'  => $lineProducer,
                'supervisor' => $supervisor,
                'ITcoor'    => $ITcoor,
                'adminFacility' => $adminFacility,
                'assistGM' => $assistGM

            ]);
        } else {
            return Redirect::route('leave/apply');
        }
    }

    public function createEtc()
    {

        $department = dept_category::where(['id' => Auth::user()->dept_category_id])->value('dept_category_name');
        $leave      = [];
        $list_leave = leave_Category::where('id', '>', '2')->orderBy('id', 'asc')->get();
        foreach ($list_leave as $value)
            $leave[$value->leave_category_name] = $value->leave_category_name;

        $pro_category = User::where('users.koor', '=', 1)->where('active', 1)->where('users.dept_category_id', 6)->orderBy('first_name', 'asc')->get();
        foreach ($pro_category as $value)
            $proe[$value->email] = $value->first_name . ' ' . $value->last_name;

        $pm_category = User::where('users.pm', '=', 1)->where('active', 1)->orderBy('first_name', 'asc')->get();
        foreach ($pm_category as $value)
            $pmm[$value->email] = $value->first_name . ' ' . $value->last_name;

        $level_hrd =  User::where('level_hrd', '=', 'Senior Pipeline')->where('dept_category_id', 6)->where('active', 1)->get();
        foreach ($level_hrd as $value)
            $level[$value->email] =  $value->first_name . ' ' . $value->last_name;

        $ricky = null;

        $ghea =   User::select('email')->where('hd', '=', '1')->where('dept_category_id', 6)->pluck('email');

        $infiniteApproved = User::where('active', 1)->where('infiniteApprove', 1)->where('dept_ApprovedHOD', auth::user()->dept_category_id)->value('email');

        $johnReedel = User::select('email')->where('active', 1)->where('hd', 1)->where('dept_category_id', 7)->value('email');

        $lineProducer = User::where('active', 1)->where('producer', 1)->where('dept_category_id', 6)->orderBy('first_name', 'asc')->get();
        foreach ($lineProducer as $value)
            $producer[$value->email] =  $value->first_name . ' ' . $value->last_name;

        $supervisor = User::where('active', 1)->where('spv', 1)->whereIn('dept_category_id', [4, 6])->orderBy('first_name', 'asc')->get();

        $coordinatorIT = User::where('active', 1)->where('koor', 1)->where('dept_category_id', 1)->get();
        foreach ($coordinatorIT as $coorIT)
            $ITcoor[$coorIT->email] = $coorIT->first_name . ' ' . $coorIT->last_name;

        // $provinsi = file_get_contents('http://dev.farizdotid.com/api/daerahindonesia/provinsi');
        $provinsi = file_get_contents('https://ibnux.github.io/data-indonesia/propinsi.json');
        $provinsii = json_decode($provinsi, true);
        // $provinsii = $provinsii['provinsi'];

        $adminFacility = NewUser::where('active', 1)->where('dept_category_id', 5)->where('position', 'Admin Facility')->value('id');

        $assistGM = User::select('email')->where('active', 1)->where('assistGM', 1)->value('email');
        if (auth::user()->assistGM == 1 && auth::user()->hd == 1) {
            $assistGM = $ghea[0];
        }

        if (auth::user()->dept_category_id === 7 and auth::user()->hd === 1) {
            $assistGM = User::select('email')->where('username', 'hr')->where('hr', 1)->value('email');
        }

        if (auth::user()->hd == 1 && auth::user()->dept_category_id == 4 or auth::user()->dept_category_id == 6) {
            $assistGM = $ghea[0];
        }

        if (auth::user()->hd !== 1) {
            $assistGM = "";
        }

        if (Auth::user()->dept_category_id !== 5) {
            $adminFacility = null;
        }

        if (auth::user()->hd === 1) {
            if (auth::user()->dept_category_id === 7) {

                $emailHoD = User::where('active', 1)->where('gm', 1)->where('sg', 1)->get();
            } elseif (auth::user()->dept_category_id === 5) {

                $emailHoD = User::where('active', 1)->where('gm', 1)->where('sg', 1)->get();
            } else {
                $emailHoD = User::where('active', 1)->where('gm', 1)->where('sg', 0)->get();
            }

            $labelEmailHOD = 'Verify by :';
        } else {
            if (auth::user()->dept_category_id === 4) {
                $emailHoD = User::where('active', 1)->where('dept_category_id', 6)->where('hd', 1)->get();
                $labelEmailHOD = 'Produciton Manager :';
            } else {
                if (auth::user()->id === 1175) {
                    $emailHoD = User::where('active', 1)->where('dept_category_id', 7)->where('hd', 1)->get();
                } else {
                    $emailHoD = User::where('active', 1)->where('dept_category_id', auth::user()->dept_category_id)->where('hd', 1)->get();
                }

                $labelEmailHOD = 'Head of Department :';
            }
        }

        foreach ($emailHoD as $Hod)
            $emailHOD[$Hod->email] = $Hod->first_name . ' ' . $Hod->last_name;

        return View::make('leave.createEtc')->with([
            'leave' => $leave,
            'labelEmailHOD' => $labelEmailHOD,
            'department' => $department,
            'proe' => $proe,
            'pro_category' => $pro_category,
            'pmm' => $pmm,
            'pm_category' => $pm_category,
            'level' => $level,
            'ghea' => $ghea,
            'provinsii' => $provinsii,
            'ricky' => $ricky,
            'emailHOD'  => $emailHOD,
            'infiniteApproved' => $infiniteApproved,
            'johnReedel' => $johnReedel,
            'producer'      => $lineProducer,
            'supervisor'    => $supervisor,
            'ITcoor'        => $ITcoor,
            'adminFacility' => $adminFacility,
            'assistGM'     => $assistGM

        ]);
    }

    public function createAdvance()
    {
        $department = dept_category::where(['id' => Auth::user()->dept_category_id])->value('dept_category_name');


        $init_annual = DB::table('users')
            ->leftJoin('initial_leave', 'initial_leave.user_id', '=', 'users.id')
            ->leftJoin('leave_category', 'leave_category.id', '=', 'initial_leave.leave_category_id')

            ->select([
                DB::raw('
            (
                select (
                    select initial_annual from users where id=' . Auth::user()->id . '
                ) - (
                    select COALESCE(sum(total_day), 0) from leave_transaction where user_id=' . Auth::user()->id . ' and leave_category_id=1
                    and ap_hd  = 1
                    and ap_gm  = 1
                    and ver_hr = 1
                    and ap_hrd = 1
                )
            ) as remainannual')
            ])
            ->first();

        $annual = DB::table('users')
            ->leftJoin('initial_leave', 'initial_leave.user_id', '=', 'users.id')
            ->leftJoin('leave_category', 'leave_category.id', '=', 'initial_leave.leave_category_id')
            ->leftJoin('leave_transaction', 'leave_transaction.user_id', '=', 'users.id')

            ->select([
                DB::raw('
            (
                select (
                    select COALESCE(sum(total_day), 0) from leave_transaction where user_id=' . Auth::user()->id . ' and leave_category_id=1
                    and ap_hd  = 1
                    and ap_gm  = 1
                    and ver_hr = 1
                    and ap_hrd = 1
                )
            ) as transactionAnnual')
            ])
            ->first();

        // new rule
        $user = User::find(auth::user()->id);

        $startDate = date_create($user->join_date);
        $endDate = date_create($user->end_date);

        $startYear = date('Y', strtotime($user->join_date));
        $endYear = date('Y', strtotime($user->end_date));

        if (auth::user()->emp_status === "Permanent") {
            $yearEnd = date('Y');
        } else {
            $yearEnd = $endYear;
        }

        $now = date_create();
        $now1 = date_create(date('Y') . '-01-01');
        $now2 = date_create(date('Y') . '-12-31');



        if ($now <= $endDate) {
            $sekarang = $now;
        } else {
            $sekarang = $endDate;
        }



        $daff = date_diff($startDate, $sekarang->modify('+5 day'))->format('%m') + (12 * date_diff($startDate, $sekarang->modify('+5 day'))->format('%y'));

        $daffPermanent = date_diff($now1, $now->modify('+5 day'))->format('%m') + (12 * date_diff($now1, $now->modify('+5 day'))->format('%y'));

        $daffPermanent2 = date_diff($now1, $now2->modify('+5 day'))->format('%m') + (12 * date_diff($now1, $now2->modify('+5 day'))->format('%y'));

        $daffPermanent1 = 12 - $daffPermanent;


        if ($daff <= $annual->transactionAnnual) {
            $newAnnual =  $annual->transactionAnnual;
        } else {
            $newAnnual = $daff;
        }

        $totalAnnual = $newAnnual - $annual->transactionAnnual;

        $totalAnnualPermanent = $user->initial_annual - $annual->transactionAnnual;

        $totalAnnualPermanent1 = $totalAnnualPermanent - $daffPermanent1;

        $forfeited = Forfeited::where('user_id', auth::user()->id)->pluck('countAnnual');
        $forfeitedCounts = ForfeitedCounts::where('user_id', auth::user()->id)->where('status', 1)->pluck('amount');
        $countAmount = $forfeited->sum() - $forfeitedCounts->sum();

        $bla = 0;

        if (auth::user()->forfeitcase === 1) {
            $bla = 0;
        } else {
            if ($countAmount >= 0) {
                $bla = $countAmount;
            } else {
                $bla = 0;
            }
        }

        $getAnnualBalance = null;

        if (auth::user()->emp_status === "Permanent") {
            $getAnnualBalance = $totalAnnualPermanent1 - $bla;
        } else {
            $getAnnualBalance = $totalAnnual - $bla;
        }

        $initAja = 0;

        if (auth::user()->forfeitcase === 1) {
            $initAja = $init_annual->remainannual;
        } else {
            $initAja = $init_annual->remainannual - $bla;
        }

        $taken = DB::table('users')
            ->leftJoin('initial_leave', 'initial_leave.user_id', '=', 'users.id')
            ->leftJoin('leave_category', 'leave_category.id', '=', 'initial_leave.leave_category_id')
            ->leftJoin('leave_transaction', 'leave_transaction.user_id', '=', 'users.id')
            ->select([
                DB::raw('
            (
                (
                    select COALESCE(sum(total_day), 0) from leave_transaction where user_id=' . Auth::user()->id . ' and leave_category_id=1
                    and ap_hd  = 1
                    and ap_gm  = 1
                    and ver_hr = 1
                    and ap_hrd = 1
                )
            ) as leavetaken')
            ])
            ->first();

        $ent_annual = DB::table('users')
            ->leftJoin('initial_leave', 'initial_leave.user_id', '=', 'users.id')
            ->leftJoin('leave_category', 'leave_category.id', '=', 'initial_leave.leave_category_id')
            ->leftJoin('leave_transaction', 'leave_transaction.user_id', '=', 'users.id')
            ->select([
                DB::raw('
            (
                (
                    select COALESCE(sum(initial), 0) from initial_leave where user_id=' . Auth::user()->id . ' and leave_category_id=1
                )
            ) as entitle_ann')
            ])
            ->first();

        $leave_day = DB::table('users')
            ->leftJoin('initial_leave', 'initial_leave.user_id', '=', 'users.id')
            ->leftJoin('leave_category', 'leave_category.id', '=', 'initial_leave.leave_category_id')
            ->leftJoin('leave_transaction', 'leave_transaction.user_id', '=', 'users.id')
            ->select([
                DB::raw('
            (
                select (
                    SELECT datediff(end_leave_date, leave_date) FROM leave_transaction

                )
            ) as remainleaveday')
            ])
            ->first();


        $pro_category = User::where('users.koor', '=', 1)->where('active', 1)->where('users.dept_category_id', 6)->orderBy('first_name', 'asc')->get();
        foreach ($pro_category as $value)
            $proe[$value->email] = $value->first_name . ' ' . $value->last_name;

        $pm_category = User::where('users.pm', '=', 1)->where('active', 1)->orderBy('first_name', 'asc')->get();
        foreach ($pm_category as $value)
            $pmm[$value->email] = $value->first_name . ' ' . $value->last_name;

        $level_hrd =  User::where('level_hrd', '=', 'Senior Pipeline')->where('dept_category_id', 6)->where('active', 1)->get();
        foreach ($level_hrd as $value)
            $level[$value->email] =  $value->first_name . ' ' . $value->last_name;

        $ricky = null;

        $ghea =   User::select('email')->where('hd', '=', '1')->where('dept_category_id', 6)->pluck('email');

        $johnReedel = User::select('email')->where('active', 1)->where('hd', 1)->where('dept_category_id', 7)->value('email');

        $lineProducer = User::where('active', 1)->where('producer', 1)->where('dept_category_id', 6)->orderBy('first_name', 'asc')->get();
        foreach ($lineProducer as $value)
            $producer[$value->email] =  $value->first_name . ' ' . $value->last_name;

        $supervisor = User::where('active', 1)->where('spv', 1)->whereIn('dept_category_id', [4, 6])->orderBy('first_name', 'asc')->get();

        $coordinatorIT = User::where('active', 1)->where('koor', 1)->where('dept_category_id', 1)->get();
        foreach ($coordinatorIT as $coorIT)
            $ITcoor[$coorIT->email] = $coorIT->first_name . ' ' . $coorIT->last_name;

        // $provinsi = file_get_contents('http://dev.farizdotid.com/api/daerahindonesia/provinsi');
        $provinsi = file_get_contents('https://ibnux.github.io/data-indonesia/propinsi.json');
        $provinsii = json_decode($provinsi, true);
        // $provinsii = $provinsii['provinsi'];

        $adminFacility = NewUser::where('active', 1)->where('dept_category_id', 5)->where('position', 'Admin Facility')->value('id');

        $assistGM = User::select('email')->where('active', 1)->where('assistGM', 1)->value('email');
        if (auth::user()->assistGM == 1 && auth::user()->hd == 1) {
            $assistGM = $ghea[0];
        }

        if (auth::user()->dept_category_id === 7 and auth::user()->hd === 1) {
            $assistGM = User::select('email')->where('username', 'hr')->where('hr', 1)->value('email');
        }

        if (auth::user()->hd == 1 && auth::user()->dept_category_id == 4 or auth::user()->dept_category_id == 6) {
            $assistGM = $ghea[0];
        }

        if (auth::user()->hd !== 1) {
            $assistGM = "";
        }

        if (Auth::user()->dept_category_id !== 5) {
            $adminFacility = null;
        }

        if (auth::user()->hd === 1) {
            if (auth::user()->dept_category_id === 7) {

                $emailHoD = User::where('active', 1)->where('gm', 1)->where('sg', 1)->get();
            } elseif (auth::user()->dept_category_id === 5) {

                $emailHoD = User::where('active', 1)->where('gm', 1)->where('sg', 1)->get();
            } else {
                $emailHoD = User::where('active', 1)->where('gm', 1)->where('sg', 0)->get();
            }

            $labelEmailHOD = 'Verify by :';
        } else {
            if (auth::user()->dept_category_id === 4) {
                $emailHoD = User::where('active', 1)->where('dept_category_id', 6)->where('hd', 1)->get();
                $labelEmailHOD = 'Produciton Manager :';
            } else {
                if (auth::user()->id === 1175) {
                    $emailHoD = User::where('active', 1)->where('dept_category_id', 7)->where('hd', 1)->get();
                } else {
                    $emailHoD = User::where('active', 1)->where('dept_category_id', auth::user()->dept_category_id)->where('hd', 1)->get();
                }

                $labelEmailHOD = 'Head of Department :';
            }
        }

        foreach ($emailHoD as $Hod)
            $emailHOD[$Hod->email] = $Hod->first_name . ' ' . $Hod->last_name;

        if ($initAja > 0) {
            return View::make('leave.createAdvance')->with([
                'leave' => $initAja,
                'department' => $department,
                'taken' => $taken->leavetaken,
                'labelEmailHOD' => $labelEmailHOD,
                'ent_annual' => $ent_annual->entitle_ann,
                'proe' => $proe,
                'pro_category' => $pro_category,
                'pmm' => $pmm,
                'pm_category' => $pm_category,
                'level' => $level,
                'ricky' => $ricky,
                'ghea' => $ghea,
                'provinsii' => $provinsii,
                'emailHOD' => $emailHOD,
                'johnReedel' => $johnReedel,
                'producer' => $lineProducer,
                'supervisor' => $supervisor,
                'ITcoor'    => $ITcoor,
                'adminFacility' => $adminFacility,
                'assistGM'     => $assistGM
            ]);
        } else {
            return Redirect::route('createAdvanceLeave');
        }
    }

    public function namaPronvisi($id)
    {
        if ($id === '11') {
            $nama_provins = 'Aceh';
        } elseif ($id === '12') {
            $nama_provins = 'Sumatera Utara';
        } elseif ($id === '13') {
            $nama_provins = 'Sumatera Barat';
        } elseif ($id === '14') {
            $nama_provins = 'Riau';
        } elseif ($id === '15') {
            $nama_provins = 'Jambi';
        } elseif ($id === '16') {
            $nama_provins = 'Sumatera Selatan';
        } elseif ($id === '17') {
            $nama_provins = 'Bengkulu';
        } elseif ($id === '18') {
            $nama_provins = 'Lampung';
        } elseif ($id === '19') {
            $nama_provins = 'Kepulauan Bangka Belitung';
        } elseif ($id === '21') {
            $nama_provins = 'Kepulauan Riau';
        } elseif ($id === '31') {
            $nama_provins = 'Dki Jakarta';
        } elseif ($id === '32') {
            $nama_provins = 'Jawa Barat';
        } elseif ($id === '33') {
            $nama_provins = 'Jawa Tengah';
        } elseif ($id === '34') {
            $nama_provins = 'Di Yogyakarta';
        } elseif ($id === '35') {
            $nama_provins = 'Jawa Timur';
        } elseif ($id === '36') {
            $nama_provins = 'Banten';
        } elseif ($id === '51') {
            $nama_provins = 'Bali';
        } elseif ($id === '52') {
            $nama_provins = 'Nusa Tenggara Barat';
        } elseif ($id === '53') {
            $nama_provins = 'Nusa Tenggara Timur';
        } elseif ($id === '61') {
            $nama_provins = 'Kalimantan Barat';
        } elseif ($id === '62') {
            $nama_provins = 'Kalimantan Tengah';
        } elseif ($id === '63') {
            $nama_provins = 'Kalimantan Selatan';
        } elseif ($id === '64') {
            $nama_provins = 'Kalimantan Timur';
        } elseif ($id === '65') {
            $nama_provins = 'Kalimantan Utara';
        } elseif ($id === '71') {
            $nama_provins = 'Sulawesi Utara';
        } elseif ($id === '72') {
            $nama_provins = 'Sulawesi Tengah';
        } elseif ($id === '73') {
            $nama_provins = 'Sulawesi Selatan';
        } elseif ($id === '74') {
            $nama_provins = 'Sulawesi Tenggara';
        } elseif ($id === '75') {
            $nama_provins = 'Gorontalo';
        } elseif ($id === '76') {
            $nama_provins = 'Sulawesi Barat';
        } elseif ($id === '81') {
            $nama_provins = 'Maluku';
        } elseif ($id === '82') {
            $nama_provins = 'Maluku Utara';
        } elseif ($id === '91') {
            $nama_provins = 'Papua Barat';
        } elseif ($id === '94') {
            $nama_provins = 'Papua';
        } else {
            $nama_provins = $id;
        }

        return $nama_provins;
    }

    public function ruleFormLeave($emailCoor, $emailSPV, $emailPM, $emailProducer)
    {
        $email_koor = null;
        $email_spv = null;
        $email_pm = null;
        $email_producer = null;
        $ap_gm      = 0;
        $date_ap_gm = null;
        $ap_pipeline = 0;
        $date_ap_pipeline = null;
        $ap_spv = 0;
        $date_ap_spv = null;
        $ap_koor = 0;
        $date_ap_koor = null;
        $ap_pm = 0;
        $date_ap_pm = null;
        $ap_producer = 0;
        $date_producer  = null;
        $ap_hd = 0;
        $date_ap_hd = null;
        // ---------------------
        $ap_infinite = 0;
        $date_ap_infinite = null;

        // start rule leave

        // dept_category 1 = IT
        if (auth::user()->dept_category_id === 1) {
            if (auth::user()->hd === 1) {
                // HOD -> GM (mike) -> Ver_hr -> HRD ->
                // HOD -> FA Manager -> GM (ghea) -> Ver_hr -> HRD
                // HOD -> FA Manager -> Ver_hr -> HRD

                $ap_pipeline = 1;
                $date_ap_pipeline = date("Y-m-d");
                $ap_spv = 1;
                $date_ap_spv = date("Y-m-d");
                $ap_koor = 1;
                $date_ap_koor = date("Y-m-d");
                $ap_pm = 1;
                $date_ap_pm = date("Y-m-d");
                // $ap_producer = 1;
                // $date_producer  = date("Y-m-d");
                $ap_hd = 1;
                $date_ap_hd = date("Y-m-d");
                $email_pm = $emailPM;
                $email_producer = $emailProducer;
            } else {
                // Employee -> HOD -> Ver_HR -> HRD
                if (auth::user()->stat_officer === 0) {
                    $ap_koor = 1;
                    $date_ap_koor = date("Y-m-d");
                }

                $ap_pipeline = 1;
                $date_ap_pipeline = date("Y-m-d");
                $ap_spv = 1;
                $date_ap_spv = date("Y-m-d");
                $ap_pm = 1;
                $date_ap_pm = date("Y-m-d");
                $ap_producer = 1;
                $date_producer  = date("Y-m-d");
                $ap_gm = 1;
                $date_ap_gm = date("Y-m-d");
                if (auth::user()->stat_officer === 1) {
                    $email_koor = $emailCoor;
                }
                $email_pm = $emailPM;
            }
        }
        // dept_category 2 = Finance
        elseif (auth::user()->dept_category_id === 2) {
            if (auth::user()->hd === 1) {
                // HOD -> Sow Kim -> Ver_HR -> HRD -> GM (mike)
                $ap_pipeline = 1;
                $date_ap_pipeline = date("Y-m-d");
                $ap_spv = 1;
                $date_ap_spv = date("Y-m-d");
                $ap_koor = 1;
                $date_ap_koor = date("Y-m-d");
                $ap_pm = 1;
                $date_ap_pm = date("Y-m-d");
                // $ap_producer = 1;
                // $date_producer  = date("Y-m-d");
                $ap_hd = 1;
                $date_ap_hd = date("Y-m-d");
                $email_producer = $emailProducer;

                // temp for assist GM
                if (auth::user()->assistGM === 1) {
                    $ap_pipeline = 1;
                    $date_ap_pipeline = date("Y-m-d");
                    $ap_spv = 1;
                    $date_ap_spv = date("Y-m-d");
                    $ap_koor = 1;
                    $date_ap_koor = date("Y-m-d");
                    $ap_pm = 1;
                    $date_ap_pm = date("Y-m-d");
                    $ap_producer = 1;
                    $date_producer  = date("Y-m-d");
                    $ap_hd = 1;
                    $date_ap_hd = date("Y-m-d");
                    $email_producer = $emailProducer;
                }
            } else {
                // Employee -> HOD -> Ver_HR -> HRD
                $ap_pipeline = 1;
                $date_ap_pipeline = date("Y-m-d");
                $ap_spv = 1;
                $date_ap_spv = date("Y-m-d");
                $ap_koor = 1;
                $date_ap_koor = date("Y-m-d");
                $ap_pm = 1;
                $date_ap_pm = date("Y-m-d");
                $ap_producer = 1;
                $date_producer  = date("Y-m-d");
                $ap_gm = 1;
                $date_ap_gm = date("Y-m-d");
                $email_pm = $emailPM;
            }
        }
        // dept_category 3 = HRD
        elseif (auth::user()->dept_category_id === 3) {
            if (auth::user()->hd === 1) {
                // HOD -> Ver_HR -> HRD -> GM (mike)
                $ap_pipeline = 1;
                $date_ap_pipeline = date("Y-m-d");
                $ap_spv = 1;
                $date_ap_spv = date("Y-m-d");
                $ap_koor = 1;
                $date_ap_koor = date("Y-m-d");
                $ap_pm = 1;
                $date_ap_pm = date("Y-m-d");
                // $ap_producer = 1;
                // $date_producer  = date("Y-m-d");
                $ap_hd = 1;
                $date_ap_hd = date("Y-m-d");
                $email_pm = $emailPM;
                $email_producer = $emailProducer;
            } else {
                // Employee -> Ver_HR -> HRD
                $ap_pipeline = 1;
                $date_ap_pipeline = date("Y-m-d");
                $ap_spv = 1;
                $date_ap_spv = date("Y-m-d");
                $ap_koor = 1;
                $date_ap_koor = date("Y-m-d");
                $ap_pm = 1;
                $date_ap_pm = date("Y-m-d");
                $ap_producer = 1;
                $date_producer  = date("Y-m-d");
                $ap_gm = 1;
                $date_ap_gm = date("Y-m-d");
                $email_pm = 'hr.verification@frameworks-studios.com';
                // $email_pm = $emailPM;
                $ap_hd = 1;
                $date_ap_hd = date("Y-m-d");
            }
        }
        //dept Category 4 = Operational ?
        elseif (auth::user()->dept_category_id === 4) {
            $ap_pipeline = 1;
            $date_ap_pipeline = date("Y-m-d");
            $ap_spv = 1;
            $date_ap_spv = date("Y-m-d");
            $ap_koor = 1;
            $date_ap_koor = date("Y-m-d");
            $ap_pm = 1;
            $date_ap_pm = date("Y-m-d");
            $ap_producer = 1;
            $date_producer  = date("Y-m-d");
            // $ap_gm = 1;
            // $date_ap_gm = date("Y-m-d");
            $email_pm = $emailPM;
            $email_producer = $emailProducer;
        }
        //dept_category 5 = Facility
        elseif (auth::user()->dept_category_id === 5) {
            if (auth::user()->hd === 1) {
                // HOD -> John Reedel -> Ver_HR -> HRD -> GM (Mike)
                // HOD -> FA Manager -> GM Ghea -> Ver_HR -> HRD -> GM (Mike)
                // HOD -> FA Manager -> Ver_HR -> HRD
                $ap_pipeline = 1;
                $date_ap_pipeline = date("Y-m-d");
                $ap_spv = 1;
                $date_ap_spv = date("Y-m-d");
                $ap_koor = 1;
                $date_ap_koor = date("Y-m-d");
                $ap_pm = 1;
                $date_ap_pm = date("Y-m-d");
                // $ap_producer = 0;
                // $date_producer  = date("Y-m-d");
                $ap_hd = 1;
                $date_ap_hd = date("Y-m-d");
                // -------------------
                // $ap_infinite = 1;
                // $date_ap_infinite = date('Y-m-d');
                $email_pm = $emailPM;
                $email_producer = $emailProducer;
            } else {
                // Employee -> HOD -> Ver_HR -> HRD
                $ap_pipeline = 1;
                $date_ap_pipeline = date("Y-m-d");
                $ap_spv = 1;
                $date_ap_spv = date("Y-m-d");
                $ap_koor = 1;
                $date_ap_koor = date("Y-m-d");
                $ap_pm = 1;
                $date_ap_pm = date("Y-m-d");
                $ap_producer = 1;
                $date_producer  = date("Y-m-d");
                $ap_gm = 1;
                $date_ap_gm = date("Y-m-d");
                $email_pm = $emailPM;
            }
        }
        //dept category 6 = Production
        elseif (auth::user()->dept_category_id === 6) {
            if (auth::user()->hd === 1) {
                // HOD -> Choonmeng -> Ver_HR -> HRD -> GM (Mike)
                // HOD -> HRD
                $ap_pipeline = 0;
                $date_ap_pipeline = date("Y-m-d");
                $ap_spv = 0;
                $date_ap_spv = date("Y-m-d");
                $ap_koor = 1;
                $date_ap_koor = date("Y-m-d");
                $ap_pm = 1;
                $date_ap_pm = date("Y-m-d");
                // $ap_producer = 1;
                // $date_producer  = date("Y-m-d");
                $ap_hd = 1;
                $date_ap_hd = date("Y-m-d");
                // -----------------------
                $ap_infinite = 1;
                $date_ap_infinite = date('Y-m-d');
                $email_pm = $emailPM;
                $email_producer = $emailProducer;

                if (auth::user()->gm === 1) {
                    $ap_pipeline = 1;
                    $date_ap_pipeline = date("Y-m-d");
                    $ap_spv = 1;
                    $date_ap_spv = date("Y-m-d");
                    $ap_koor = 1;
                    $date_ap_koor = date("Y-m-d");
                    $ap_pm = 1;
                    $date_ap_pm = date("Y-m-d");
                    $ap_producer = 1;
                    $date_producer  = date("Y-m-d");
                    $ap_hd = 1;
                    $date_ap_hd = date("Y-m-d");
                    // -----------------------
                    $ap_infinite = 1;
                    $date_ap_infinite = date('Y-m-d');
                    $email_pm = 'hr.verification@frameworks-studios';
                }
            } else {
                if (auth::user()->level_hrd != '0') {
                    if (auth::user()->level_hrd === 'Junior Pipeline') {
                        // Employee -> Senior Pipeline -> HOD -> Ver_HR -> HRD
                        $ap_pipeline = 0;
                        $date_ap_pipeline = null;
                        $ap_spv = 0;
                        $date_ap_spv = null;
                        $ap_koor = 1;
                        $date_ap_koor = date("Y-m-d");
                        $ap_pm = 1;
                        $date_ap_pm = date("Y-m-d");
                        $ap_producer = 1;
                        $date_producer  = date("Y-m-d");
                        $ap_gm = 1;
                        $date_ap_gm = date("Y-m-d");
                        $email_koor = $emailCoor;
                    } elseif (auth::user()->level_hrd === 'Junior Technical') {
                        // Employee -> Senior Pipeline -> HOD -> Ver_HR -> HRD
                        $ap_pipeline = 0;
                        $date_ap_pipeline = null;
                        $ap_spv = 0;
                        $date_ap_spv = null;
                        $ap_koor = 1;
                        $date_ap_koor = date("Y-m-d");
                        $ap_pm = 1;
                        $date_ap_pm = date("Y-m-d");
                        $ap_producer = 1;
                        $date_producer  = date("Y-m-d");
                        $ap_gm = 1;
                        $date_ap_gm = date("Y-m-d");
                        $email_koor = $emailCoor;
                    } elseif (auth::user()->level_hrd === 'Senior Pipeline') {
                        $ap_pipeline = 0;
                        $date_ap_pipeline = date("Y-m-d");
                        $ap_spv = 1;
                        $date_ap_spv = date("Y-m-d");
                        $ap_koor = 1;
                        $date_ap_koor = date("Y-m-d");
                        $ap_pm = 1;
                        $date_ap_pm = date("Y-m-d");
                        $ap_producer = 1;
                        $date_producer  = date("Y-m-d");
                        $ap_gm = 1;
                        $date_ap_gm = date("Y-m-d");
                        $email_pm = $emailPM;

                        if (auth::user()->position = "Head Of Technology") {
                            $ap_pipeline = 1;
                            $date_ap_pipeline = date("Y-m-d");
                            $ap_spv = 1;
                            $date_ap_spv = date("Y-m-d");
                            $ap_koor = 1;
                            $date_ap_koor = date("Y-m-d");
                            $ap_pm = 1;
                            $date_ap_pm = date("Y-m-d");
                            $ap_producer = 1;
                            $date_producer  = date("Y-m-d");
                            $ap_hd = 1;
                            $date_ap_hd = date("Y-m-d");
                            $ap_gm = 0;
                            $date_ap_gm = null;
                            $email_pm = $emailPM;
                        }
                    } elseif (auth::user()->level_hrd === 'Senior Technical') {
                        $ap_pipeline = 0;
                        $date_ap_pipeline = date("Y-m-d");
                        $ap_spv = 1;
                        $date_ap_spv = date("Y-m-d");
                        $ap_koor = 1;
                        $date_ap_koor = date("Y-m-d");
                        $ap_pm = 1;
                        $date_ap_pm = date("Y-m-d");
                        $ap_producer = 1;
                        $date_producer  = date("Y-m-d");
                        $ap_gm = 1;
                        $date_ap_gm = date("Y-m-d");
                        $email_pm = $emailPM;
                    }
                } else {
                    if (auth::user()->producer === 1) {
                        $ap_pipeline = 0;
                        $date_ap_pipeline = date("Y-m-d");
                        $ap_spv = 1;
                        $date_ap_spv = date("Y-m-d");
                        $ap_koor = 1;
                        $date_ap_koor = date("Y-m-d");
                        $ap_pm = 1;
                        $date_ap_pm = date("Y-m-d");
                        $ap_producer = 1;
                        $date_producer  = date("Y-m-d");
                        $ap_gm = 1;
                        $date_ap_gm = date("Y-m-d");
                        $email_pm = $emailPM;
                    } elseif (auth::user()->pm === 1) {
                        $ap_pipeline = 0;
                        $date_ap_pipeline = date("Y-m-d");
                        $ap_spv = 1;
                        $date_ap_spv = date("Y-m-d");
                        $ap_koor = 1;
                        $date_ap_koor = date("Y-m-d");
                        $ap_pm = 1;
                        $date_ap_pm = date("Y-m-d");
                        $ap_producer = 1;
                        $date_producer  = date("Y-m-d");
                        $ap_gm = 1;
                        $date_ap_gm = date("Y-m-d");
                        $email_pm = $emailPM;
                    } elseif (auth::user()->koor === 1) {
                        if (auth::user()->lineGM === 1) {
                            $ap_pipeline = 0;
                            $date_ap_pipeline = date("Y-m-d");
                            $ap_spv = 1;
                            $date_ap_spv = date("Y-m-d");
                            $ap_pm = 1;
                            $date_ap_pm = date("Y-m-d");
                            $ap_koor = 1;
                            $date_ap_koor = date("Y-m-d");
                            $ap_gm = 1;
                            $date_ap_gm = date("Y-m-d");
                            $email_pm = $emailPM;
                            $email_producer = $emailProducer;
                        } else {
                            $ap_pipeline = 0;
                            $date_ap_pipeline = date("Y-m-d");
                            $ap_spv = 1;
                            $date_ap_spv = date("Y-m-d");
                            $ap_koor = 1;
                            $date_ap_koor = date("Y-m-d");
                            $ap_gm = 1;
                            $date_ap_gm = date("Y-m-d");
                            $email_pm = $emailPM;
                            $email_producer = $emailProducer;
                        }
                    } elseif (auth::user()->spv === 1) {
                        $ap_pipeline = 0;
                        $date_ap_pipeline = date("Y-m-d");
                        $ap_spv = 1;
                        $date_ap_spv = date("Y-m-d");
                        $ap_koor = 1;
                        $date_ap_koor = date("Y-m-d");
                        $ap_gm = 1;
                        $date_ap_gm = date("Y-m-d");
                        $email_producer = $emailProducer;
                        $email_pm = $emailPM;
                    } else {
                        $ap_pipeline = 0;
                        $date_ap_pipeline = date("Y-m-d");
                        $ap_gm = 1;
                        $date_ap_gm = date("Y-m-d");
                        $email_koor = $emailCoor;
                        $email_spv = $emailSPV;
                        $email_pm = $emailPM;
                    }
                }
            }
        }
        // dept category 7 = Production LS
        elseif (auth::user()->dept_category_id === 7) {
            if (auth::user()->hd === 1) {
                // HOD -> Ver_HR -> HRD -> GM (mike)
                $ap_pipeline = 1;
                $date_ap_pipeline = date("Y-m-d");
                $ap_spv = 1;
                $date_ap_spv = date("Y-m-d");
                $ap_koor = 1;
                $date_ap_koor = date("Y-m-d");
                $ap_pm = 1;
                $date_ap_pm = date("Y-m-d");
                $ap_producer = 1;
                $date_producer  = date("Y-m-d");
                $ap_hd = 1;
                $date_ap_hd = date("Y-m-d");
                $ap_gm = 1;
                $date_ap_gm = date("Y-m-d");
                $email_pm = $emailPM;
                $email_producer = $emailProducer;
            } else {
                // Employee -> HOD-> Ver_HR -> HRD
                $ap_pipeline = 1;
                $date_ap_pipeline = date("Y-m-d");
                $ap_spv = 1;
                $date_ap_spv = date("Y-m-d");
                $ap_koor = 1;
                $date_ap_koor = date("Y-m-d");
                $ap_pm = 1;
                $date_ap_pm = date("Y-m-d");
                $ap_producer = 1;
                $date_producer  = date("Y-m-d");
                $ap_gm = 1;
                $date_ap_gm = date("Y-m-d");
                $email_pm = $emailPM;
            }
        }
        // dept category 8 = General
        elseif (auth::user()->dept_category_id === 8) {
            if (auth::user()->hd === 1) {
                // HOD -> Ver_HR -> HRD
                $ap_pipeline = 1;
                $date_ap_pipeline = date("Y-m-d");
                $ap_spv = 1;
                $date_ap_spv = date("Y-m-d");
                $ap_koor = 1;
                $date_ap_koor = date("Y-m-d");
                $ap_pm = 1;
                $date_ap_pm = date("Y-m-d");
                // $ap_producer = 1;
                // $date_producer  = date("Y-m-d");
                $ap_hd = 1;
                $date_ap_hd = date("Y-m-d");
                $email_pm = $emailPM;
                $email_producer = $emailProducer;
            } else {
                // Employee -> HOD -> Ver_HR -> HRD
                $ap_pipeline = 1;
                $date_ap_pipeline = date("Y-m-d");
                $ap_spv = 1;
                $date_ap_spv = date("Y-m-d");
                $ap_koor = 1;
                $date_ap_koor = date("Y-m-d");
                $ap_pm = 1;
                $date_ap_pm = date("Y-m-d");
                $ap_producer = 1;
                $date_producer  = date("Y-m-d");
                $ap_gm = 1;
                $date_ap_gm = date("Y-m-d");
                $email_pm = $emailPM;
            }
        }
        // dept category 9 = Management ?
        elseif (auth::user()->dept_category_id === 9) {
            $ap_pipeline = 1;
            $date_ap_pipeline = date("Y-m-d");
            $ap_spv = 1;
            $date_ap_spv = date("Y-m-d");
            $ap_koor = 1;
            $date_ap_koor = date("Y-m-d");
            $ap_pm = 1;
            $date_ap_pm = date("Y-m-d");
            $ap_producer = 1;
            $date_producer  = date("Y-m-d");
            $ap_hd = 1;
            $date_ap_hd = date("Y-m-d");
            $ap_gm = 1;
            $date_ap_gm = date("Y-m-d");
        }


        $return = [
            "email_koor"        => $email_koor,
            "email_spv"         => $email_spv,
            "email_pm"          => $email_pm,
            "email_producer"    => $email_producer,
            "ap_gm"             => $ap_gm,
            "date_ap_gm"        => $date_ap_gm,
            "ap_pipeline"        => $ap_pipeline,
            "date_ap_pipeline"  => $date_ap_pipeline,
            "ap_spv"            => $ap_spv,
            "date_ap_spv"       => $date_ap_spv,
            "ap_koor"           => $ap_koor,
            "date_ap_koor"      => $date_ap_koor,
            "ap_pm"             => $ap_pm,
            "date_ap_pm"        => $date_ap_pm,
            "ap_producer"       => $ap_producer,
            "date_producer"     => $date_producer,
            "ap_hd"             => $ap_hd,
            "date_ap_hd"        => $date_ap_hd,
            "ap_infinite"       => $ap_infinite,
            "date_ap_infinite"  => $date_ap_infinite
        ];

        return $return;
    }

    public function storeLeave(Request $request)
    {
        $init_annual = DB::table('users')
            ->leftJoin('initial_leave', 'initial_leave.user_id', '=', 'users.id')
            ->leftJoin('leave_category', 'leave_category.id', '=', 'initial_leave.leave_category_id')
            ->leftJoin('leave_transaction', 'leave_transaction.user_id', '=', 'users.id')
            ->select([
                DB::raw('
            (
                select (
                    select initial_annual from users where id=' . Auth::user()->id . '
                ) - (
                    select COALESCE(sum(total_day), 0) from leave_transaction where user_id=' . Auth::user()->id . ' and leave_category_id=1
                    and ap_hd  = 1
                    and ap_gm  = 1
                    and ver_hr = 1
                    and ap_hrd = 1
                )
            ) as remainannual')
            ])
            ->first();

        $taken = DB::table('users')
            ->leftJoin('initial_leave', 'initial_leave.user_id', '=', 'users.id')
            ->leftJoin('leave_category', 'leave_category.id', '=', 'initial_leave.leave_category_id')
            ->leftJoin('leave_transaction', 'leave_transaction.user_id', '=', 'users.id')
            ->select([
                DB::raw('
            (
                (
                    select COALESCE(sum(total_day), 0) from leave_transaction where user_id=' . Auth::user()->id . ' and leave_category_id=1
                    and ap_hd  = 1
                    and ap_gm  = 1
                    and ver_hr = 1
                    and ap_hrd = 1
                )
            ) as leavetaken')
            ])
            ->first();

        $ent_annual = DB::table('users')
            ->leftJoin('initial_leave', 'initial_leave.user_id', '=', 'users.id')
            ->leftJoin('leave_category', 'leave_category.id', '=', 'initial_leave.leave_category_id')
            ->leftJoin('leave_transaction', 'leave_transaction.user_id', '=', 'users.id')
            ->select([
                DB::raw('
            (
                (
                    select initial_annual from users where id=' . Auth::user()->id . '
                )
            ) as entitle_ann')
            ])
            ->first();

        $leave_d = DB::table('leave_transaction')
            ->whereRaw('Datediff(end_leave_date, leave_date)')
            ->first();

        $emailCoor = $request->input('sendto');
        $emailSPV = $request->input('sendtoSPV');
        $emailPM = $request->input('sendtoPM');
        $emailProducer = $request->input('sendtoProducer');

        $ruleForm = $this->ruleFormLeave($emailCoor, $emailSPV, $emailPM, $emailProducer);

        $email_koor = $ruleForm['email_koor'];
        $email_spv = $ruleForm['email_spv'];
        $email_pm = $ruleForm['email_pm'];
        $email_producer = $ruleForm['email_producer'];
        $ap_gm      = $ruleForm['ap_gm'];
        $date_ap_gm = $ruleForm['date_ap_gm'];
        $ap_pipeline = $ruleForm['ap_pipeline'];
        $date_ap_pipeline = $ruleForm['date_ap_pipeline'];
        $ap_spv = $ruleForm['ap_spv'];
        $date_ap_spv = $ruleForm['date_ap_spv'];
        $ap_koor = $ruleForm['ap_koor'];
        $date_ap_koor = $ruleForm['date_ap_koor'];
        $ap_pm = $ruleForm['ap_pm'];
        $date_ap_pm = $ruleForm['date_ap_pm'];
        $ap_producer = $ruleForm['ap_producer'];
        $date_producer  = $ruleForm['date_producer'];
        $ap_hd = $ruleForm['ap_hd'];
        $date_ap_hd = $ruleForm['date_ap_hd'];
        // ---------------------
        $ap_infinite = $ruleForm['ap_infinite'];
        $date_ap_infinite = $ruleForm['date_ap_infinite'];

        ////////////////////////////////////////////
        $tahun_libur = db::table('view_off_year')->whereDate('date', '>=', $request->input('leave_date'))->whereDate('date', '<=', $request->input('end_leave_date'))->get();
        $tahuan = array();
        foreach ($tahun_libur as $key => $tahuan_value) {
            $tahuan[] = $tahuan_value->date;
        }

        $awal_cuti = strtotime($request->input('leave_date'));
        $akhir_cuti = strtotime($request->input('end_leave_date'));
        $haricuti = array();
        for ($i = $awal_cuti; $i <= $akhir_cuti; $i += (60 * 60 * 24)) {
            if (date('w', $i) !== '0' && date('w', $i) !== '6') {

                $haricuti[] = $i;
            }
        }

        $start_leaved       = $request->input('leave_date');
        $end_leaved         = $request->input('end_leave_date');
        $back_work_leaved   = $request->input('back_work');

        if ($start_leaved <= $end_leaved) {
            $get_end_leaved = $end_leaved;
        } else {
            $get_end_leaved = null;
        }

        if ($back_work_leaved > $end_leaved) {
            $jumlah_cuti = count($haricuti) - count($tahuan);
        } elseif ($back_work_leaved = $end_leaved) {
            $jumlah_cuti = count($haricuti) - count($tahuan) - 0.5;
        } elseif ($back_work_leaved < $end_leaved) {
            $jumlah_cuti = 0;
        }

        if ($back_work_leaved >= $end_leaved) {
            $get_bacl_work_leaved = $back_work_leaved;
        } else {
            $get_bacl_work_leaved = null;
        }

        $remaining = $request->input('entitlement') - $jumlah_cuti;
        //////////////////////////////////////////////////////

        $nama_provins = $this->namaPronvisi($request->input('nama_provin'));

        $forfeited = Forfeited::where('user_id', auth::user()->id)->pluck('countAnnual')->sum();
        $forfeitedCount = ForfeitedCounts::where('user_id', auth::user()->id)->where('status', 1)->pluck('amount')->sum();

        $rules = [
            'leave_date'        => 'required|date',
            'end_leave_date'    => 'required|date',
            'back_work'         => 'required|date',
            'reason'            => 'required|max:50'
        ];

        $data = [
            'user_id'                    => Auth::user()->id,
            'leave_category_id'          => '1',
            'request_by'                 => Auth::user()->first_name . ' ' . Auth::user()->last_name,
            'request_nik'                => Auth::user()->nik,
            'request_position'           => Auth::user()->position,
            'request_join_date'          => Auth::user()->join_date,
            'request_dept_category_name' => dept_category::where(['id' => Auth::user()->dept_category_id])->value('dept_category_name'),
            'period'                     => date('Y'),
            'leave_date'                 => $request->input('leave_date'),
            'end_leave_date'             => $get_end_leaved,
            'back_work'                  => $get_bacl_work_leaved,
            'total_day'                  => $jumlah_cuti,
            'taken'                      => $taken->leavetaken,
            'entitlement'                => $ent_annual->entitle_ann,
            'pending'                    => $init_annual->remainannual,
            'remain'                     => $remaining,
            'ap_hd'                      => $ap_hd,
            'ap_gm'                      => $ap_gm,
            'date_ap_hd'                 => $date_ap_hd,
            'date_ap_gm'                 => $date_ap_gm,
            'ver_hr'                     => '0',
            'ap_koor'                    => $ap_koor,
            'ap_spv'                     => $ap_spv,
            'ap_pm'                     => $ap_pm,
            'ap_producer'               => $ap_producer,
            'ap_pipeline'               => $ap_pipeline,
            'ap_Infinite'               => $ap_infinite,
            'date_ap_Infinite'          => $date_ap_infinite,
            'date_ap_koor'              => $date_ap_koor,
            'date_ap_spv'               => $date_ap_spv,
            'date_ap_pm'                => $date_ap_pm,
            'date_ap_pipeline'          => $date_ap_pipeline,
            'email_koor'                => $email_koor,
            'email_spv'                 => $email_spv,
            'email_pm'                  => $email_pm,
            'email_producer'            => $email_producer,
            'reason_leave'              => strtolower($request->input('reason')),
            'r_departure'               => $nama_provins,
            'r_after_leaving'           => $request->input('nama_city'),
            'plan_leave'                => $request->input('rencana'),
            'agreement'                 => $request->input('agree'),
            'resendmail'                => 2,
        ];
        // dd($data);
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Redirect::route('leave/create')
                ->withErrors($validator)
                ->withInput();
        } else {
            if ($get_bacl_work_leaved != null) {
                if ($get_end_leaved != null) {
                    if ($remaining < 0) {
                        Session::flash('getError', Lang::get('messages.annual_balance_error', ['name' => auth::user()->first_name . ' ' . auth::user()->last_name]));
                        return back();
                    } else {
                        $leave = Leave::where('user_id', auth::user()->id)->where('leave_date', $request->input('leave_date'))->where('end_leave_date', $get_end_leaved)->latest()->first();

                        if (!empty($leave)) {
                            Session::flash('getError', Lang::get('messages.data_custom', ['data' => 'Sorry, you have applied for leave on that date']));
                            return Redirect::route('leave/create');
                        }

                        Leave::insert($data);

                        $lastLeaved = Leave::where('user_id', auth::user()->id)->where('leave_category_id', 1)->latest()->first();

                        $forfeited = [
                            'user_id'   => auth::user()->id,
                            'leave_id'  => $lastLeaved->id,
                            'amount'    => $jumlah_cuti,
                            'status'    => 0,
                        ];

                        if (auth::user()->dept_category_id == 5) {
                            $adminFacility = $request->input('adminFacility');
                            $this->sendNotifAdminFacility($adminFacility, auth::user()->id);
                        }

                        if ($forfeitedCount < $forfeited) {
                            ForfeitedCounts::insert($forfeited);
                        }

                        Session::flash('message', Lang::get('messages.data_inserted', ['data' => 'Data Leave Transaction']));
                        if (auth::user()->hd == 1) {
                            $this->sendFormLeaveHoD();
                        } else {
                            $this->sendEmail3();
                        }
                        return Redirect::route('leave/transaction');
                    }
                } else {
                    Session::flash('getError', Lang::get('messages.annual_date_error', ['name' => auth::user()->first_name . ' ' . auth::user()->last_name]));
                    return back();
                }
            } else {
                Session::flash('getError', Lang::get('messages.annual_date_error', ['name' => auth::user()->first_name . ' ' . auth::user()->last_name]));
                return back();
            }
        }
    }

    public function sendFormLeaveHoD()
    {
        $select = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()
            ->orderBy('leave_transaction.id', 'des')
            ->where('leave_transaction.user_id', auth::user()->id)
            ->first();

        $subject = "Approval Request Leave Application - " . auth::user()->first_name . " " . auth::user()->last_name;

        if (!empty($select->email_pm)) {
            $email = $select->email_pm;
        } else {
            $email = $select->email_producer;
        }

        if (!empty($email)) {
            Mail::send('email.appMail', ['select' => $select, 'email' => $email], function ($message) use ($select, $subject, $email) {
                $message->to($email)->subject($subject);
                $message->from('wis_system@frameworks-studios.com', 'WIS');
            });
        } else {
            Mail::send('email.leaveMail', ['select' => $select, 'email' => $email], function ($message) use ($select, $subject, $email) {
                $message->to('hr.verification@frameworks-studios.com')->subject($subject);
                $message->from('wis_system@frameworks-studios.com', 'WIS');
            });
        }
    }

    public function sendEmail3()
    {
        $select = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()
            ->orderBy('leave_transaction.id', 'des')
            ->where('leave_transaction.user_id', auth::user()->id)
            ->first();

        $subject = 'Approval Request Leave Application - ' . $select->request_by . '';

        if ($select->email_koor != null) {
            Mail::send('email.appMail', ['select' => $select], function ($message) use ($select, $subject) {

                $message->to($select->email_koor)->subject($subject);
                $message->from('wis_system@frameworks-studios.com', 'WIS');
            });
        } else {

            if ($select->email_spv != null) {
                Mail::send('email.appMail', ['select' => $select], function ($message) use ($select, $subject) {

                    $message->to($select->email_spv)->subject($subject);
                    $message->from('wis_system@frameworks-studios.com', 'WIS');
                });
            } else {

                if ($select->email_pm != null) {
                    // untuk Officer sent to Head Departement
                    if (auth::user()->dept_category_id === 3) {
                        Mail::send('email.leaveMail', ['select' => $select], function ($message) use ($select, $subject) {

                            $message->to('hr.verification@frameworks-studios.com')->subject($subject);
                            $message->from('wis_system@frameworks-studios.com', 'WIS');
                        });
                    } else {
                        Mail::send('email.appMail', ['select' => $select], function ($message) use ($select, $subject) {

                            $message->to($select->email_pm)->subject($subject);
                            $message->from('wis_system@frameworks-studios.com', 'WIS');
                        });
                    }
                } else {
                    // untuk Head Department
                    Mail::send('email.leaveMail', ['select' => $select], function ($message) use ($select, $subject) {

                        $message->to('hr.verification@frameworks-studios.com')->subject($subject);
                        $message->from('wis_system@frameworks-studios.com', 'WIS');
                    });
                }
            }
        }
    }

    public function sendNotifAdminFacility($param, $id)
    {
        $user = Leave::where('user_id', $id)->orderBy('id', 'desc')->first();

        $adminFacility = User::find($param);

        $subject = 'Someone Request Leave Application - ' . $user->request_by . '';

        Mail::send('email.Notifikasi.Leave.adminFacility', ['adminFacility' => $adminFacility], function ($message) use ($adminFacility, $subject) {
            $message->to($adminFacility->email)->subject($subject);
            $message->from('wis_system@frameworks-studios.com', 'WIS');
        });
    }

    public function sendmail2()
    {
        $select = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()
            ->orderBy('leave_transaction.id', 'des')
            ->first();

        $subject = 'Verify Request Leave Application - ' . $select->request_by . '';

        if (auth::user()->dept_category_id === 1) {
            if (auth::user()->hd === 1) {
                $gm = NewUser::joinDeptCategory()
                    ->where('users.gm', '=', 1)
                    ->first();

                Mail::send('email.leaveMail', ['select' => $select], function ($message) use ($gm, $subject) {

                    $message->to($gm->email)->subject($subject);
                    $message->from('wis_system@frameworks-studios.com', 'WIS');
                });
            } else {
                $IT = NewUser::joinDeptCategory()
                    ->where('users.hd', '=', 1)
                    ->where('users.dept_category_id', 1)
                    ->first();

                Mail::send('email.leaveMail', ['select' => $select], function ($message) use ($IT, $subject) {

                    $message->to($IT->email)->subject($subject);
                    $message->from('wis_system@frameworks-studios.com', 'WIS');
                });
            }
        } elseif (auth::user()->dept_category_id === 2) {
            if (auth::user()->hd === 1) {
                $gm = NewUser::joinDeptCategory()
                    ->where('users.gm', '=', 1)
                    ->first();

                Mail::send('email.leaveMail', ['select' => $select], function ($message) use ($gm, $subject) {

                    $message->to($gm->email)->subject($subject);
                    $message->from('wis_system@frameworks-studios.com', 'WIS');
                });
            } else {
                $acc = NewUser::joinDeptCategory()
                    ->where('users.hd', '=', 1)
                    ->where('users.dept_category_id', 2)
                    ->first();

                Mail::send('email.leaveMail', ['select' => $select], function ($message) use ($acc, $subject) {

                    $message->to($acc->email)->subject($subject);
                    $message->from('wis_system@frameworks-studios.com', 'WIS');
                });
            }
        } elseif (auth::user()->dept_category_id === 3) {
            if (auth::user()->hd === 1) {
                $gm = NewUser::joinDeptCategory()
                    ->where('users.gm', '=', 1)
                    ->first();

                Mail::send('email.leaveMail', ['select' => $select], function ($message) use ($gm, $subject) {

                    $message->to($gm->email)->subject($subject);
                    $message->from('wis_system@frameworks-studios.com', 'WIS');
                });
            } else {
                $hr = NewUser::joinDeptCategory()
                    ->where('users.hd', '=', 1)
                    ->where('users.dept_category_id', 3)
                    ->first();

                Mail::send('email.leaveMail', ['select' => $select], function ($message) use ($hr, $subject) {

                    $message->to($hr->email)->subject($subject);
                    $message->from('wis_system@frameworks-studios.com', 'WIS');
                });
            }
        } elseif (auth::user()->dept_category_id === 4) {
            $operation = NewUser::joinDeptCategory()
                ->where('users.hd', '=', 1)
                ->where('users.dept_category_id', 6)
                ->first();

            Mail::send('email.leaveMail', ['select' => $select], function ($message) use ($operation, $subject) {

                $message->to($operation->email)->subject($subject);
                $message->from('wis_system@frameworks-studios.com', 'WIS');
            });
        } elseif (auth::user()->dept_category_id === 5) {
            if (auth::user()->hd === 1) {
                $gm = NewUser::joinDeptCategory()
                    ->where('users.gm', '=', 1)
                    ->first();

                Mail::send('email.leaveMail', ['select' => $select], function ($message) use ($gm, $subject) {

                    $message->to($gm->email)->subject($subject);
                    $message->from('wis_system@frameworks-studios.com', 'WIS');
                });
            } else {
                $facility = NewUser::joinDeptCategory()
                    ->where('users.hd', '=', 1)
                    ->where('users.dept_category_id', 6)
                    ->first();

                Mail::send('email.leaveMail', ['select' => $select], function ($message) use ($facility, $subject) {

                    $message->to($facility->email)->subject($subject);
                    $message->from('wis_system@frameworks-studios.com', 'WIS');
                });
            }
        } elseif (auth::user()->dept_category_id === 6) {
            if (auth::user()->hd === 1) {
                $gm = NewUser::joinDeptCategory()
                    ->where('users.gm', '=', 1)
                    ->first();

                Mail::send('email.leaveMail', ['select' => $select], function ($message) use ($gm, $subject) {

                    $message->to($gm->email)->subject($subject);
                    $message->from('wis_system@frameworks-studios.com', 'WIS');
                });
            } else {
                if (auth::user()->level_hrd != '0') {
                    if (auth::user()->level_hrd === 'Junior Pipeline') {
                        $juniorPipeline = NewUser::joinDeptCategory()
                            ->where('users.dept_category_id', 6)
                            ->where('level_hrd', '=', 'Senior Pipeline')
                            ->first();

                        Mail::send('email.leaveMail', ['select' => $select], function ($message) use ($juniorPipeline, $subject) {

                            $message->to($juniorPipeline->email)->subject($subject);
                            $message->from('wis_system@frameworks-studios.com', 'WIS');
                        });
                    } elseif (auth::user()->level_hrd === 'Technical Director' or auth::user()->level_hrd === 'Senior Technical' or auth::user()->level_hrd === 'Senior Pipeline') {
                        $Pipeline = NewUser::joinDeptCategory()
                            ->where('users.hd', '=', 1)
                            ->where('users.dept_category_id', 6)
                            ->first();

                        Mail::send('email.leaveMail', ['select' => $select], function ($message) use ($Pipeline, $subject) {

                            $message->to($Pipeline->email)->subject($subject);
                            $message->from('wis_system@frameworks-studios.com', 'WIS');
                        });
                    }
                } else {
                    if (auth::user()->producer === 1) {
                        $producer = NewUser::joinDeptCategory()
                            ->where('users.hd', '=', 1)
                            ->where('users.dept_category_id', 6)
                            ->first();

                        Mail::send('email.leaveMail', ['select' => $select], function ($message) use ($producer, $subject) {

                            $message->to($producer->email)->subject($subject);
                            $message->from('wis_system@frameworks-studios.com', 'WIS');
                        });
                    } elseif (auth::user()->pm === 1) {
                        $pm = NewUser::joinDeptCategory()
                            ->where('users.hd', '=', 1)
                            ->where('users.dept_category_id', 6)
                            ->first();

                        Mail::send('email.leaveMail', ['select' => $select], function ($message) use ($pm, $subject) {

                            $message->to($pm->email)->subject($subject);
                            $message->from('wis_system@frameworks-studios.com', 'WIS');
                        });
                    } elseif (auth::user()->koor === 1) {
                        $koor = NewUser::joinDeptCategory()
                            ->where('users.pm', '=', 1)
                            ->where('users.dept_category_id', 6)
                            ->first();

                        Mail::send('email.leaveMail', ['select' => $select], function ($message) use ($koor, $subject) {

                            $message->to($koor->email)->subject($subject);
                            $message->from('wis_system@frameworks-studios.com', 'WIS');
                        });
                    } elseif (auth::user()->spv === 1) {
                        $spv = NewUser::joinDeptCategory()
                            ->where('users.koor', '=', 1)
                            ->where('users.dept_category_id', 6)
                            ->first();

                        Mail::send('email.leaveMail', ['select' => $select], function ($message) use ($spv, $subject) {

                            $message->to($spv->email)->subject($subject);
                            $message->from('wis_system@frameworks-studios.com', 'WIS');
                        });
                    } else {
                        $karyawanProduction = NewUser::joinDeptCategory()
                            ->where('users.koor', '=', 1)
                            ->where('users.dept_category_id', 6)
                            ->first();

                        Mail::send('email.leaveMail', ['select' => $select], function ($message) use ($karyawanProduction, $subject) {

                            $message->to($karyawanProduction->email)->subject($subject);
                            $message->from('wis_system@frameworks-studios.com', 'WIS');
                        });
                    }
                }
            }
        } elseif (auth::user()->dept_category_id === 7) {
            Mail::send('email.leaveMail', ['select' => $select], function ($message) use ($subject) {
                $message->to('hr.verification@frameworks-studios.com')->subject($subject);
                $message->from('wis_system@frameworks-studios.com', 'WIS');
            });
        } elseif (auth::user()->dept_category_id === 8) {
            if (auth::user()->hd === 1) {
                Mail::send('email.leaveMail', ['select' => $select], function ($message) use ($subject) {
                    $message->to('hr.verification@frameworks-studios.com')->subject($subject);
                    $message->from('wis_system@frameworks-studios.com', 'WIS');
                });
            } else {
                $pr = NewUser::joinDeptCategory()
                    ->where('users.hd', '=', 1)
                    ->where('users.dept_category_id', 8)
                    ->first();

                Mail::send('email.leaveMail', ['select' => $select], function ($message) use ($pr, $subject) {

                    $message->to($pr->email)->subject($subject);
                    $message->from('wis_system@frameworks-studios.com', 'WIS');
                });
            }
        } elseif (auth::user()->dept_category_id === 9) {
            Mail::send('email.leaveMail', ['select' => $select], function ($message) use ($subject) {
                $message->to('hr.verification@frameworks-studios.com')->subject($subject);
                $message->from('wis_system@frameworks-studios.com', 'WIS');
            });
        }
    }

    public function storeLeaveExdo(Request $request)
    {

        $init_annual = DB::table('users')
            ->leftJoin('initial_leave', 'initial_leave.user_id', '=', 'users.id')
            ->leftJoin('leave_category', 'leave_category.id', '=', 'initial_leave.leave_category_id')
            ->leftJoin('leave_transaction', 'leave_transaction.user_id', '=', 'users.id')
            ->select([
                DB::raw('
            (
                select (
                    select COALESCE(sum(initial), 0) from initial_leave where user_id=' . Auth::user()->id . ' and leave_category_id=2
                ) - (
                    select COALESCE(sum(total_day), 0) from leave_transaction where user_id=' . Auth::user()->id . ' and leave_category_id=2
                    and ap_hd  = 1
                    and ap_gm  = 1
                    and ver_hr = 1
                    and ap_hrd = 1
                )
            ) as remainexdo')
            ])
            ->first();

        $taken = DB::table('users')
            ->leftJoin('initial_leave', 'initial_leave.user_id', '=', 'users.id')
            ->leftJoin('leave_category', 'leave_category.id', '=', 'initial_leave.leave_category_id')
            ->leftJoin('leave_transaction', 'leave_transaction.user_id', '=', 'users.id')
            ->select([
                DB::raw('
            (
                (
                    select COALESCE(sum(total_day), 0) from leave_transaction where user_id=' . Auth::user()->id . ' and leave_category_id=2
                    and ap_hd  = 1
                    and ap_gm  = 1
                    and ver_hr = 1
                    and ap_hrd = 1
                )
            ) as leavetaken')
            ])
            ->first();

        $ent_exdo = DB::table('users')
            ->leftJoin('initial_leave', 'initial_leave.user_id', '=', 'users.id')
            ->leftJoin('leave_category', 'leave_category.id', '=', 'initial_leave.leave_category_id')
            ->leftJoin('leave_transaction', 'leave_transaction.user_id', '=', 'users.id')
            ->select([
                DB::raw('
            (
                (
                    select COALESCE(sum(initial), 0) from initial_leave where user_id=' . Auth::user()->id . ' and leave_category_id=2
                )
            ) as entitle_exdo')
            ])
            ->first();

        $emailCoor = $request->input('sendto');
        $emailSPV = $request->input('sendtoSPV');
        $emailPM = $request->input('sendtoPM');
        $emailProducer = $request->input('sendtoProducer');

        $ruleForm = $this->ruleFormLeave($emailCoor, $emailSPV, $emailPM, $emailProducer);

        $email_koor = $ruleForm['email_koor'];
        $email_spv = $ruleForm['email_spv'];
        $email_pm = $ruleForm['email_pm'];
        $email_producer = $ruleForm['email_producer'];
        $ap_gm      = $ruleForm['ap_gm'];
        $date_ap_gm = $ruleForm['date_ap_gm'];
        $ap_pipeline = $ruleForm['ap_pipeline'];
        $date_ap_pipeline = $ruleForm['date_ap_pipeline'];
        $ap_spv = $ruleForm['ap_spv'];
        $date_ap_spv = $ruleForm['date_ap_spv'];
        $ap_koor = $ruleForm['ap_koor'];
        $date_ap_koor = $ruleForm['date_ap_koor'];
        $ap_pm = $ruleForm['ap_pm'];
        $date_ap_pm = $ruleForm['date_ap_pm'];
        $ap_producer = $ruleForm['ap_producer'];
        $date_producer  = $ruleForm['date_producer'];
        $ap_hd = $ruleForm['ap_hd'];
        $date_ap_hd = $ruleForm['date_ap_hd'];
        // ---------------------
        $ap_infinite = $ruleForm['ap_infinite'];
        $date_ap_infinite = $ruleForm['date_ap_infinite'];
        ////////////////////////////////////////////
        $tahun_libur = db::table('view_off_year')->whereDate('date', '>=', $request->input('leave_date'))->whereDate('date', '<=', $request->input('end_leave_date'))->get();
        $tahuan = array();
        foreach ($tahun_libur as $key => $tahuan_value) {
            $tahuan[] = $tahuan_value->date;
        }

        $awal_cuti = strtotime($request->input('leave_date'));
        $akhir_cuti = strtotime($request->input('end_leave_date'));
        $haricuti = array();
        for ($i = $awal_cuti; $i <= $akhir_cuti; $i += (60 * 60 * 24)) {
            if (date('w', $i) !== '0' && date('w', $i) !== '6') {

                $haricuti[] = $i;
            }
        }

        $start_leaved       = $request->input('leave_date');
        $end_leaved         = $request->input('end_leave_date');
        $back_work_leaved   = $request->input('back_work');

        if ($start_leaved <= $end_leaved) {
            $get_end_leaved = $end_leaved;
        } else {
            $get_end_leaved = null;
        }

        if ($back_work_leaved > $end_leaved) {
            $jumlah_cuti = count($haricuti) - count($tahuan);
        } elseif ($back_work_leaved = $end_leaved) {
            $jumlah_cuti = count($haricuti) - count($tahuan) - 0.5;
        } elseif ($back_work_leaved < $end_leaved) {
            $jumlah_cuti = 0;
        }

        if ($back_work_leaved >= $end_leaved) {
            $get_bacl_work_leaved = $back_work_leaved;
        } else {
            $get_bacl_work_leaved = null;
        }


        $remaining = $request->input('entitlement') - $jumlah_cuti;

        //////////////////////////////////////////////////////

        $nama_provins = $this->namaPronvisi($request->input('nama_provin'));

        $rules = [
            'leave_date'        => 'required',
            'end_leave_date'    => 'required',
            'back_work'         => 'required',
            'reason'            => 'required|max:50'
            /* 'total_day'         => 'required|numeric',
            'remain'            => 'required|numeric|min:0'*/
        ];

        $data = [
            'user_id'                    => Auth::user()->id,
            'leave_category_id'          => '2',
            'request_by'                 => Auth::user()->first_name . ' ' . Auth::user()->last_name,
            'request_nik'                => Auth::user()->nik,
            'request_position'           => Auth::user()->position,
            'request_join_date'          => Auth::user()->join_date,
            'request_dept_category_name' => dept_category::where(['id' => Auth::user()->dept_category_id])->value('dept_category_name'),
            'period'                     => date('Y'),
            'leave_date'                 => $request->input('leave_date'),
            'end_leave_date'             => $get_end_leaved,
            'back_work'                  => $get_bacl_work_leaved,
            'total_day'                  => $jumlah_cuti,
            'taken'                      => null,
            'entitlement'                => null,
            'pending'                    => $init_annual->remainexdo,
            'remain'                     => $remaining,
            'ap_hd'                      => $ap_hd,
            'ap_gm'                      => $ap_gm,
            'date_ap_hd'                 => $date_ap_hd,
            'date_ap_gm'                 => $date_ap_gm,
            'ver_hr'                     => '0',
            'ap_koor'                    => $ap_koor,
            'ap_spv'                     => $ap_spv,
            'ap_pm'                     => $ap_pm,
            'ap_producer'               => $ap_producer,
            'ap_pipeline'               => $ap_pipeline,
            'date_ap_koor'               => $date_ap_koor,
            'date_ap_spv'                => $date_ap_spv,
            'date_ap_pm'                => $date_ap_pm,
            'date_ap_pipeline'          => $date_ap_pipeline,
            'email_koor'                => $email_koor,
            'email_spv'                 => $email_spv,
            'email_pm'                  => $email_pm,
            'email_producer'            => $email_producer,
            'reason_leave'              => strtolower($request->input('reason')),
            'r_departure'               => $nama_provins,
            'r_after_leaving'           => $request->input('nama_city'),
            'plan_leave'                => $request->input('rencana'),
            'agreement'                 => $request->input('agree'),
            'ap_Infinite'               => $ap_infinite,
            'date_ap_Infinite'          => $date_ap_infinite,
            'resendmail'                => 2,
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Redirect::route('leave/createExdo')
                ->withErrors($validator)
                ->withInput();
        } else {
            if ($get_bacl_work_leaved != null) {
                if ($get_end_leaved != null) {
                    if ($remaining < 0) {
                        Session::flash('getError', Lang::get('messages.annual_balance_error', ['name' => auth::user()->first_name . ' ' . auth::user()->last_name]));
                        return back();
                    } else {
                        $leave = Leave::where('user_id', auth::user()->id)->where('leave_date', $request->input('leave_date'))->where('end_leave_date', $get_end_leaved)->latest()->first();

                        if (!empty($leave)) {
                            Session::flash('getError', Lang::get('messages.data_custom', ['data' => 'Sorry, you have applied for leave on that date']));
                            return Redirect::route('leave/create');
                        }

                        Leave::insert($data);
                        Session::flash('message', Lang::get('messages.data_inserted', ['data' => 'Data Leave Transaction']));
                        if (auth::user()->dept_category_id == 5) {
                            $adminFacility = $request->input('adminFacility');
                            $this->sendNotifAdminFacility($adminFacility, auth::user()->id);
                        }

                        if (auth::user()->hd == 1) {
                            $this->sendFormLeaveHoD();
                        } else {
                            $this->sendEmail3();
                        }

                        return Redirect::route('leave/transaction');
                    }
                } else {
                    Session::flash('getError', Lang::get('messages.annual_date_error', ['name' => auth::user()->first_name . ' ' . auth::user()->last_name]));
                    return back();
                }
            } else {
                Session::flash('getError', Lang::get('messages.annual_date_error', ['name' => auth::user()->first_name . ' ' . auth::user()->last_name]));
                return back();
            }
        }
    }


    public function storeLeaveEtc(Request $request)
    {
        $emailCoor = $request->input('sendto');
        $emailSPV = $request->input('sendtoSPV');
        $emailPM = $request->input('sendtoPM');
        $emailProducer = $request->input('sendtoProducer');

        $ruleForm = $this->ruleFormLeave($emailCoor, $emailSPV, $emailPM, $emailProducer);

        $email_koor = $ruleForm['email_koor'];
        $email_spv = $ruleForm['email_spv'];
        $email_pm = $ruleForm['email_pm'];
        $email_producer = $ruleForm['email_producer'];
        $ap_gm      = $ruleForm['ap_gm'];
        $date_ap_gm = $ruleForm['date_ap_gm'];
        $ap_pipeline = $ruleForm['ap_pipeline'];
        $date_ap_pipeline = $ruleForm['date_ap_pipeline'];
        $ap_spv = $ruleForm['ap_spv'];
        $date_ap_spv = $ruleForm['date_ap_spv'];
        $ap_koor = $ruleForm['ap_koor'];
        $date_ap_koor = $ruleForm['date_ap_koor'];
        $ap_pm = $ruleForm['ap_pm'];
        $date_ap_pm = $ruleForm['date_ap_pm'];
        $ap_producer = $ruleForm['ap_producer'];
        $date_producer  = $ruleForm['date_producer'];
        $ap_hd = $ruleForm['ap_hd'];
        $date_ap_hd = $ruleForm['date_ap_hd'];
        // ---------------------
        $ap_infinite = $ruleForm['ap_infinite'];
        $date_ap_infinite = $ruleForm['date_ap_infinite'];

        ////////////////////////////////////////////
        $tahun_libur = db::table('view_off_year')->whereDate('date', '>=', $request->input('leave_date'))->whereDate('date', '<=', $request->input('end_leave_date'))->get();
        $tahuan = array();
        foreach ($tahun_libur as $key => $tahuan_value) {
            $tahuan[] = $tahuan_value->date;
        }

        $awal_cuti = strtotime($request->input('leave_date'));
        $akhir_cuti = strtotime($request->input('end_leave_date'));
        $haricuti = array();
        for ($i = $awal_cuti; $i <= $akhir_cuti; $i += (60 * 60 * 24)) {
            if (date('w', $i) !== '0' && date('w', $i) !== '6') {

                $haricuti[] = $i;
            }
        }
        /////////////////////////////////////////////////////////////
        $start_leaved       = $request->input('leave_date');
        $end_leaved         = $request->input('end_leave_date');
        $back_work_leaved   = $request->input('back_work');

        if ($start_leaved <= $end_leaved) {
            $get_end_leaved = $end_leaved;
        } else {
            $get_end_leaved = null;
        }

        if ($back_work_leaved > $end_leaved) {
            $jumlah_cuti = count($haricuti) - count($tahuan);
        } elseif ($back_work_leaved = $end_leaved) {
            $jumlah_cuti = count($haricuti) - count($tahuan) - 0.5;
        } elseif ($back_work_leaved < $end_leaved) {
            $jumlah_cuti = 0;
        }

        if ($back_work_leaved >= $end_leaved) {
            $get_bacl_work_leaved = $back_work_leaved;
        } else {
            $get_bacl_work_leaved = null;
        }


        $remaining = $request->input('entitlement') - $jumlah_cuti;
        /////////////////////////////////////////////////////////

        $nama_provins = $this->namaPronvisi($request->input('nama_provin'));

        $rules = [
            'leave_date'        => 'required',
            'end_leave_date'    => 'required',
            'back_work'         => 'required',
            'reason'            => 'required|max:100'
            /*'total_day'         => 'required|numeric'*/
        ];

        $data = [
            'user_id'                    => Auth::user()->id,
            'leave_category_id'          => leave_Category::where('leave_category_name', $request->input('leave_category_id'))->value('id'),
            'request_by'                 => Auth::user()->first_name . ' ' . Auth::user()->last_name,
            'request_nik'                => Auth::user()->nik,
            'request_position'           => Auth::user()->position,
            'request_join_date'          => Auth::user()->join_date,
            'request_dept_category_name' => dept_category::where(['id' => Auth::user()->dept_category_id])->value('dept_category_name'),
            'period'                     => date('Y'),
            'leave_date'                 => $request->input('leave_date'),
            'end_leave_date'             => $get_end_leaved,
            'back_work'                  => $get_bacl_work_leaved,
            /*  'total_day'                  => $request->input('total_day'),*/
            'total_day'                  => $jumlah_cuti,
            'taken'                      => null,
            'entitlement'                => null,
            'pending'                    => null,
            'ap_hd'                      => $ap_hd,
            'ap_gm'                      => $ap_gm,
            'date_ap_hd'                 => $date_ap_hd,
            'date_ap_gm'                 => $date_ap_gm,
            'ver_hr'                     => '0',
            'ap_koor'                    => $ap_koor,
            'ap_spv'                     => $ap_spv,
            'ap_pm'                     => $ap_pm,
            'ap_producer'               => $ap_producer,
            'ap_pipeline'               => $ap_pipeline,
            'date_ap_koor'               => $date_ap_koor,
            'date_ap_spv'                => $date_ap_spv,
            'date_ap_pm'                => $date_ap_pm,
            'date_ap_pipeline'          => $date_ap_pipeline,
            'email_koor'                => $email_koor,
            'email_spv'                 => $email_spv,
            'email_pm'                  => $email_pm,
            'email_producer'            => $email_producer,
            'reason_leave'              => strtolower($request->input('reason')),
            'r_departure'               => $nama_provins,
            'r_after_leaving'           => $request->input('nama_city'),
            'plan_leave'              => $request->input('rencana'),
            'agreement'                 => $request->input('agree'),
            'ap_Infinite'               => $ap_infinite,
            'date_ap_Infinite'          => $date_ap_infinite,
            'resendmail'                => 2,
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Redirect::route('leave/createEtc')
                ->withErrors($validator)
                ->withInput();
        } else {
            if ($get_bacl_work_leaved != null) {
                if ($get_end_leaved != null) {
                    $leave = Leave::where('user_id', auth::user()->id)->where('leave_date', $request->input('leave_date'))->where('end_leave_date', $get_end_leaved)->latest()->first();

                    if (!empty($leave)) {
                        Session::flash('getError', Lang::get('messages.data_custom', ['data' => 'Sorry, you have applied for leave on that date']));
                        return Redirect::route('leave/create');
                    }

                    Leave::insert($data);
                    if (auth::user()->dept_category_id == 5) {
                        $adminFacility = $request->input('adminFacility');

                        $this->sendNotifAdminFacility($adminFacility, auth::user()->id);
                    }
                    if (auth::user()->hd == 1) {
                        $this->sendFormLeaveHoD();
                    } else {
                        $this->sendEmail3();
                    }
                    Session::flash('message', Lang::get('messages.data_inserted', ['data' => 'Data Leave Transaction']));
                    return Redirect::route('leave/transaction');
                } else {
                    Session::flash('getError', Lang::get('messages.annual_date_error', ['name' => auth::user()->first_name . ' ' . auth::user()->last_name]));
                    return back();
                }
            } else {
                Session::flash('getError', Lang::get('messages.annual_date_error', ['name' => auth::user()->first_name . ' ' . auth::user()->last_name]));
                return back();
            }
        }
    }

    public function storeAdvanceLeave(Request $request)
    {
        $init_annual = DB::table('users')
            ->leftJoin('initial_leave', 'initial_leave.user_id', '=', 'users.id')
            ->leftJoin('leave_category', 'leave_category.id', '=', 'initial_leave.leave_category_id')
            ->leftJoin('leave_transaction', 'leave_transaction.user_id', '=', 'users.id')
            ->select([
                DB::raw('
            (
                select (
                    select initial_annual from users where id=' . Auth::user()->id . '
                ) - (
                    select COALESCE(sum(total_day), 0) from leave_transaction where user_id=' . Auth::user()->id . ' and leave_category_id=1
                    and ap_hd  = 1
                    and ap_gm  = 1
                    and ver_hr = 1
                    and ap_hrd = 1
                )
            ) as remainannual')
            ])
            ->first();

        $taken = DB::table('users')
            ->leftJoin('initial_leave', 'initial_leave.user_id', '=', 'users.id')
            ->leftJoin('leave_category', 'leave_category.id', '=', 'initial_leave.leave_category_id')
            ->leftJoin('leave_transaction', 'leave_transaction.user_id', '=', 'users.id')
            ->select([
                DB::raw('
            (
                (
                    select COALESCE(sum(total_day), 0) from leave_transaction where user_id=' . Auth::user()->id . ' and leave_category_id=1
                    and ap_hd  = 1
                    and ap_gm  = 1
                    and ver_hr = 1
                    and ap_hrd = 1
                )
            ) as leavetaken')
            ])
            ->first();

        $ent_annual = DB::table('users')
            ->leftJoin('initial_leave', 'initial_leave.user_id', '=', 'users.id')
            ->leftJoin('leave_category', 'leave_category.id', '=', 'initial_leave.leave_category_id')
            ->leftJoin('leave_transaction', 'leave_transaction.user_id', '=', 'users.id')
            ->select([
                DB::raw('
            (
                (
                    select initial_annual from users where id=' . Auth::user()->id . '
                )
            ) as entitle_ann')
            ])
            ->first();

        $leave_d = DB::table('leave_transaction')
            ->whereRaw('Datediff(end_leave_date, leave_date)')
            ->first();

        $emailCoor = $request->input('sendto');
        $emailSPV = $request->input('sendtoSPV');
        $emailPM = $request->input('sendtoPM');
        $emailProducer = $request->input('sendtoProducer');

        $ruleForm = $this->ruleFormLeave($emailCoor, $emailSPV, $emailPM, $emailProducer);

        $email_koor = $ruleForm['email_koor'];
        $email_spv = $ruleForm['email_spv'];
        $email_pm = $ruleForm['email_pm'];
        $email_producer = $ruleForm['email_producer'];
        $ap_gm      = $ruleForm['ap_gm'];
        $date_ap_gm = $ruleForm['date_ap_gm'];
        $ap_pipeline = $ruleForm['ap_pipeline'];
        $date_ap_pipeline = $ruleForm['date_ap_pipeline'];
        $ap_spv = $ruleForm['ap_spv'];
        $date_ap_spv = $ruleForm['date_ap_spv'];
        $ap_koor = $ruleForm['ap_koor'];
        $date_ap_koor = $ruleForm['date_ap_koor'];
        $ap_pm = $ruleForm['ap_pm'];
        $date_ap_pm = $ruleForm['date_ap_pm'];
        $ap_producer = $ruleForm['ap_producer'];
        $date_producer  = $ruleForm['date_producer'];
        $ap_hd = $ruleForm['ap_hd'];
        $date_ap_hd = $ruleForm['date_ap_hd'];
        // ---------------------
        $ap_infinite = $ruleForm['ap_infinite'];
        $date_ap_infinite = $ruleForm['date_ap_infinite'];
        ////////////////////////////////////////////
        $tahun_libur = db::table('view_off_year')->whereDate('date', '>=', $request->input('leave_date'))->whereDate('date', '<=', $request->input('end_leave_date'))->get();
        $tahuan = array();
        foreach ($tahun_libur as $key => $tahuan_value) {
            $tahuan[] = $tahuan_value->date;
        }

        $awal_cuti = strtotime($request->input('leave_date'));
        $akhir_cuti = strtotime($request->input('end_leave_date'));
        $haricuti = array();
        for ($i = $awal_cuti; $i <= $akhir_cuti; $i += (60 * 60 * 24)) {
            if (date('w', $i) !== '0' && date('w', $i) !== '6') {

                $haricuti[] = $i;
            }
        }

        $start_leaved       = $request->input('leave_date');
        $end_leaved         = $request->input('end_leave_date');
        $back_work_leaved   = $request->input('back_work');

        if ($start_leaved <= $end_leaved) {
            $get_end_leaved = $end_leaved;
        } else {
            $get_end_leaved = null;
        }

        if ($back_work_leaved > $end_leaved) {
            $jumlah_cuti = count($haricuti) - count($tahuan);
        } elseif ($back_work_leaved = $end_leaved) {
            $jumlah_cuti = count($haricuti) - count($tahuan) - 0.5;
        } elseif ($back_work_leaved < $end_leaved) {
            $jumlah_cuti = 0;
        }

        if ($back_work_leaved >= $end_leaved) {
            $get_bacl_work_leaved = $back_work_leaved;
        } else {
            $get_bacl_work_leaved = null;
        }

        $remaining = $request->input('entitlement') - $jumlah_cuti;
        /////////////////////////////////////////


        $nama_provins = $this->namaPronvisi($request->input('nama_provin'));

        $forfeited = Forfeited::where('user_id', auth::user()->id)->pluck('countAnnual')->sum();
        $forfeitedCount = ForfeitedCounts::where('user_id', auth::user()->id)->where('status', 1)->pluck('amount')->sum();


        $rules = [
            'leave_date'        => 'required|date',
            'end_leave_date'    => 'required|date',
            'back_work'         => 'required|date',
            'reason'            => 'required|max:50'
            /* 'total_day'         => 'required|numeric',*/
            /* 'remain'            => 'required|numeric|min:0'*/
        ];

        $data = [
            'user_id'                    => Auth::user()->id,
            'leave_category_id'          => '1',
            'req_advance'                => 1,
            'request_by'                 => Auth::user()->first_name . ' ' . Auth::user()->last_name,
            'request_nik'                => Auth::user()->nik,
            'request_position'           => Auth::user()->position,
            'request_join_date'          => Auth::user()->join_date,
            'request_dept_category_name' => dept_category::where(['id' => Auth::user()->dept_category_id])->value('dept_category_name'),
            'period'                     => date('Y'),
            'leave_date'                 => $request->input('leave_date'),
            'end_leave_date'             => $get_end_leaved,
            'back_work'                  => $get_bacl_work_leaved,
            'total_day'                  => $jumlah_cuti,
            'taken'                      => $taken->leavetaken,
            'entitlement'                => $ent_annual->entitle_ann,
            'pending'                    => $init_annual->remainannual,
            'remain'                     => $remaining,
            'ap_hd'                      => $ap_hd,
            'ap_gm'                      => $ap_gm,
            'date_ap_hd'                 => $date_ap_hd,
            'date_ap_gm'                 => $date_ap_gm,
            'ver_hr'                     => '0',
            'ap_koor'                    => $ap_koor,
            'ap_spv'                     => $ap_spv,
            'ap_pm'                     => $ap_pm,
            'ap_producer'               => $ap_producer,
            'ap_pipeline'               => $ap_pipeline,
            'ap_Infinite'               => $ap_infinite,
            'date_ap_Infinite'          => $date_ap_infinite,
            'date_ap_koor'              => $date_ap_koor,
            'date_ap_spv'               => $date_ap_spv,
            'date_ap_pm'                => $date_ap_pm,
            'date_ap_pipeline'          => $date_ap_pipeline,
            'email_koor'                => $email_koor,
            'email_spv'                 => $email_spv,
            'email_pm'                  => $email_pm,
            'email_producer'            => $email_producer,
            'reason_leave'              => strtolower($request->input('reason')),
            'r_departure'               => $nama_provins,
            'r_after_leaving'           => $request->input('nama_city'),
            'plan_leave'                => $request->input('rencana'),
            'agreement'                 => $request->input('agree'),
            'resendmail'                => 2,
        ];

        $forfeited = [
            'user_id'   => auth::user()->id,
            'amount'    => $jumlah_cuti,
            'status'    => 0,
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Redirect::route('leave/create')
                ->withErrors($validator)
                ->withInput();
        } else {
            if ($get_bacl_work_leaved != null) {
                if ($get_end_leaved != null) {
                    if ($remaining < 0) {
                        Session::flash('getError', Lang::get('messages.annual_balance_error', ['name' => auth::user()->first_name . ' ' . auth::user()->last_name]));
                        return back();
                    } else {
                        $leave = Leave::where('user_id', auth::user()->id)->where('leave_date', $request->input('leave_date'))->where('end_leave_date', $get_end_leaved)->latest()->first();

                        if (!empty($leave)) {
                            Session::flash('getError', Lang::get('messages.data_custom', ['data' => 'Sorry, you have applied for leave on that date']));
                            return Redirect::route('leave/create');
                        }

                        Leave::insert($data);
                        $lastLeaved = Leave::where('user_id', auth::user()->id)->where('leave_category_id', 1)->latest()->first();

                        $forfeited = [
                            'user_id'   => auth::user()->id,
                            'leave_id'  => $lastLeaved->id,
                            'amount'    => $jumlah_cuti,
                            'status'    => 0,
                        ];

                        if (auth::user()->dept_category_id == 5) {
                            $adminFacility = $request->input('adminFacility');
                            $this->sendNotifAdminFacility($adminFacility, auth::user()->id);
                        }

                        if ($forfeitedCount < $forfeited) {

                            ForfeitedCounts::insert($forfeited);
                        }

                        if (auth::user()->hd == 1) {
                            $this->sendFormLeaveHoD();
                        } else {
                            $this->sendEmail3();
                        }

                        Session::flash('message', Lang::get('messages.data_inserted', ['data' => 'Data Leave Transaction']));
                        return Redirect::route('leave/transaction');
                    }
                } else {
                    Session::flash('getError', Lang::get('messages.annual_date_error', ['name' => auth::user()->first_name . ' ' . auth::user()->last_name]));
                    return back();
                }
            } else {
                Session::flash('getError', Lang::get('messages.annual_date_error', ['name' => auth::user()->first_name . ' ' . auth::user()->last_name]));
                return back();
            }
        }
    }

    public function DetailStoreLeaveETC(Request $request)
    {
        $return   = "
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <h4 class='modal-title' id='showModalLabel'>Detail  </h4>
            </div>
            <div class='modal-body'>
                <div class='well'>
                    <
                </div>
            </div>

        ";
        return $return;
    }


    //	Start Route Leave > Transaction
    public function indexLeaveTransaction()
    {
        if (Auth::user()->hd === 1) {
            return View::make('leave.indexTransactionHD');
        }

        if (Auth::user()->hr === 0) {
            return View::make('leave.indexTransactionUser');
        }
        if (Auth::user()->hd === 0) {
            return View::make('leave.indexTransactionUser');
        }
        if (Auth::user()->gm === 0) {
            return View::make('leave.indexTransactionUser');
        }
        if (Auth::user()->hr === 1) {
            return View::make('leave.indexTransactionUser');
        }
    }

    public function indexLeaveTransactionUser()
    {
        return View::make('leave.indexTransactionUser');
    }
    public function indexLeaveTransactionHD()
    {
        return View::make('leave.indexTransactionHD');
    }

    public function getIndexTransactionHDFacilities()
    {
        $select = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id',
            'users.nik',
            'leave_transaction.request_by',
            'leave_category.leave_category_name',
            'leave_transaction.leave_date',
            'leave_transaction.total_day',
            'leave_transaction.ap_producer',
            'leave_transaction.ver_hr',
            'leave_transaction.ap_hrd',
            'leave_transaction.resendmail'
        ])
            ->where('leave_transaction.user_id', '=', Auth::user()->id)
            ->get();

        return Datatables::of($select)
            ->edit_column('ap_producer', '@if ($ap_producer === 1){{"APPROVED"}} @elseif($ap_producer === 2){{"DISAPPROVED"}} @else {{"PENDING"}} @endif')

            ->edit_column('ver_hr', '@if ($ver_hr === 1){{ "VERIFIED" }} @elseif($ver_hr === 2){{"UNVERIFIED"}} @elseif ($ap_producer === 0){{ "WAITING HEAD STUDIO" }} @elseif($ap_producer === 1){{"PENDING"}} @elseif ($ver_hr === 3){{"CANCEL"}} @else{{"--"}} @endif')

            ->edit_column('ap_hrd', '@if ($ap_hrd === 1){{ "APPROVED" }} @elseif ($ap_hrd === 2){{"DISAPPROVED"}}  @endif')
            ->edit_column('leave_date', '{!! date("M d, Y", strtotime($leave_date)) !!}')

            ->add_column(
                'actions',
                Lang::get('messages.btn_success', ['title' => 'Detail', 'url' => '{{ URL::route(\'leave/detail\', [$id]) }}', 'class' => 'file']) .
                    '@if ( $ap_hrd === 1 && $ver_hr === 1 )'
                    . Lang::get('messages.btn_print', ['title' => 'Print', 'url' => '{{ URL::route(\'leave/print1\', [$id]) }}', 'class' => 'print'])
                    . '@endif'
            )
            ->add_column(
                'sendmails',
                '@if($resendmail <= 2 and $resendmail > 0)'
                    . Lang::get('messages.btn_resendmail', ['title' => 'send back email notification x{{ $resendmail }}', 'url' => '{{ URL::route(\'leave/reSendMailLeave\', [$id]) }}'])
                    . '@endif'
            )

            ->make();
    }

    public function getIndexLeaveTransactionHD()
    {
        $select = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id',
            'users.nik',
            'leave_transaction.request_by',
            'leave_category.leave_category_name',
            'leave_transaction.leave_date',
            'leave_transaction.total_day',
            'leave_transaction.ap_producer',
            'leave_transaction.ap_gm',
            'leave_transaction.ver_hr',
            'leave_transaction.ap_hrd',
            'leave_transaction.resendmail'
        ])
            ->where('leave_transaction.user_id', '=', Auth::user()->id)
            ->get();

        return Datatables::of($select)
            ->edit_column('ap_producer', '@if ($ap_producer == 1) {{ "APRROVED" }} @elseif ($ap_producer == 2) {{"DISAPPROVED"}} @else {{ "PENDING" }} @endif')
            ->edit_column('ap_gm', '@if($ap_gm === 1){{"APPROVED"}} @elseif($ap_gm === 2){{"DISAPPROVED"}} @else @if ($ap_producer == 0) {{"WAITING FA MANAGER"}} @else {{"PENDING"}} @endif @endif')
            ->edit_column('ver_hr', '@if($ver_hr === 1){{"VERIFIED"}} @elseif($ver_hr === 2){{"UNVERIFIED"}} @else @if ($ap_gm == 0 and $ap_producer == 0) {{"WAITING FA MANAGER"}} @elseif ($ap_gm == 0 and $ap_producer == 1){{"WAITING GM"}} @else {{"PENDING"}} @endif @endif')
            ->edit_column('ap_hrd', '@if($ap_hrd === 1){{"CONFIRMED"}} @elseif($ap_hrd === 2){{"CANCELED"}} @else @if($ap_producer == 0 and $ap_gm == 0 and $ver_hr == 0){{"WAITING FA MAANGER"}} @elseif ($ap_producer == 1 and $ap_gm == 0 and $ver_hr == 0){{"WAITING GM"}} @elseif ($ap_producer == 1 and $ap_gm == 1 and $ver_hr == 0){{"WAITING HR VERIFY"}} @else {{"PENDING"}} @endif @endif')
            ->edit_column('leave_date', '{!! date("M d, Y", strtotime($leave_date)) !!}')
            ->add_column(
                'actions',
                Lang::get('messages.btn_success', ['title' => 'Detail', 'url' => '{{ URL::route(\'leave/detail/hod\', [$id]) }}', 'class' => 'file']) .
                    '@if ( $ap_hrd === 1 && $ver_hr === 1 )'
                    . Lang::get('messages.btn_print', ['title' => 'Print', 'url' => '{{ URL::route(\'leave/print1\', [$id]) }}', 'class' => 'print'])
                    . '@else'
                    . Lang::get('messages.btn_delete_custom', ['title' => 'Delete', 'url' => '{{ URL::route(\'leave/delete/form/Officer\', [$id]) }}', 'class' => 'trash'])
                    . '@endif'
            )
            ->add_column(
                'sendmails',
                '@if($resendmail <= 2 and $ap_hrd == 0)'
                    . Lang::get('messages.btn_resendmail', ['title' => 'send back email notification x{{ $resendmail }}', 'url' => '{{ URL::route(\'leave/reSendMailLeave/hod\', [$id]) }}'])
                    . '@endif'
            )
            // ->rawColumns(['actions'])
            ->make();
    }

    public function getIndexLeaveTransactionHDfa()
    {
        $select = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id',
            'users.nik',
            'leave_transaction.request_by',
            'leave_category.leave_category_name',
            'leave_transaction.leave_date',
            'leave_transaction.total_day',
            'leave_transaction.ap_Infinite',
            'leave_transaction.ver_hr',
            'leave_transaction.ap_hrd',
            'leave_transaction.ap_gm',
            'leave_transaction.resendmail'
        ])
            ->where('leave_transaction.user_id', '=', Auth::user()->id)
            ->get();

        return Datatables::of($select)
            ->edit_column('ap_Infinite', '@if($ap_Infinite === 0){{"APPROVED"}} @elseif($ap_Infinite === 2){{"DISAPPROVED"}} @else {{"PENDING"}} @endif')

            ->edit_column('ver_hr', '@if($ver_hr === 1){{"VERIFIED"}} @elseif($ver_hr === 2){{"UNVERIFIED"}} @elseif($ver_hr === 0 and $ap_Infinite === 0){{"HR CHECKING"}} @elseif($ver_hr === 0 and $ap_Infinite === 1){{"WAITING INFINITE APPROVED"}} @else {{"--"}} @endif')

            ->edit_column('ap_hrd', '@if($ap_hrd === 1){{"APPROVED"}} @elseif($ap_hrd === 2){{"DISAPPROVED"}} @elseif($ap_hrd === 0 and $ver_hr === 1){{"PENDING"}} @elseif($ver_hr === 0 and $ap_Infinite === 1){{"HR CHECKING"}} @elseif($ap_Infinite === 0){{"WAITING INFINITE APPROVED"}} @else {{"--"}} @endif')

            ->edit_column('ap_gm', '@if($ap_gm === 1){{"APPROVED"}} @elseif($ap_gm === 2){{"DISAPPROVED"}} @elseif($ap_gm === 0 and $ap_hrd === 1){{"PENDING"}} @elseif($ap_hrd === 0 and $ver_hr === 1){{"WAITING HR MANAGER"}} @elseif($ver_hr === 0 and $ap_Infinite === 1){{"HR CHECKING"}} @elseif($ap_Infinite === 0){{"WAITING INFINITE APPROVED"}} @else {{"--"}} @endif')

            ->edit_column('leave_date', '{!! date("M d, Y", strtotime($leave_date)) !!}')

            ->add_column(
                'actions',
                Lang::get('messages.btn_success', ['title' => 'Detail', 'url' => '{{ URL::route(\'leave/detail\', [$id]) }}', 'class' => 'file']) .
                    '@if ( $ap_hrd === 1 && $ver_hr === 1 )'
                    . Lang::get('messages.btn_print', ['title' => 'Print', 'url' => '{{ URL::route(\'leave/print1\', [$id]) }}', 'class' => 'print'])
                    . '@endif'
            )
            ->add_column(
                'sendmails',
                '@if($resendmail <= 2 and $resendmail > 0)'
                    . Lang::get('messages.btn_resendmail', ['title' => 'send back email notification x{{ $resendmail }}', 'url' => '{{ URL::route(\'leave/reSendMailLeave\', [$id]) }}'])
                    . '@endif'
            )
            ->make();
    }

    public function indexLeaveAllTransaction()
    {
        return View::make('leave.indexAllTransaction');
    }

    public function getIndexLeaveTransaction()
    {
        // dept_category_id 6 = Production
        if (Auth::user()->dept_category_id != 6) {
            // dept_category_id 4 = Operation
            if (auth::user()->dept_category_id === 4) {
                $select = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id',
                    'users.nik',
                    'leave_transaction.request_by',
                    'leave_category.leave_category_name',
                    'leave_transaction.leave_date',
                    'leave_transaction.total_day',
                    'leave_transaction.ver_hr',
                    'leave_transaction.ap_hrd',
                    //'leave_transaction.ap_gm'
                ])
                    ->where('leave_transaction.user_id', '=', Auth::user()->id)
                    ->get();

                return Datatables::of($select)
                    ->edit_column('ap_hrd', '@if ($ap_hrd === 1){{ "APPROVED" }} @elseif ($ap_hrd === 2){{"DISAPPROVED"}} @elseif ($ver_hr === 0){{ "WAITING HR" }} @elseif ($ver_hr === 1){{ "PENDING" }}@endif')

                    ->edit_column('ver_hr', '@if ($ver_hr === 1){{ "VERIFIED" }} @elseif ($ver_hr === 0){{ "PENDING" }} @elseif ($ver_hr === 2){{ "UNVERIFIED" }} @elseif ($ver_hr === 3){{"CANCEL"}} @endif')
                    ->edit_column('leave_date', '{!! date("M d, Y", strtotime($leave_date)) !!}')
                    ->add_column(
                        'actions',
                        Lang::get('messages.btn_success', ['title' => 'Detail', 'url' => '{{ URL::route(\'leave/detail\', [$id]) }}', 'class' => 'file']) .
                            '@if ( $ap_hrd === 1 && $ver_hr === 1 )'
                            . Lang::get('messages.btn_print', ['title' => 'Print', 'url' => '{{ URL::route(\'leave/print1\', [$id]) }}', 'class' => 'print'])
                            . '@endif'
                    )
                    ->make();
            }

            if (auth::user()->hd === 1) {

                $GM = DB::table('users')
                    ->select(db::raw('gm'))
                    ->where('gm', '=', 1)
                    ->value('gm');

                if ($GM != null) {
                    $select = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                        'leave_transaction.id',
                        'users.nik',
                        'leave_transaction.request_by',
                        'leave_category.leave_category_name',
                        'leave_transaction.leave_date',
                        'leave_transaction.total_day',
                        'leave_transaction.ver_hr',
                        'leave_transaction.ap_hrd',
                        'leave_transaction.ap_gm'
                    ])
                        ->where('leave_transaction.user_id', '=', Auth::user()->id)
                        ->get();

                    return Datatables::of($select)
                        ->edit_column('ap_gm', '@if($ap_gm === 1){{"APPROVED"}} @elseif($ap_gm === 2){{"DISAPPROVED"}} @elseif($ver_hr === 0){{"WAITING VERIFY"}} @elseif($ver_hr === 1 and $ap_hrd === 0){{"WAITING HRD"}} @elseif($ap_hrd === 1){{"PENDING"}} @else {{"--"}} @endif')
                        ->edit_column('ver_hr', '@if($ver_hr === 1){{"VERIFIED"}} @elseif($ver_hr === 2){{"UNVERIFIED"}} @else {{"PENDING"}} @endif')
                        ->edit_column('ap_hrd', '@if($ap_hrd === 1){{"APPROVED"}} @elseif($ap_hrd === 2){{"DISAPPROVED"}} @elseif($ver_hr === 0){{"WAITING VERIFY"}} @elseif($ver_hr === 1){{"PENDING"}} @else {{"--"}} @endif')
                        ->edit_column('leave_date', '{!! date("M d, Y", strtotime($leave_date)) !!}')
                        ->add_column(
                            'actions',
                            Lang::get('messages.btn_success', ['title' => 'Detail', 'url' => '{{ URL::route(\'leave/detail\', [$id]) }}', 'class' => 'file']) .
                                '@if ( $ap_hrd === 1 && $ap_gm === 1 )'
                                . Lang::get('messages.btn_print', ['title' => 'Print', 'url' => '{{ URL::route(\'leave/print1\', [$id]) }}', 'class' => 'print'])
                                . '@endif'
                        )
                        ->make();
                } else {
                    $select = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                        'leave_transaction.id',
                        'users.nik',
                        'leave_transaction.request_by',
                        'leave_category.leave_category_name',
                        'leave_transaction.leave_date',
                        'leave_transaction.total_day',
                        'leave_transaction.ver_hr',
                        'leave_transaction.ap_hrd',
                        //'leave_transaction.ap_gm'
                    ])
                        ->where('leave_transaction.user_id', '=', Auth::user()->id);

                    return Datatables::of($select)
                        ->edit_column('ap_hrd', '@if ($ap_hrd === 1){{ "APPROVED" }} @elseif ($ap_hrd === 2){{"DISAPPROVED"}} @elseif ($ver_hr === 0){{ "WAITING HR" }} @elseif ($ver_hr === 1){{ "PENDING" }}@endif')

                        ->edit_column('ver_hr', '@if ($ver_hr === 1){{ "VERIFIED" }} @elseif ($ver_hr === 0){{ "PENDING" }} @elseif ($ver_hr === 2){{ "UNVERIFIED" }} @elseif ($ver_hr === 3){{"CANCEL"}} @endif')
                        ->edit_column('leave_date', '{!! date("M d, Y", strtotime($leave_date)) !!}')
                        ->add_column(
                            'actions',
                            Lang::get('messages.btn_success', ['title' => 'Detail', 'url' => '{{ URL::route(\'leave/detail\', [$id]) }}', 'class' => 'file']) .
                                '@if ( $ap_hrd === 1 && $ver_hr === 1 )'
                                . Lang::get('messages.btn_print', ['title' => 'Print', 'url' => '{{ URL::route(\'leave/print1\', [$id]) }}', 'class' => 'print'])
                                . '@endif'
                        )
                        ->make();
                }
            } else {
                /* officer */
                $select = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                    'leave_transaction.id',
                    'users.nik',
                    'leave_transaction.request_by',
                    'leave_category.leave_category_name',
                    'leave_transaction.leave_date',
                    'leave_transaction.total_day',
                    'leave_transaction.ap_hd',
                    'leave_transaction.ver_hr',
                    'leave_transaction.ap_hrd',
                    'leave_transaction.resendmail',
                    'leave_transaction.req_advance'
                ])
                    ->where('leave_transaction.user_id', '=', Auth::user()->id)
                    ->get();

                return Datatables::of($select)
                    ->edit_column('ap_hd', '@if($ap_hd === 1){{"APPROVED"}} @elseif($ap_hd === 2){{"DISAPPROVED"}} @else {{"PENDING"}} @endif')
                    ->edit_column('ver_hr', '@if($ver_hr === 1){{"VERIFIED"}} @elseif($ver_hr === 2){{"UNVERIFIED"}} @elseif($ap_hd === 0 and $ver_hr === 0){{"WAITING HD"}} @elseif($ap_hd === 1 and $ver_hr === 0){{"PENDING"}} @else{{"--"}} @endif')
                    ->edit_column('ap_hrd', '@if($ap_hrd === 1){{"APPROVED"}} @elseif($ap_hrd === 2){{"DISAPPROVED"}} @elseif($ap_hrd === 5){{"--"}} @elseif($ver_hr === 1 and $ap_hrd === 0 and $ap_hd === 1){{"PENDING"}} @elseif($ver_hr === 0 and $ap_hd === 0 and $ap_hrd === 0){{"WAITING HD"}} @elseif($ver_hr === 0 and $ap_hrd === 0 and $ap_hd === 1){{"WAITING VERIFY"}} @else{{"--"}} @endif')
                    ->edit_column('leave_date', '{!! date("M d, Y", strtotime($leave_date)) !!}')
                    ->edit_column('leave_category_name', '@if($req_advance === 2){{"Forfeited"}} @else {{$leave_category_name}} @endif')
                    ->add_column(
                        'actions',
                        Lang::get('messages.btn_success', ['title' => 'Detail', 'url' => '{{ URL::route(\'leave/detail\', [$id]) }}', 'class' => 'file'])
                            . '@if ( $ap_hrd === 1 && $ap_hd === 1 )'
                            . Lang::get('messages.btn_print', ['title' => 'Print', 'url' => '{{ URL::route(\'leave/print1\', [$id]) }}', 'class' => 'print'])
                            . '@endif'
                    )
                    ->add_column(
                        'sendmails',
                        '@if($resendmail <= 2 and $resendmail > 0)'
                            . Lang::get('messages.btn_resendmail', ['title' => 'send back email notification x{{ $resendmail }}', 'url' => '{{ URL::route(\'leave/reSendMailLeave\', [$id]) }}'])
                            . '@endif'
                    )
                    ->add_column(
                        'frase',
                        '@if ($ap_hd === 0)'
                            . Lang::get('messages.btn_danger', ['url' => '{{ URL::route(\'leave/delete/form/Officer\', [$id]) }}', 'data' => 'this eform'])
                            . '@endif'

                    )
                    ->setRowClass('@if ($req_advance !== 0){{ "danger" }}@endif')

                    ->make();
            }
        } else {
            $select = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
                'leave_transaction.id',
                'users.nik',
                'leave_transaction.request_by',
                'leave_category.leave_category_name',
                'leave_transaction.leave_date',
                'leave_transaction.total_day',
                'leave_transaction.ap_koor',
                'leave_transaction.ap_pm',
                'leave_transaction.ap_hd',
                'leave_transaction.ap_hrd',

            ])
                ->where('leave_transaction.user_id', '=', Auth::user()->id)
                ->get();

            return Datatables::of($select)
                ->edit_column('ap_koor', '@if ($ap_koor === 1) {{"APPROVED"}} @elseif ($ap_koor === 2) {{"DISAPPROVED"}} @else {{"PENDING"}} @endif  ')

                ->edit_column('ap_pm', '@if ($ap_pm === 1) {{"APPROVED"}} @elseif ($ap_pm === 2) {{"DISAPPROVED"}} @elseif($ap_koor === 2){{"--"}} @elseif ($ap_koor === 0) {{"WAITING COORDINATOR"}}  @else {{"PENDING"}} @endif ')

                ->edit_column('ap_hd', '@if ($ap_hd === 1) {{"APPROVED"}} @elseif ($ap_hd === 2) {{"DISAPPROVED"}} @elseif ($ap_koor === 2) {{"--"}} @elseif ($ap_pm === 2){{"--"}} @elseif ($ap_hrd === 2) {{"--"}} @elseif ($ap_koor === 0) {{"WAITING COORDINATOR"}} @elseif ($ap_pm === 0){{"WAITING PM"}} @else {{"PENDING"}} @endif')

                ->edit_column('ap_hrd', '@if ($ap_hrd === 1) {{"VERIFIED"}} @elseif ($ap_hrd === 2) {{"UNVERIFIED"}} @elseif ($ap_koor === 2) {{"--"}} @elseif ($ap_pm === 2){{"--"}} @elseif ($ap_hd === 2){{"--"}} @elseif ($ap_koor === 0) {{"WAITING COORDINATOR"}} @elseif ($ap_pm === 0){{"WAITING PM"}} @elseif ($ap_hd === 0){{"WAITING MANAGER"}}  @else {{"PENDING"}} @endif')


                ->edit_column('leave_date', '{!! date("M d, Y", strtotime($leave_date)) !!}')
                ->add_column(
                    'actions',
                    Lang::get('messages.btn_success', ['title' => 'Detail', 'url' => '{{ URL::route(\'leave/detail\', [$id]) }}', 'class' => 'file']) .
                        '@if ( $ap_hrd === 1  )'
                        . Lang::get('messages.btn_print', ['title' => 'Print', 'url' => '{{ URL::route(\'leave/print1\', [$id]) }}', 'class' => 'print'])
                        . '@endif'
                )
                ->make();
        }
    }

    public function getIndexTransactionOperation()
    {
        $select = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id',
            'users.nik',
            'leave_transaction.request_by',
            'leave_category.leave_category_name',
            'leave_transaction.leave_date',
            'leave_transaction.total_day',
            'leave_transaction.ap_hd',
            'leave_transaction.ver_hr',
            'leave_transaction.ap_hrd',
            'leave_transaction.resendmail',
            'leave_transaction.req_advance'
        ])->where('leave_transaction.user_id', '=', Auth::user()->id)->get();


        return Datatables::of($select)
            ->edit_column('ap_hd', '@if($ap_hd === 1){{"APPROVED"}} @elseif($ap_hd === 2){{"DISAPPROVED"}} @else {{"PENDING"}} @endif')
            ->edit_column('ver_hr', '@if($ver_hr === 1){{"VERIFIED"}} @elseif($ver_hr === 2){{"UNVERIFIED"}} @elseif($ap_hd === 0 and $ver_hr === 0){{"WAITING HD"}} @elseif($ap_hd === 1 and $ver_hr === 0){{"PENDING"}} @else{{"--"}} @endif')
            ->edit_column('ap_hrd', '@if($ap_hrd === 1){{"APPROVED"}} @elseif($ap_hrd === 2){{"DISAPPROVED"}} @elseif($ap_hrd === 5){{"--"}} @elseif($ver_hr === 1 and $ap_hrd === 0 and $ap_hd === 1){{"PENDING"}} @elseif($ver_hr === 0 and $ap_hd === 0 and $ap_hrd === 0){{"WAITING HD"}} @elseif($ver_hr === 0 and $ap_hrd === 0 and $ap_hd === 1){{"WAITING VERIFY"}} @else{{"--"}} @endif')
            ->edit_column('leave_date', '{!! date("M d, Y", strtotime($leave_date)) !!}')
            ->edit_column('leave_category_name', '@if($req_advance === 2){{"Forfeited"}} @else {{$leave_category_name}} @endif')
            ->setRowClass('@if ($req_advance !== 0){{ "danger" }}@endif')
            ->add_column(
                'actions',
                Lang::get('messages.btn_success', ['title' => 'Detail', 'url' => '{{ URL::route(\'leave/detail\', [$id]) }}', 'class' => 'file'])
                    . '@if ( $ap_hrd === 1 && $ap_hd === 1 )'
                    . Lang::get('messages.btn_print', ['title' => 'Print', 'url' => '{{ URL::route(\'leave/print1\', [$id]) }}', 'class' => 'print'])
                    . '@endif'
            )
            ->add_column(
                'sendmails',
                '@if($resendmail <= 2 and $resendmail > 0)'
                    . Lang::get('messages.btn_resendmail', ['title' => 'send back email notification x{{ $resendmail }}', 'url' => '{{ URL::route(\'leave/reSendMailLeave\', [$id]) }}'])
                    . '@endif'
            )
            ->add_column(
                'frase',
                '@if ($ap_hd === 0)'
                    . Lang::get('messages.btn_danger', ['url' => '{{ URL::route(\'leave/delete/form/Officer\', [$id]) }}', 'data' => 'this eform'])
                    . '@endif'

            )
            ->make();
    }

    public function getIndexTransactionProduction()
    {
        $select = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id',
            'users.nik',
            'leave_transaction.request_by',
            'leave_category.leave_category_name',
            'leave_transaction.leave_date',
            'leave_transaction.total_day',
            'leave_transaction.ap_koor',
            'leave_transaction.ap_spv',
            'leave_transaction.ap_pm',
            'leave_transaction.ap_producer',
            'leave_transaction.ap_hd',
            'leave_transaction.ver_hr',
            'leave_transaction.ap_hrd',
            'leave_transaction.resendmail',
            'leave_transaction.req_advance'
        ])
            ->where('leave_transaction.user_id', '=', Auth::user()->id)
            ->get();

        return Datatables::of($select)
            ->edit_column('ap_koor', '@if ($ap_koor === 1) {{"APPROVED"}} @elseif ($ap_koor === 2) {{"DISAPPROVED"}} @elseif($ap_koor === 0){{"PENDING"}} @else {{"--"}} @endif  ')
            ->edit_column('ap_spv', '@if ($ap_spv === 1){{"APPROVED"}} @elseif ($ap_spv === 2) {{"DISAPPROVED"}} @elseif ($ap_spv === 0 and $ap_koor === 0){{"WAITING COORDINATOR"}} @else {{"PENDING"}} @endif')
            ->edit_column('ap_pm', '@if ($ap_pm === 1) {{"APPROVED"}} @elseif ($ap_pm === 2) {{"DISAPPROVED"}} @elseif($ap_koor === 2){{"--"}} @elseif ($ap_koor === 0) {{"WAITING COORDINATOR"}}  @else {{"PENDING"}} @endif ')

            ->edit_column('ap_producer', '@if ($ap_producer === 1){{"APPROVED"}} @elseif ($ap_producer === 2){{"DISAPPROVED"}} @elseif ($ap_koor === 0){{"WAITING COORDINATOR"}} @elseif ($ap_koor === 1 and $ap_spv === 0){{"WAITING SPV"}} @elseif ($ap_spv === 1 and $ap_pm === 0){{"WAITING PM"}} @elseif ($ap_pm === 1 and $ap_producer === 0){{"PENDING"}} @else {{"--"}} @endif')

            ->edit_column('ap_hd', '@if ($ap_hd === 1) {{"APPROVED"}} @elseif ($ap_hd === 2) {{"DISAPPROVED"}} @elseif ($ap_koor === 0){{"WAITING COORDINATOR"}} @elseif ($ap_koor === 1 and $ap_spv === 0){{"WAITING SPV"}} @elseif ($ap_spv === 1 and $ap_pm === 0){{"WAITING PM"}} @elseif ($ap_pm === 1 and $ap_producer === 0){{"WAITING PRODUCER"}} @elseif ($ap_producer === 1 and $ap_hd === 0){{"PENDING"}} @else {{"--"}} @endif')

            ->edit_column('ver_hr', '@if ($ver_hr === 1) {{"VERIFIED"}} @elseif ($ver_hr === 2) {{"UNVERIFIED"}} @elseif ($ap_koor === 0){{"WAITING COORDINATOR"}} @elseif ($ap_koor === 1 and $ap_spv === 0){{"WAITING SPV"}} @elseif ($ap_spv === 1 and $ap_pm === 0){{"WAITING PM"}} @elseif ($ap_pm === 1 and $ap_producer === 0){{"WAITING PRODUCER"}} @elseif ($ap_producer === 1 and $ap_hd === 0){{"WAITING HOD"}} @elseif ($ap_hd === 1 and $ver_hr === 0){{"PENDING"}} @else {{"--"}} @endif')

            ->edit_column('ap_hrd', '@if($ap_hrd === 1){{"APPROVED"}} @elseif($ap_hrd === 2){{"DISAPPROVED"}} @elseif ($ap_koor === 0){{"WAITING COORDINATOR"}} @elseif ($ap_koor === 1 and $ap_spv === 0){{"WAITING SPV"}} @elseif ($ap_spv === 1 and $ap_pm === 0){{"WAITING PM"}} @elseif ($ap_pm === 1 and $ap_producer === 0){{"WAITING PRODUCER"}} @elseif ($ap_producer === 1 and $ap_hd === 0){{"WAITING HOD"}} @elseif ($ap_hd === 1 and $ver_hr === 0){{"PENDING"}} @elseif ($ver_hr === 1 and $ap_hrd === 0){{"PENDING"}} @else {{"--"}} @endif')

            ->edit_column('leave_category_name', '@if($req_advance === 2){{"Forfeited"}} @else {{$leave_category_name}} @endif')
            ->setRowClass('@if ($req_advance !== 0){{ "danger" }}@endif')
            ->edit_column('leave_date', '{!! date("M d, Y", strtotime($leave_date)) !!}')
            ->add_column(
                'actions',
                Lang::get('messages.btn_success', ['title' => 'Detail', 'url' => '{{ URL::route(\'leave/detail\', [$id]) }}', 'class' => 'file']) .
                    '@if ( $ap_hrd === 1  )'
                    . Lang::get('messages.btn_print', ['title' => 'Print', 'url' => '{{ URL::route(\'leave/print1\', [$id]) }}', 'class' => 'print'])
                    . '@endif'
            )
            ->add_column(
                'sendmails',
                '@if($resendmail <= 2 and $resendmail > 0)'
                    . Lang::get('messages.btn_resendmail', ['title' => 'send back email notification x{{ $resendmail }}', 'url' => '{{ URL::route(\'leave/reSendMailLeave\', [$id]) }}'])
                    . '@endif'
            )
            ->add_column(
                'frase',
                '@if ($ap_hd === 0)'
                    . Lang::get('messages.btn_danger', ['url' => '{{ URL::route(\'leave/delete/form/Officer\', [$id]) }}', 'data' => 'this eform'])
                    . '@endif'

            )
            ->make();
    }
    // tes pipeline
    public function indexLeaveTransactionPipiline()
    {
        $select = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id',
            'users.nik',
            'leave_transaction.request_by',
            'leave_category.leave_category_name',
            'leave_transaction.leave_date',
            'leave_transaction.total_day',
            'leave_transaction.ap_spv',
            'leave_transaction.ap_hd',
            'leave_transaction.ver_hr',
            'leave_transaction.ap_hrd',
            'leave_transaction.resendmail',
            'leave_transaction.req_advance'
        ])
            ->where('leave_transaction.user_id', '=', Auth::user()->id);

        return Datatables::of($select)
            ->edit_column('ap_spv', '@if($ap_spv === 1){{"APPROVED"}} @elseif($ap_spv === 2){{"DISAPPROVED"}} @elseif($ap_spv === 0){{"PENDING"}} @else {{"--"}} @endif')
            ->edit_column('ap_hd', '@if($ap_hd === 1){{"APPROVED"}} @elseif($ap_hd === 2){{"DISAPPROVED"}} @elseif($ap_spv === 1){{"PENDING"}} @elseif($ap_spv === 0){{"WAITING SPV"}} @else {{"--"}} @endif')
            ->edit_column('ver_hr', '@if($ver_hr === 1){{"VERIFIED"}} @elseif($ver_hr === 2){{"UNVERIFIED"}} @elseif($ap_hd === 1 and $ap_spv === 1){{"PENDING"}} @elseif($ap_hd === 0 and $ap_spv === 1){{"WAITING HD"}} @elseif($ap_hd === 0 and $ap_spv === 0){{"WAITING SPV"}} @else {{"--"}} @endif')
            ->edit_column('ap_hrd', '@if($ap_hrd === 1){{"APPROVED"}} @elseif($ap_hrd === 2){{"DISAPPROVED"}} @elseif($ver_hr === 1 and $ap_hd == 1 and $ap_spv === 1){{"PENDING"}} @elseif($ver_hr === 0 and $ap_hd == 1 and $ap_spv === 1){{"WAITING VERIFY"}} @elseif($ver_hr === 0 and $ap_hd == 0 and $ap_spv === 1){{"WAITING HD"}} @elseif($ver_hr === 0 and $ap_hd == 0 and $ap_spv === 0){{"WAITING SPV"}} @else {{"-"}} @endif')
            ->edit_column('leave_category_name', '@if($req_advance === 2){{"Forfeited"}} @else {{$leave_category_name}} @endif')
            ->setRowClass('@if ($req_advance !== 0){{ "danger" }}@endif')
            ->add_column(
                'actions',
                Lang::get('messages.btn_success', ['title' => 'Detail', 'url' => '{{ URL::route(\'leave/detail\', [$id]) }}', 'class' => 'file']) .
                    '@if ( $ap_hrd === 1  )'
                    . Lang::get('messages.btn_print', ['title' => 'Print', 'url' => '{{ URL::route(\'leave/print1\', [$id]) }}', 'class' => 'print'])
                    . '@endif'
            )

            ->add_column(
                'sendmails',
                '@if($resendmail <= 2 and $resendmail > 0)'
                    . Lang::get('messages.btn_resendmail', ['title' => 'send back email notification x{{ $resendmail }}', 'url' => '{{ URL::route(\'leave/reSendMailLeave\', [$id]) }}'])
                    . '@endif'
            )
            ->add_column(
                'frase',
                '@if ($ap_hd === 0)'
                    . Lang::get('messages.btn_danger', ['url' => '{{ URL::route(\'leave/delete/form/Officer\', [$id]) }}', 'data' => 'this eform'])
                    . '@endif'

            )
            ->make();
    }



    public function getIndexAllLeaveTransaction()
    {
        $select = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id',
            'users.nik',
            'leave_transaction.request_by',
            'leave_category.leave_category_name',
            'leave_transaction.leave_date',
            'leave_transaction.total_day',
            'leave_transaction.ap_hd',
            'leave_transaction.ver_hr',
            'leave_transaction.ap_hrd',
            'leave_transaction.ap_gm',
        ])->where('users.active', 1)
            ->where('leave_transaction.period', date('Y'))
            ->get();


        return Datatables::of($select)
            ->edit_column('ap_hd', '@if ($ap_hd === 1){{ "APPROVED" }} @elseif ($ap_hd === 2){{"DISAPPROVED"}} @else{{"PENDING"}} @endif')
            ->edit_column('ver_hr', '@if ($ver_hr === 1){{ "VERIFIED" }} @elseif ($ver_hr === 0 and $ap_hd === 1){{ "PENDING" }} @elseif ($ver_hr === 2){{ "UNVERIFIED" }} @elseif($ap_hd === 0){{"WAITING HD"}} @else{{"--"}} @endif')
            ->edit_column('ap_hrd', '@if($ap_hrd === 1){{"APPROVED"}} @elseif($ap_hrd === 2){{"DISAPPROVED"}} @elseif($ap_hrd === 5){{"CANCELED"}} @elseif($ap_hd === 0){{"WAITING HD"}} @elseif($ver_hr === 0 and $ap_hd === 1){{"WAITING VERIFY"}} @else{{"PENDING"}} @endif')

            ->edit_column('ap_gm', '@if($ap_hd === 0){{"WAITING HD"}} @elseif($ap_hd === 1 and $ver_hr === 0){{"WAITING VERIFY"}} @elseif($ver_hr === 1 and $ap_hrd === 0){{"WAITING HR MANAGER"}} @elseif($ap_hrd === 5){{"--"}} @elseif($ap_gm === 1 and $ap_hrd !== 5){{"APPROVED"}} @elseif($ap_gm === 2){{"DISAPPROVED"}} @else{{"PENDING"}} @endif')

            ->edit_column('leave_date', '{!! date("M d, Y", strtotime($leave_date)) !!}')
            ->add_column(
                'actions',

                Lang::get('messages.btn_success', ['title' => 'Detail', 'url' => '{{ URL::route(\'leave/detail\', [$id]) }}', 'class' => 'file']) .
                    '@if ($ap_hd === 1 && $ap_gm === 1 && $ver_hr === 1)'
                    . Lang::get('messages.btn_print', ['title' => 'Print', 'url' => '{{ URL::route(\'leave/print1\', [$id]) }}', 'class' => 'print'])
                    . '@endif'

            )

            ->make();
    }



    public function detailLeave($id)
    {
        $leave = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->find($id);



        $coor = null;
        $spv = null;
        $spvV = null;
        $pm = null;
        $pmM = null;
        $head = null;

        if (!empty($leave->email_koor)) {
            $coor = User::where('email', $leave->email_koor)->first();
            $coor = $coor->first_name . ' ' . $coor->last_name;
        }

        if (!empty($leave->email_spv)) {
            $spv = User::where('email', $leave->email_spv)->first();
            $spvV = $spv->first_name . ' ' . $spv->last_name;
        }

        if (!empty($leave->email_pm)) {
            $pm = User::where('email', $leave->email_pm)->first();

            if ($pm->hd === 1) {
                $pmM =  "<strong>Head of Deparment :</strong>" . $pm->first_name . ' ' . $pm->last_name;
            } else {
                $pmM =  "<strong>Project Manager / Producer :</strong>" . $pm->first_name . ' ' . $pm->last_name;
            }
        }


        if ($leave->dept_category_id === 6) {
            $head = "<strong>Head of Deparment :</strong> Ghea Lisanova";
        }

        $return   = "
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <h4 class='modal-title' id='showModalLabel'>Detail</h4>
            </div>
            <div class='modal-body'>
                <div class='well'>
                    <h4><strong><u>Leave Transaction	</u></strong></h4>
                    <strong>Request by :</strong> $leave->first_name $leave->last_name<br>
                    <strong>Period :</strong> $leave->period <br>
                    <strong>Join Date :</strong> $leave->join_date <br>
                    <strong>NIK :</strong> $leave->nik <br>
                    <strong>Position :</strong> $leave->position <br>
                    <strong>Department :</strong> $leave->dept_category_name <br>
                    <strong>Contact Address :</strong> $leave->address <br>
                    <strong>Leave Category :</strong> $leave->leave_category_name <br>
                    <strong>Start Leave :</strong> $leave->leave_date <br>
                    <strong>End Leave :</strong> $leave->end_leave_date <br>
                    <strong>Back to Work :</strong> $leave->back_work <br>
                    <strong>Total Annual :</strong> $leave->pending <br>
                    <strong>Request Day :</strong> $leave->total_day <br>
                    <strong>Total Balance :</strong> $leave->remain <br>
                </div>
                <div class='well'>

                    <h5><strong><u>Additional</u></strong></h5>
                    <strong>Destination :</strong> $leave->r_departure - $leave->r_after_leaving <br>
                    <strong>Reason :</strong> $leave->reason_leave <br> <br>

                    <h5><strong><u>Requested Approval To</u></strong></h5>
                    <strong>Coordinator :</strong> $coor <br>
                    <strong>Supervisor :</strong> $spvV <br>
                    $pmM <br> $head

                </div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
            </div>
        ";


        return $return;
    }

    public function detailLeaveHod($id)
    {
        $leave = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->find($id);

        // $FaManager = User::where('email', $leave->email_pm)->where('dept_ApprovedHOD', 1)->where('active', 1)->first();
        $FaManager = User::where('email', $leave->email_pm)->where('dept_ApprovedHOD', 0)->where('active', 1)->first();
        $FaManager = $FaManager->first_name . " " . $FaManager->last_name;

        // $GenaralManager = User::where('email', $leave->email_producer)->where('gm', 1)->where('active', 1)->first();
        $GenaralManager = User::where('gm', 1)->where('active', 1)->first();
        $GenaralManager = $GenaralManager->first_name . " " . $GenaralManager->last_name;

        $return   = "
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <h4 class='modal-title' id='showModalLabel'>Detail</h4>
            </div>
            <div class='modal-body'>
                <div class='well'>
                    <h4><strong><u>Leave Transaction	</u></strong></h4>
                    <strong>Request by :</strong> $leave->first_name $leave->last_name<br>
                    <strong>Period :</strong> $leave->period <br>
                    <strong>Join Date :</strong> $leave->join_date <br>
                    <strong>NIK :</strong> $leave->nik <br>
                    <strong>Position :</strong> $leave->position <br>
                    <strong>Department :</strong> $leave->dept_category_name <br>
                    <strong>Contact Address :</strong> $leave->address <br>
                    <strong>Leave Category :</strong> $leave->leave_category_name <br>
                    <strong>Start Leave :</strong> $leave->leave_date <br>
                    <strong>End Leave :</strong> $leave->end_leave_date <br>
                    <strong>Back to Work :</strong> $leave->back_work <br>
                    <strong>Total Annual :</strong> $leave->pending <br>
                    <strong>Request Day :</strong> $leave->total_day <br>
                    <strong>Total Balance :</strong> $leave->remain <br>
                </div>
                <div class='well'>
                    <h5><strong><u>Additional</u></strong></h5>
                    <strong>Destination :</strong> $leave->r_departure - $leave->r_after_leaving <br>
                    <strong>Reason :</strong> $leave->reason_leave <br> <br>

                    <h5><strong><u>Requested Approval To</u></strong></h5>
                    <strong>FA Manager :</strong> $FaManager <br>
                    <strong>General Manager :</strong> $GenaralManager <br>

                </div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
            </div>
        ";


        return $return;
    }


    public function printLeave($id)
    {
        $select = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->joinInitialLeave()
            ->select([
                '*'
            ])
            ->where('leave_transaction.user_id', '=', Auth::user()->id)
            ->where('leave_transaction.ap_hd', '=', 1)
            //->where('leave_transaction.ap_gm', '=', 1)
            ->where('leave_transaction.ver_hr', '=', 1)
            //->where('leave_transaction.ap_hrd', '=', 5)
            ->find($id);
        view()->share('select', $select);

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadview('leave.print');
        return $pdf->stream();
    }

    public function print1Leave($id)
    {
        $select = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->joinInitialLeave()
            ->select([
                '*'
            ])
            ->where('leave_transaction.user_id', '=', Auth::user()->id)
            //->where('leave_transaction.ap_hd', '=', 1)
            ->where('leave_transaction.ap_hrd', '=', 1)
            ->where('leave_transaction.ver_hr', '=', 1)
            ->find($id);

        view()->share('select', $select);

        // dd($select);

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadview('leave.print');
        return $pdf->stream();
    }

    public function indexOrganitation()
    {

        $avatar = asset("storage/app/prof_pict/no_avatar.jpg");

        return View::make('leave.indexOraganitation', ['no_avatar' => $avatar]);
    }

    public function meeting()
    {
        $project = Project_Category::where('project_name', '!=', 'Operasional')->orderBY('project_name', 'asc')->get();
        return View::make('meeting.meeting', ['project' => $project]);
    }

    public function storemeeting(Request $request)
    {

        $rules = [
            'start_time' => 'required',
            'end_time' => 'required',
            'date' => 'required',
            'job' => 'required|string'
        ];

        $data = [
            'users_id' => Auth::user()->id,
            'room' => $request->input("room"),
            'start_time' => $request->input('date') . ' ' . $request->input("start_time"),
            'end_time' => $request->input('date') . ' ' . $request->input("end_time"),
            'date' => $request->input('date'),
            'Project' => $request->input("job"),
            'request_by' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
            'status' => 1,

        ];

        $validator = validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Redirect::route('meeting')
                ->withErrors($validator)
                ->withInput();
        } else {

            Meeting::insert($data);
            Session::flash('message', Lang::get('messages.data_inserted', ['data' => 'Meeting Room Success']));
            return Redirect::route('meeting');
        }
    }

    public function indexMeetingAudit()
    {
        $meeting = DB::Table("meeting")->get();

        return view::make('meeting.meeting_auditing', ['meet' => $meeting]);
    }

    public function getINdexMeetingAudit()
    {
        $meeting = DB::Table("meeting")->select([
            'id', 'Project', 'request_by', 'start_time', 'end_time', 'date', 'status'
        ])->get();

        return Datatables::of($meeting)
            ->edit_column('start_time', '{{date("H:i:s A", (strtotime($start_time)))}}')
            ->edit_column('end_time', '{{date("H:i:s A", (strtotime($end_time)))}}')
            ->edit_column('date', '{{date("M, d Y", (strtotime($date)))}}')
            ->edit_column('status', '@if($status === 1){{"Active"}} @else {{"Deactive"}} @endif')
            ->add_column(
                'actions',
                Lang::get('messages.btn_success', ['title' => 'Detail', 'url' => '{{ URL::route(\'detailMeeting\', [$id]) }}', 'class' => 'file'])
            )
            ->make();
    }

    public function detailMeeting($id)
    {
        $meeting = Meeting::find($id);
        $t = DB::table('meeting')->where('id', $id)->first();

        $return   = "
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
            </div>
          <form method='POST' action='" . URL::route('postDetailMeeting', [$id]) . "', $t->id)}}'  enctype='multipart/data'>
                " . csrf_field() . "

                <div class='modal-body'>
                 <div class='form-group'>
                <label for='room'>Status Room</label>
                <select class='form-control' id='room' required='true' name='room'>
                     <option value='3'>Select Status</option>
                    <option value='1'>Active</option>
                    <option value='0'>Deactive</option>
                </select>
              </div>
      </div>
      <div class='modal-footer'>
        <button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>
        <button type='submit' class='btn btn-primary'>Save changes</button>
      </div>
      </form>



        ";

        return $return;
    }

    public function postDetailMeeting(request $request, $id)
    {
        $rules = [
            'room' => 'required'
        ];

        $data = [
            'status' => $request->input('room')
        ];

        /* dd($data);*/
        $validator = validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Redirect::route('meeting/audit')
                ->withErrors($validator)
                ->withInput();
        } else {

            Meeting::where('id', '=', $id)->update($data);
            Session::flash('message', Lang::get('messages.data_inserted', ['data' => 'Meeting Room Success']));
            return back();
        }
    }

    public function indexYourProjects()
    {
        return view::make('production.yourProjects');
    }

    public function getGUuide()
    {
        $contents = storage_path("app/pdf/Guide WIS.pdf");

        return response()->download($contents);
    }

    public function guided()
    {
        return view::make('guide.index');
    }

    public function reSendMailLeave($id)
    {
        $email      = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->find($id);

        $hd = User::where('dept_category_id', auth::user()->dept_category_id)->where('hd', 1)->where('active', 1)->first();

        $ver_hr = User::where('hr', 1)->first();

        $data = $email->resendmail - 1;

        if ($email->ap_koor === 0) {
            if ($email->dept_category_id === 6) {
                Mail::send('email.Reminders.pendingLeaveProduction', ['email' => $email], function ($message) use ($email) {
                    $message->to($email->email_koor, 'WIS')->subject('[Approval] Requesting Leave Application - ' . $email->request_by . '');
                    $message->from('wis_system@frameworks-studios.com', 'WIS');
                });
            } else {
                Mail::send('email.Reminders.pendingLeaveOfficer', ['email' => $email], function ($message) use ($email) {
                    $message->to($email->email_koor, 'WIS')->subject('[Approval] Requesting Leave Application - ' . $email->request_by . '');
                    $message->from('wis_system@frameworks-studios.com', 'WIS');
                });
            }
        } elseif ($email->ap_spv === 0) {
            if ($email->dept_category_id === 6) {
                Mail::send('email.Reminders.pendingLeaveProduction', ['email' => $email], function ($message) use ($email) {
                    $message->to($email->email_spv, 'WIS')->subject('[Approval] Requesting Leave Application - ' . $email->request_by . '');
                    $message->from('wis_system@frameworks-studios.com', 'WIS');
                });
            } else {
                Mail::send('email.Reminders.pendingLeaveOfficer', ['email' => $email], function ($message) use ($email) {
                    $message->to($email->email_pm, 'WIS')->subject('[Approval] Requesting Leave Application - ' . $email->request_by . '');
                    $message->from('wis_system@frameworks-studios.com', 'WIS');
                });
            }
        } elseif ($email->ap_pm === 0) {
            if ($email->dept_category_id === 6) {
                Mail::send('email.Reminders.pendingLeaveProduction', ['email' => $email], function ($message) use ($email) {
                    $message->to($email->email_pm, 'WIS')->subject('[Approval] Requesting Leave Application - ' . $email->request_by . '');
                    $message->from('wis_system@frameworks-studios.com', 'WIS');
                });
            } else {
                Mail::send('email.Reminders.pendingLeaveOfficer', ['email' => $email], function ($message) use ($email) {
                    $message->to($email->email_pm, 'WIS')->subject('[Approval] Requesting Leave Application - ' . $email->request_by . '');
                    $message->from('wis_system@frameworks-studios.com', 'WIS');
                });
            }
        } elseif ($email->ap_producer === 0) {
            if ($email->dept_category_id === 6) {
                Mail::send('email.Reminders.pendingLeaveProduction', ['email' => $email], function ($message) use ($email) {
                    $message->to($email->email_producer, 'WIS')->subject('[Approval] Requesting Leave Application - ' . $email->request_by . '');
                    $message->from('wis_system@frameworks-studios.com', 'WIS');
                });
            } else {
                Mail::send('email.Reminders.pendingLeaveOfficer', ['email' => $email], function ($message) use ($email) {
                    $message->to($email->email_producer, 'WIS')->subject('[Approval] Requesting Leave Application - ' . $email->request_by . '');
                    $message->from('wis_system@frameworks-studios.com', 'WIS');
                });
            }
        } elseif ($email->ap_hd === 0) {
            if ($email->dept_category_id === 6) {
                Mail::send('email.Reminders.pendingLeaveProduction', ['email' => $email], function ($message) use ($email, $hd) {
                    $message->to($hd->email, 'WIS')->subject('[Approval] Requesting Leave Application - ' . $email->request_by . '');
                    $message->from('wis_system@frameworks-studios.com', 'WIS');
                });
            } else {
                Mail::send('email.Reminders.pendingLeaveOfficer', ['email' => $email], function ($message) use ($email, $hd) {
                    $message->to($hd->email, 'WIS')->subject('[Approval] Requesting Leave Application - ' . $email->request_by . '');
                    $message->from('wis_system@frameworks-studios.com', 'WIS');
                });
            }
        } elseif ($email->ver_hr === 0) {
            if ($email->dept_category_id === 6) {
                Mail::send('email.verMail', ['email' => $email], function ($message) use ($email, $ver_hr) {
                    $message->to($ver_hr->email, 'WIS')->subject('[Verify] Requesting Leave Application - ' . $email->request_by . '');
                    $message->from('wis_system@frameworks-studios.com', 'WIS');
                });
            } else {
                Mail::send('email.verMail', ['email' => $email], function ($message) use ($email, $ver_hr) {
                    $message->to($ver_hr->email, 'WIS')->subject('[Verify] Requesting Leave Application - ' . $email->request_by . '');
                    $message->from('wis_system@frameworks-studios.com', 'WIS');
                });
            }
        } elseif ($email->ap_hrd === 0) {
            if ($email->dept_category_id === 6) {
                Mail::send('email.Reminders.pendingLeaveProduction', ['email' => $email], function ($message) use ($email) {
                    $message->to('wahyuni.hasan@frameworks-studios.com', 'WIS')->subject('[Approval] Requesting Leave Application - ' . $email->request_by . '');
                    $message->from('wis_system@frameworks-studios.com', 'WIS');
                });
            } else {
                Mail::send('email.Reminders.pendingLeaveOfficer', ['email' => $email], function ($message) use ($email) {
                    $message->to('wahyuni.hasan@frameworks-studios.com', 'WIS')->subject('[Approval] Requesting Leave Application - ' . $email->request_by . '');
                    $message->from('wis_system@frameworks-studios.com', 'WIS');
                });
            }
        }
        Leave::where('id', $id)->update([
            'resendmail' => $data
        ]);
        Session::flash('message', Lang::get('messages.resendmail', ['data' => 'leave']));
        return redirect()->route('leave/transaction');
    }

    public function resendMailLeaveHod($id)
    {
        $email = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->find($id);

        $FaManager = User::where('email', $email->email_pm)->where('dept_ApprovedHOD', 1)->where('active', 1)->first();

        $GM = User::where('email', $email->email_producer)->where('gm', 1)->where('active', 1)->first();

        $verify = User::where('hr', 1)->where('nik', null)->first();

        $hr = User::where('dept_category_id', 3)->where('hd', 1)->where('active', 1)->first();

        $data = $email->resendmail - 1;

        if ($data < 0) {
            Session::flash('getError', Lang::get('messages.resendmailErr'));
            return redirect()->route('leave/transaction');
        }

        if ($email->resendmail <= 2) {

            if ($email->ap_producer == 0 and $email->ap_gm == 0) {
                Mail::send('email.Reminders.pendingLeaveOfficer', ['email' => $email], function ($message) use ($email) {
                    $message->to($email->email_pm, 'WIS')->subject('[Approval] Requesting Leave Application - ' . $email->request_by . '');
                    $message->from('wis_system@frameworks-studios.com', 'WIS');
                });
            }

            if ($email->ap_producer == 1 and $email->ap_gm == 0) {
                Mail::send('email.Reminders.pendingLeaveOfficer', ['email' => $email], function ($message) use ($email) {
                    $message->to($email->email_producer, 'WIS')->subject('[Approval] Requesting Leave Application - ' . $email->request_by . '');
                    $message->from('wis_system@frameworks-studios.com', 'WIS');
                });
            }

            if ($email->ap_gm == 1 and $email->ver_hr == 0) {
                Mail::send('email.Reminders.pendingLeaveOfficer', ['email' => $email], function ($message) use ($email, $verify) {
                    $message->to($verify->email)->subject('[Approval] Requesting Leave Application - ' . $email->request_by . '');
                    $message->from('wis_system@frameworks-studios.com', 'WIS');
                });
            }

            if ($email->ver_hr == 1 and $email->ap_hrd == 0) {
                Mail::send('email.Reminders.pendingLeaveOfficer', ['email' => $email], function ($message) use ($email, $hr) {
                    $message->to($hr->email)->subject('[Approval] Requesting Leave Application - ' . $email->request_by . '');
                    $message->from('wis_system@frameworks-studios.com', 'WIS');
                });
            }
        }

        Leave::where('id', $id)->update([
            'resendmail' => $data
        ]);
        Session::flash('message', Lang::get('messages.resendmail', ['data' => 'leave']));
        return redirect()->route('leave/transaction');
    }

    public function getIndexLeaveTransactionHRD()
    {
        $select = Leave::joinUsers()->joinDeptCategory()->joinLeaveCategory()->select([
            'leave_transaction.id',
            'users.nik',
            'leave_transaction.request_by',
            'leave_category.leave_category_name',
            'leave_transaction.leave_date',
            'leave_transaction.total_day',
            'leave_transaction.ver_hr',
            'leave_transaction.ap_hrd',
            'leave_transaction.resendmail',
            'leave_transaction.req_advance'
        ])
            ->where('leave_transaction.user_id', auth::user()->id)
            ->get();

        return Datatables::of($select)
            ->edit_column('ver_hr', '@if($ver_hr === 1){{"VERIFIED"}} @elseif($ver_hr === 2){{"UNVERIFIED"}} @elseif($ver_hr === 0){{"PENDING"}} @else {{"--"}} @endif')
            ->edit_column('ap_hrd', '@if($ap_hrd === 1){{"APPROVED"}} @elseif($ap_hrd === 2){{"DISAPPROVED"}} @elseif($ver_hr === 1){{"PENDING"}} @elseif($ver_hr === 0){{"WAITING VERIFY"}} @else {{"--"}} @endif')
            ->edit_column('leave_date', '{!! date("M d, Y", strtotime($leave_date)) !!}')
            ->edit_column('leave_category_name', '@if($req_advance === 2){{"Forfeited"}} @else {{$leave_category_name}} @endif')
            ->setRowClass('@if ($req_advance !== 0){{ "danger" }}@endif')
            ->add_column(
                'actions',
                Lang::get('messages.btn_success', ['title' => 'Detail', 'url' => '{{ URL::route(\'leave/detail\', [$id]) }}', 'class' => 'file']) .
                    '@if ($ap_hrd === 1 && $ver_hr === 1)'
                    . Lang::get('messages.btn_print', ['title' => 'Print', 'url' => '{{ URL::route(\'leave/print1\', [$id]) }}', 'class' => 'print'])
                    . '@endif'
            )
            ->add_column(
                'sendmails',
                '@if($resendmail <= 2 and $resendmail > 0)' . Lang::get('messages.btn_resendmail', ['title' => 'send back email notification x{{ $resendmail }}', 'url' => '{{ URL::route(\'leave/reSendMailLeave\', [$id]) }}']) . '@endif'
            )
            ->add_column(
                'frase',
                '@if ($ver_hr === 0)'
                    . Lang::get('messages.btn_danger', ['url' => '{{ URL::route(\'leave/delete/form/Officer\', [$id]) }}', 'data' => 'this eform'])
                    . '@endif'

            )
            ->make();
    }

    public function coverImage()
    {
        Storage::url('app/prof_pict/' . Auth::user()->prof_pict);

        return asset($k);
    }
}
