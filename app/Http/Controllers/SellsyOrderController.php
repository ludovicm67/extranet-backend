<?php

namespace App\Http\Controllers;

use App\SellsyOrder;
use Illuminate\Http\Request;

class SellsyOrderController extends Controller
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
        'data' => SellsyOrder::all(),
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
     * @param  \App\SellsyOrder  $sellsyOrder
     * @return \Illuminate\Http\Response
     */
    public function show(SellsyOrder $sellsyOrder)
    {
      return response()->json([
        'success' => true,
        'data' => $sellsyOrder,
      ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SellsyOrder  $sellsyOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SellsyOrder $sellsyOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SellsyOrder  $sellsyOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(SellsyOrder $sellsyOrder)
    {
        //
    }
}
