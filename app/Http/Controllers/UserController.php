<?php

namespace App\Http\Controllers;

use Validator;
use App\User;
use App\Mail\ResetPassword;
use App\ResetPassword as Pass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Ramsey\Uuid\Uuid;

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

    public function resetPassword(Request $request) {

      // first of all, delete all old records
      $date = new \DateTime();
      $date->modify('-1 hour');
      $formatted = $date->format('Y-m-d H:i:s');
      Pass::where('updated_at', '<=', $formatted)->delete();

      // then, validate the email
      $validator = Validator::make($request->all(), [
        'email' => 'required|email|max:255|exists:users,email',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors' => $validator->errors()->all(),
        ], 400);
      }

      // a user with this email exists, so we get it
      $user = User::where('email', $request->email)->first();

      if (empty($request->token)) {
        // we generate a token, create a DB entry and send the mail
        $token = Uuid::uuid4()->toString();
        $pass = Pass::create([
          'user_id' => $user->id,
          'token' => $token,
        ]);

        Mail::to($user->email)->send(new ResetPassword($pass));

        return response()->json([
          'success' => true,
          'message' => 'mail containing the token was send to the user',
        ]);
      }

      // validate other fields
      $validator = Validator::make($request->all(), [
        'token' => 'required|max:255|exists:reset_password,token',
        'password' => 'required|min:1|max:255',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors' => $validator->errors()->all(),
        ], 400);
      }

      $user->password = bcrypt(trim($request->password));
      $user->save();

      $pass = Pass::where('user_id', $user->id)->delete();

      return response()->json([
        'success' => true,
        'message' => 'new password successfully set, you can now log in.',
      ]);
    }
}
