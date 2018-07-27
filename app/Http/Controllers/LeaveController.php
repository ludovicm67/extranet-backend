<?php

namespace App\Http\Controllers;

use Validator;
use App\Leave;
use Illuminate\Http\Request;

class LeaveController extends Controller
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
        'data' => Leave::with('user')->get(),
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
        'reason' => 'required|string',
        'file' => 'nullable|file',
        'start' => 'required|string',
        'start_time' => 'required|integer|min:0|max:23',
        'end' => 'required|string',
        'end_time' => 'required|integer|min:0|max:23',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors' => $validator->errors()->all(),
        ], 400);
      }

      $file = $request->file('file');
      if (!empty($file)) {
        $file = str_replace('public/', '', $file->store('public/leave/' . date('Y') . '/' . date('n')));
      }

      $startDate = date(
        'Y-m-d H:i:s',
        strtotime($request->start . ' ' . $request->start_time . ':00:00')
      );
      $endDate = date(
        'Y-m-d H:i:s',
        strtotime($request->end . ' ' . $request->end_time . ':00:00')
      );

      Leave::create([
        'user_id' => auth()->user()->id,
        'accepted' => 0,
        'file' => $file,
        'details' => $request->details,
        'start' => $startDate,
        'start_time' => $request->start_time,
        'end' => $endDate,
        'end_time' => $request->end_time,
        'reason' => $request->reason,
      ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function show(Leave $leave)
    {
      return response()->json([
        'success' => true,
        'data' => $leave->fresh(['user']),
      ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Leave $leave)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function destroy(Leave $leave)
    {
      $leave->delete();

      return response()->json([
        'success' => true,
      ]);
    }
}
