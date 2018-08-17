<?php

namespace App\Http\Controllers;

use Validator;
use App\Identifier;
use Illuminate\Http\Request;

class IdentifierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $this->needPermission('identifiers', 'show');
      return response()->json([
        'success' => true,
        'data' => Identifier::all(),
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
      $this->needPermission('identifiers', 'add');
      $validator = Validator::make($request->all(), [
        'name' => 'required|max:255|unique:identifiers',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'message' => 'name is required and should be unique',
        ], 409);
      }

      $identifier = new Identifier;
      $identifier->name = $request->name;
      $identifier->save();

      return response()->json([
        'success' => true,
      ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Identifier  $identifier
     * @return \Illuminate\Http\Response
     */
    public function show(Identifier $identifier)
    {
      $this->needPermission('identifiers', 'show');
      return response()->json([
        'success' => true,
        'data' => $identifier
      ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Identifier  $identifier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Identifier $identifier)
    {
      $this->needPermission('identifiers', 'edit');
      $validator = Validator::make($request->all(), [
        'name' => 'required|max:255|unique:identifiers,name,' . $identifier->id,
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'message' => 'name should be unique',
        ], 409);
      }

      $identifier->name = $request->name;
      $identifier->save();

      return response()->json([
        'success' => true,
      ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Identifier  $identifier
     * @return \Illuminate\Http\Response
     */
    public function destroy(Identifier $identifier)
    {
      $this->needPermission('identifiers', 'delete');

      $identifier->delete();

      return response()->json([
        'success' => true,
      ]);
    }
}
