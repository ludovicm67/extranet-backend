<?php

namespace App\Http\Controllers;

use Laravel\Scout\Searchable;
use App\SellsyClient;
use Illuminate\Http\Request;

class SellsyClientController extends Controller
{
  use Searchable;
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
     * @param  \App\SellsyClient  $sellsySellsyClient
     * @return \Illuminate\Http\Response
     */
    public function show(SellsyClient $sellsySellsyClient)
    {
      return response()->json([
        'success' => true,
        'data' => $sellsySellsyClient->fresh([
          'contacts', 'orders', 'subscriptions', 'projects'
        ]),
      ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SellsyClient  $sellsySellsyClient
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SellsyClient $sellsySellsyClient)
    {
      abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SellsyClient  $sellsySellsyClient
     * @return \Illuminate\Http\Response
     */
    public function destroy(SellsyClient $sellsySellsyClient)
    {
      abort(404);
    }
}
