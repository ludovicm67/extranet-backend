<?php

namespace App\Http\Controllers;

use App\SellsyContact;
use Illuminate\Http\Request;

class SellsyContactController extends Controller
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
        'data' => SellsyContact::all(),
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
      abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SellsyContact  $sellsyContact
     * @return \Illuminate\Http\Response
     */
    public function show(SellsyContact $sellsyContact)
    {
      return response()->json([
        'success' => true,
        'data' => $sellsyContact,
      ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SellsyContact  $sellsyContact
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SellsyContact $sellsyContact)
    {
      abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SellsyContact  $sellsyContact
     * @return \Illuminate\Http\Response
     */
    public function destroy(SellsyContact $sellsyContact)
    {
      abort(404);
    }
}
