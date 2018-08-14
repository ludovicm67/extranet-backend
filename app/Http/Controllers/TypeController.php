<?php

namespace App\Http\Controllers;

use Validator;
use App\Contact;
use App\Type;
use Illuminate\Http\Request;

class TypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $this->needPermission('contacts', 'show');
      return response()->json([
        'success' => true,
        'data' => Type::all(),
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
      $this->needPermission('contacts', 'add');
      $validator = Validator::make($request->all(), [
        'name' => 'required|max:255|unique:types',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'message' => 'name is required and should be unique',
        ], 409);
      }

      $type = new Type;
      $type->name = $request->name;
      $type->save();

      return response()->json([
        'success' => true,
      ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Type  $type
     * @return \Illuminate\Http\Response
     */
    public function show(Type $type)
    {
      $this->needPermission('contacts', 'show');
      return response()->json([
        'success' => true,
        'data' => $type
      ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Type  $type
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Type $type)
    {
      $this->needPermission('contacts', 'edit');
      $validator = Validator::make($request->all(), [
        'name' => 'required|max:255|unique:types,name,' . $type->id,
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'message' => 'name should be unique',
        ], 409);
      }

      $type->name = $request->name;
      $type->save();

      return response()->json([
        'success' => true,
      ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Type  $type
     * @return \Illuminate\Http\Response
     */
    public function destroy(Type $type)
    {
      $this->needPermission('contacts', 'delete');
      Contact::where('type_id', $type->id)->update([
        'type_id' => null,
      ]);
      $type->delete();

      return response()->json([
        'success' => true,
      ]);
    }
}
