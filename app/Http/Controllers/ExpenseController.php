<?php

namespace App\Http\Controllers;

use Storage;
use Validator;
use App\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    private function deleteFile($file) {
      if (is_null($file)) {
        return;
      }

      Storage::delete('/public/' . $file);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      return response()->json([
        'success' => true,
        'data' => Expense::with('user')->get(),
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
        'type' => 'required|string',
        'month' => 'required|integer|min:1|max:12',
        'year' => 'required|integer|min:1900|max:2222',
        'amount' => 'required|numeric|min:0',
        'file' => 'nullable|file',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors' => $validator->errors()->all(),
        ], 400);
      }

      $file = $request->file('file');
      if (!empty($file)) {
        $file = str_replace('public/', '', $file->store('public/expenses/' . date('Y') . '/' . date('n')));
      }

      Expense::create([
        'user_id' => auth()->user()->id,
        'accepted' => 0,
        'file' => $file,
        'details' => $request->details,
        'year' => $request->year,
        'month' => $request->month,
        'amount' => $request->amount,
        'type' => $request->type,
      ]);

      return response()->json([
        'success' => true,
      ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function show(Expense $expense)
    {
      return response()->json([
        'success' => true,
        'data' => $expense->fresh(['user']),
      ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Expense $expense)
    {
      $validator = Validator::make($request->all(), [
        'type' => 'required|string',
        'month' => 'required|integer|min:1|max:12',
        'year' => 'required|integer|min:1900|max:2222',
        'amount' => 'required|numeric|min:0',
        'file' => 'nullable|file',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors' => $validator->errors()->all(),
        ], 400);
      }

      $file = $request->file('file');
      if (!empty($file)) {
        $this->deleteFile($expense->file);
        $file = str_replace('public/', '', $file->store('public/expenses/' . date('Y') . '/' . date('n')));
      } else {
        $file = $expense->file;
      }

      $expense->update([
        'user_id' => auth()->user()->id,
        'accepted' => 0,
        'file' => $file,
        'details' => $request->details,
        'year' => $request->year,
        'month' => $request->month,
        'amount' => $request->amount,
        'type' => $request->type,
      ]);

      return response()->json([
        'success' => true,
      ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function destroy(Expense $expense)
    {
      $this->deleteFile($expense->file);
      $expense->delete();

      return response()->json([
        'success' => true,
      ]);
    }

    public function accept(Expense $expense) {
      $expense->update([
        'accepted' => 1,
      ]);

      return response()->json([
        'success' => true,
      ]);
    }

    public function reject(Expense $expense) {
      $expense->update([
        'accepted' => -1,
      ]);

      return response()->json([
        'success' => true,
      ]);
    }
}
