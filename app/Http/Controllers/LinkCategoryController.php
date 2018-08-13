<?php

namespace App\Http\Controllers;

use Validator;
use App\LinkCategory;
use Illuminate\Http\Request;

class LinkCategoryController extends Controller
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
        'data' => LinkCategory::all(),
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
      $validator = Validator::make($request->all(), [
        'name' => 'required|max:255|unique:types',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'message' => 'name is required and should be unique',
        ], 409);
      }

      $type = new LinkCategory;
      $type->name = $request->name;
      $type->save();

      return response()->json([
        'success' => true,
      ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\LinkCategory  $linkCategory
     * @return \Illuminate\Http\Response
     */
    public function show(LinkCategory $linkCategory)
    {
      return response()->json([
        'success' => true,
        'data' => $linkCategory
      ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\LinkCategory  $linkCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LinkCategory $linkCategory)
    {
      $validator = Validator::make($request->all(), [
        'name' => 'required|max:255|unique:link_categories,name,' . $linkCategory->id,
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'message' => 'name should be unique',
        ], 409);
      }

      $linkCategory->name = $request->name;
      $linkCategory->save();

      return response()->json([
        'success' => true,
      ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\LinkCategory  $linkCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(LinkCategory $linkCategory)
    {
      $linkCategory->delete();

      return response()->json([
        'success' => true,
      ]);
    }
}
