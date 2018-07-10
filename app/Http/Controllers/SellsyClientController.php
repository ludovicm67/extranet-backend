<?php

namespace App\Http\Controllers;

use App\SellsyClient;
use Illuminate\Http\Request;

class SellsyClientController extends Controller
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
        'data' => SellsyClient::all(),
      ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      abort(404);
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
     * @param  \App\SellsyClient  $sellsyClient
     * @return \Illuminate\Http\Response
     */
    public function show(SellsyClient $sellsyClient)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SellsyClient  $sellsyClient
     * @return \Illuminate\Http\Response
     */
    public function edit(SellsyClient $sellsyClient)
    {
      abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SellsyClient  $sellsyClient
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SellsyClient $sellsyClient)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SellsyClient  $sellsyClient
     * @return \Illuminate\Http\Response
     */
    public function destroy(SellsyClient $sellsyClient)
    {
        //
    }
}
