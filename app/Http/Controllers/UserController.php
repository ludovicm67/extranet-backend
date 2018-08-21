<?php

namespace App\Http\Controllers;

use Validator;
use App\User;
use App\Role;
use App\Team;
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
      // $this->needPermission('users', 'show');
      return response()->json([
        'success' => true,
        'data' => User::all(),
      ]);
    }

    public function team()
    {
      $data = array_values(User::with(['leave', 'team', 'leave.user'])->get()->sortBy('team.id')->toArray());

      $user = auth()->user();
      if (!$user->can('leave', 'show')) {
        foreach ($data as $key => $value) {
          if ($value['id'] != $user->id) {
            $data[$key]['leave'] = [];
          }
        }
      }

      return response()->json([
        'success' => true,
        'data' => $data,
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
      $this->needPermission('users', 'add');
      $validator = Validator::make($request->all(), [
        'firstname' => 'required|string|max:255',
        'lastname' => 'required|string|max:255',
        'password' => 'required|string|min:1|max:255',
        'email' => 'required|email|max:255|unique:users,email',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors' => $validator->errors()->all(),
        ], 400);
      }

      // basic informations
      $userPassword = bcrypt(trim($request->password));
      $userFirstname = $request->firstname;
      $userLastname = $request->lastname;
      $userEmail = $request->email;
      $userDefaultPage = strip_tags($request->default_page);
      $userIsAdmin = 0;
      $userRoleId = null;
      $userTeamId = null;

      // if user is admin
      if ($request->role_id == -1) {
        $userIsAdmin = 1;
      } else if (!empty($request->role_id)) {
        $role = Role::find($request->role_id);

        // if no corresponding role was found, create one
        if (empty($role)) {
          $role = Role::where('name', $request->role_id)->first();
          if (empty($role)) {
            $role = Role::create([
              'name' => $request->role_id,
            ]);
          }
        }
        $userRoleId = $role->id;
      }

      if (!empty($request->team_id)) {
        $team = Team::find($request->team_id);

        // if no corresponding team was found, create one
        if (empty($team)) {
          $team = Team::where('name', $request->team_id)->first();
          if (empty($team)) {
            $team = Team::create([
              'name' => $request->team_id,
            ]);
          }
        }
        $userTeamId = $team->id;
      }

      User::create([
        'password' => $userPassword,
        'firstname' => $userFirstname,
        'lastname' => $userLastname,
        'email' => $userEmail,
        'default_page' => $userDefaultPage,
        'is_admin' => $userIsAdmin,
        'role_id' => $userRoleId,
        'team_id' => $userTeamId,
      ]);

      return response()->json([
        'success' => true,
      ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
      $this->needPermission('users', 'show');

      $authUser = auth()->user();
      if ((!$authUser || !$authUser->can('documents', 'show')) && $user->id != $authUser->id) {
        $data = $user->fresh(['team']);
        $data->documents = [];
      } else {
        $data = $user->fresh(['documents', 'team']);
      }

      return response()->json([
        'success' => true,
        'data' => $data,
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
      $this->needPermission('users', 'edit');
      $me = auth()->user();
      $isMe = $user->id === $me->id;

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

      // if we need to change the password
      if (!empty($request->password)) {
        $user->password = bcrypt(trim($request->password));
      }
      $user->firstname = $request->firstname;
      $user->lastname = $request->lastname;
      $user->email = $request->email;
      $user->default_page = strip_tags($request->default_page);

      // if user is admin
      if ($request->role_id == -1) {
        $user->is_admin = 1;
        $user->role_id = null;
      } else if (empty($request->role_id)) {
        $user->role_id = null;
        if (!$isMe) {
          $user->is_admin = 0;
        }
      } else {
        $role = Role::find($request->role_id);

        // if no corresponding role was found, create one
        if (empty($role)) {
          $role = Role::where('name', $request->role_id)->first();
          if (empty($role)) {
            $role = Role::create([
              'name' => $request->role_id,
            ]);
          }
        }
        $user->role_id = $role->id;

        if (!$isMe) {
          $user->is_admin = 0;
        }
      }

      if (!empty($request->team_id)) {
        $team = Team::find($request->team_id);

        // if no corresponding team was found, create one
        if (empty($team)) {
          $team = Team::where('name', $request->team_id)->first();
          if (empty($team)) {
            $team = Team::create([
              'name' => $request->team_id,
            ]);
          }
        }
        $user->team_id = $team->id;
      } else {
        $user->team_id = null;
      }

      $user->save();

      // if I'm editing y account, sends the new data
      $data = ($isMe) ? $user : (object) [];
      $data->isMe = $isMe;

      return response()->json([
        'success' => true,
        'data' => $data,
      ]);
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
      $user->firstname = $request->firstname;
      $user->lastname = $request->lastname;
      $user->email = $request->email;
      $user->default_page = strip_tags($request->default_page);
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
      $this->needPermission('users', 'delete');
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
        // we remove all previous entries for this user
        $pass = Pass::where('user_id', $user->id)->delete();

        // we generate a token, create a DB entry and send the mail
        $token = explode('-', Uuid::uuid4()->toString())[1];
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
