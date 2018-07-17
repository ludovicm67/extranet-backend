<?php

namespace App\Http\Controllers;

use Validator;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      return response()->json([
        'success' => true,
        'data' => User::all(),
      ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
      return response()->json([
        'success' => true,
        'data' => $user,
      ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    public function updateMe(Request $request) {
      $user = auth()->user();

      $validator = Validator::make($request->all(), [
        'firstname' => 'required|string|max:255',
        'lastname' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email,' . $user->id,
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors' => $validator->errors()->all(),
        ], 400);
      }

      // if user wanted to change his password
      if (!empty($request->password)) {
        $user->password = bcrypt(trim($request->password));
      }
      $user->firstname = e($request->firstname);
      $user->lastname = e($request->lastname);
      $user->email = e($request->email);
      $user->default_page = e(strip_tags($request->default_page));
      $user->save();

      return response()->json([
        'success' => true,
        'data' => $user,
      ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
      if (auth()->user()->id != $user->id) {
        $user->delete();

        return response()->json([
          'success' => true,
        ]);
      }

      return response()->json([
        'success' => false,
        'message' => 'you cannot delete yourself!',
      ], 403);
    }
}
