<?php

namespace App\Http\Controllers;

use App\SellsyInvoice;
use Illuminate\Http\Request;

class SellsyInvoiceController extends Controller
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
        'data' => SellsyInvoice::all(),
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
     * @param  \App\SellsyInvoice  $sellsyInvoice
     * @return \Illuminate\Http\Response
     */
    public function show(SellsyInvoice $sellsyInvoice)
    {
      return response()->json([
        'success' => true,
        'data' => $sellsyInvoice,
      ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SellsyInvoice  $sellsyInvoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SellsyInvoice $sellsyInvoice)
    {
      abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SellsyInvoice  $sellsyInvoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(SellsyInvoice $sellsyInvoice)
    {
      abort(404);
    }
}
