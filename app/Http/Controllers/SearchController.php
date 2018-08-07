<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
  public function index(Request $request) {
    $search = '%' . $request->q . '%';
    $users = User::where('firstname', 'like', $search)
              ->orWhere('lastname', 'like', $search)
              ->orWhere('email', 'like', $search)->get();

    return response()->json([
      'users' => $users,
    ]);
  }
}
