<?php

namespace App\Http\Controllers;

use App\Expense;
use App\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
  public function index() {
    $user = auth()->user();

    if ($user->can('leave', 'show')) {
      $leave = Leave::with(['user'])
        ->orderByRaw('CASE accepted WHEN 0 THEN 1 WHEN 1 THEN 2 WHEN -1 THEN 3 END', 'asc')
        ->orderBy('updated_at', 'desc')
        ->get();
    } else {
      $leave = Leave::with(['user'])
        ->where('user_id', $user->id)
        ->orderByRaw('CASE accepted WHEN 0 THEN 1 WHEN 1 THEN 2 WHEN -1 THEN 3 END', 'asc')
        ->orderBy('updated_at', 'desc')
        ->get();
    }

    if ($user->can('expenses', 'show')) {
      $expenses = Expense::with(['user'])
        ->orderByRaw('CASE accepted WHEN 0 THEN 1 WHEN 1 THEN 2 WHEN -1 THEN 3 END', 'asc')
        ->orderBy('updated_at', 'desc')
        ->get();
    } else {
      $expenses = Expense::with(['user'])
        ->where('user_id', $user->id)
        ->orderByRaw('CASE accepted WHEN 0 THEN 1 WHEN 1 THEN 2 WHEN -1 THEN 3 END', 'asc')
        ->orderBy('updated_at', 'desc')
        ->get();
    }

    $data = [];

    foreach ($leave as $l) {
      $userFirstname = isset($l->user) && isset($l->user->firstname) ? $l->user->firstname : '';
      $userLastname = isset($l->user) && isset($l->user->lastname) ? $l->user->lastname : '';
      $userEmail = isset($l->user) && isset($l->user->email) ? $l->user->email : '';

      $data[] = [
        'id' => $l->id,
        'request_type' => 'leave',

        'user_id' => $l->user_id,

        'firstname' => $userFirstname,
        'lastname' => $userLastname,
        'email' => $userEmail,

        'file' => $l->file,
        'accepted' => $l->accepted,
        'details' => $l->details,
        'created_at' => $l->created_at,
        'updated_at' => $l->updated_at,
        'category' => $l->reason,

        'leave_start' => $l->start,
        'leave_end' => $l->end,
        'leave_days' => $l->days,

        'expense_month' => null,
        'expense_year' => null,
        'expense_amount' => null,
      ];
    }

    foreach ($expenses as $e) {
      $userFirstname = isset($e->user) && isset($e->user->firstname) ? $e->user->firstname : '';
      $userLastname = isset($e->user) && isset($e->user->lastname) ? $e->user->lastname : '';
      $userEmail = isset($e->user) && isset($e->user->email) ? $e->user->email : '';

      $data[] = [
        'id' => $e->id,
        'request_type' => 'expenses',

        'user_id' => $e->user_id,

        'firstname' => $userFirstname,
        'lastname' => $userLastname,
        'email' => $userEmail,

        'file' => $e->file,
        'accepted' => $e->accepted,
        'details' => $e->details,
        'created_at' => $e->created_at,
        'updated_at' => $e->updated_at,
        'category' => $e->reason,

        'leave_start' => null,
        'leave_end' => null,
        'leave_days' => null,

        'expense_month' => $e->month,
        'expense_year' => $e->year,
        'expense_amount' => $e->amount,
      ];
    }

    $data = collect($data)->sortByDesc(function ($req, $key) {
      switch ($req['accepted']) {
        case 0:
          return 3 . '-' . $req['updated_at'];
        case 1:
          return 2 . '-' . $req['updated_at'];
        case -1:
          return 1 . '-' . $req['updated_at'];
        default:
          return 0 . '-' . $req['updated_at'];
      }
    })->values()->all();

    return response()->json([
      'success' => true,
      'data' => $data,
    ]);
  }

  public function pending() {
    $this->needPermission('request_management', 'edit');

    return response()->json([
      'success' => true,
      'data' => [
        'expenses' => Expense::where('accepted', 0)->with('user')->get(),
        'leave' => Leave::where('accepted', 0)->with('user')->get(),
      ],
    ]);
  }
}
