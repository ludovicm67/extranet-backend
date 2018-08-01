<?php

namespace App\Http\Controllers;

use App\Overtime;
use App\User;
use Illuminate\Http\Request;

class OvertimeController extends Controller
{
  public function get(Request $request, User $user) {
    $year = intval($request->input('year', date('Y')));
    $month = intval($request->input('month', date('n')));
    if ($month < 1) {
      $month = 1;
    } else if ($month > 12) {
      $month = 12;
    }
    if ($year < 1900 || $year > 2100) {
      $year = intval(date('Y'));
    }

    $overtime = Overtime::where('user_id', $user->id)
      ->where('year', $year)
      ->where('month', $month)
      ->first();

    if (empty($overtime)) {
      $overtime = Overtime::create([
        'user_id' => $user->id,
        'year' => $year,
        'month' => $month,
      ]);
    }

    return response()->json([
      'success' => true,
      'data' => $overtime,
    ]);
  }

  public function set(Request $request, User $user) {
    $year = intval($request->input('year', date('Y')));
    $month = intval($request->input('month', date('n')));
    if ($month < 1) {
      $month = 1;
    } else if ($month > 12) {
      $month = 12;
    }
    if ($year < 1900 || $year > 2100) {
      $year = intval(date('Y'));
    }

    $overtime = Overtime::where('user_id', $user->id)
      ->where('year', $year)
      ->where('month', $month)
      ->first();

    if (empty($overtime)) {
      $overtime = Overtime::create([
        'user_id' => $user->id,
        'year' => $year,
        'month' => $month,
        'volume' => intval($request->volume),
        'details' => $request->details,
      ]);
    } else {
      $overtime->update([
        'volume' => intval($request->volume),
        'details' => $request->details,
      ]);
    }

    return response()->json([
      'success' => true,
    ]);
  }
}
