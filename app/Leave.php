<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

	class Leave extends Model
	{
		protected $table = 'leave_transaction';

		public function scopeJoinUsers($query)
		{
			return $query->leftjoin('users', 'users.id', '=', 'leave_transaction.user_id');
		}

		public function scopeJoinLeaveCategory($query)
		{
			return $query->leftjoin('leave_category', 'leave_category.id', '=', 'leave_transaction.leave_category_id');
		}

		public function scopeJoinDeptCategory($query)
	    {
	        return $query->leftjoin('dept_category', 'dept_category.id', '=', 'users.dept_category_id');
	    }

		public function scopeJoinInitialLeave($query)
	    {
	        return $query->leftjoin('initial_leave', 'users.id', '=', 'initial_leave.user_id');
	    }

		public function scopeJoinProjectCategory($query)
	    {
	        return $query->leftjoin('project_category', 'users.project_category_id_1', '=', 'project_category.id');
	    }


	}