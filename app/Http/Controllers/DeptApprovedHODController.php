<?php

namespace App\Http\Controllers;

use App\Dept_Category;
use App\Leave;
use App\NewUser;
use App\User;

use Datatables;
use Illuminate\Http\Request;

class DeptApprovedHODController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'active', 'deptApprovedHOD']);
    }

    public function index()
    {
        return view('HeadOfDepartment.DeptApprovedHod.index');
    }

    public function dataIndex()
    {
        $data = Leave::JoinUsers()->JoinLeaveCategory()->select(['leave_transaction.*', 'users.active', 'users.hd', 'leave_category.leave_category_name'])
            ->where('users.hd', 1)
            ->where('users.active', 1)
            ->where('leave_transaction.ap_producer', 0)
            ->where('leave_transaction.ap_hd', 1)
            ->orderBy('updated_at', 'desc')
            ->get();

        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('ap_producer', '@if ($ap_producer == 0) {{ "Pending" }} @else {{ "--" }} @endif')
            ->editColumn('ap_gm', '@if ($ap_producer == 0 && $ap_gm == 0) {{ "Waiting FA Manager" }} @else {{ "--" }} @endif')
            ->make(true);
    }
}
