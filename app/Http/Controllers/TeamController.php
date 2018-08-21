<?php

namespace App\Http\Controllers;

use Validator;
use App\User;
use App\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $this->needPermission('users', 'show');
      return response()->json([
        'success' => true,
        'data' => Team::all(),
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
        'name' => 'required|max:255|unique:teams',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'message' => 'name is required and should be unique',
        ], 409);
      }

      $color = '#000000';
      if (!empty($request->color)) {
        $color = $request->color;
      }

      $team = new Team;
      $team->name = $request->name;
      $team->color = $color;
      $team->save();

      return response()->json([
        'success' => true,
      ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function show(Team $team)
    {
      $this->needPermission('users', 'show');
      return response()->json([
        'success' => true,
        'data' => $team
      ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Team $team)
    {
      $this->needPermission('users', 'edit');
      $validator = Validator::make($request->all(), [
        'name' => 'required|max:255|unique:teams,name,' . $team->id,
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'message' => 'name should be unique',
        ], 409);
      }

      $color = '#000000';
      if (!empty($request->color)) {
        $color = $request->color;
      }

      $team->name = $request->name;
      $team->color = $color;
      $team->save();

      return response()->json([
        'success' => true,
      ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team)
    {
      $this->needPermission('users', 'delete');
      User::where('team_id', $team->id)->update([
        'team_id' => null,
      ]);
      $team->delete();

      return response()->json([
        'success' => true,
      ]);
    }
}
