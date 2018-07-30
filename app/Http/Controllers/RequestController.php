<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
  public function index() {
    $leave = DB::table('leave')
      ->select(
        'leave.id',
        DB::raw("'leave' as request_type"),
        'leave.user_id',
        'users.firstname',
        'users.lastname',
        'users.email',
        'leave.file',
        'leave.accepted',
        'leave.details',
        'leave.created_at',
        'leave.updated_at',
        'leave.reason as category',

        'leave.start as leave_start',
        'leave.end as leave_end',
        'leave.days as amount',

        DB::raw('NULL as expense_month'),
        DB::raw('NULL as expense_year'),
        DB::raw('NULL as expense_amount')
      )
      ->join('users', 'users.id', '=', 'leave.user_id');

    $req = DB::table('expenses')
      ->select(
        'expenses.id',
        DB::raw("'expenses' as request_type"),
        'expenses.user_id',
        'users.firstname',
        'users.lastname',
        'users.email',
        'expenses.file',
        'expenses.accepted',
        'expenses.details',
        'expenses.created_at',
        'expenses.updated_at',
        'expenses.type as category',

        DB::raw('NULL as leave_start'),
        DB::raw('NULL as leave_end'),
        DB::raw('NULL as leave_days'),

        'expenses.month as expense_month',
        'expenses.year as expense_year',
        'expenses.amount as amount'
      )
      ->join('users', 'users.id', '=', 'user_id')
      ->unionAll($leave)
      ->orderByRaw('CASE accepted WHEN 0 THEN 1 WHEN 1 THEN 2 WHEN -1 THEN 3 END', 'asc')
      ->orderBy('updated_at', 'desc');

    return response()->json([
      'success' => true,
      'data' => $req->get(),
    ]);
  }
}
