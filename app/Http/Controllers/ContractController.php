<?php

namespace App\Http\Controllers;

use Validator;
use App\Contract;
use Illuminate\Http\Request;

class ContractController extends Controller
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
        'data' => Contract::all(),
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
        'user_id' => 'exists:users,id',
        'type' => 'required|string',
        'start_at' => 'required',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors' => $validator->errors()->all(),
        ], 400);
      }

      $startAt = $request->start_at
        ? date('Y-m-d', strtotime($request->start_at))
        : null;
      $endAt = $request->end_at
        ? date('Y-m-d', strtotime($request->end_at))
        : null;

      Contract::create([
        'user_id' => $request->user_id,
        'type' => $request->type,
        'start_at' => $startAt,
        'end_at' => $endAt,
      ]);

      return response()->json([
        'success' => true,
      ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function show(Contract $contract)
    {
      return response()->json([
        'success' => true,
        'data' => $contract,
      ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contract $contract)
    {
      $validator = Validator::make($request->all(), [
        'user_id' => 'exists:users,id',
        'type' => 'required|string',
        'start_at' => 'required',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors' => $validator->errors()->all(),
        ], 400);
      }

      $startAt = $request->start_at
        ? date('Y-m-d', strtotime($request->start_at))
        : null;
      $endAt = $request->end_at
        ? date('Y-m-d', strtotime($request->end_at))
        : null;

      $contract->update([
        'user_id' => $request->user_id,
        'type' => $request->type,
        'start_at' => $startAt,
        'end_at' => $endAt,
      ]);

      return response()->json([
        'success' => true,
      ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contract $contract)
    {
      $contract->delete();
      return response()->json([
        'success' => true,
      ]);
    }
}
