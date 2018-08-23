<?php

namespace App\Http\Controllers;

use Storage;
use Validator;
use App\Expense;
use App\User;
use App\Mail\Custom;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use ludovicm67\Laravel\Multidomain\Configuration;

class ExpenseController extends Controller
{
    private function deleteFile($file) {
      if (empty($file)) {
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
      $this->needPermission('expenses', 'show');

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
      $this->needPermission('expenses', 'add');
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

      // notify admins by mail
      $user = auth()->user();
      $userName = $user->firstname . ' ' . $user->lastname . ' (' . $user->email . ')';
      $emails = User::where('is_admin', 1)->orWhereIn('role_id', function ($query) {
        $query
          ->select('role_id')
          ->from('rights')
          ->where('name', 'request_management')
          ->where('edit', 1);
      })->select('email')->get()->toArray();

      $emails = array_map(function ($e) {
        return $e['email'];
      }, $emails);

      $config = Configuration::getInstance();
      $domainConf = $config->getDomain();

      $env = env('APP_ENV');
      $port = $env == 'local' ? ':3000' : '';
      $url = '';
      if (!is_null($domainConf) && !is_null($domainConf->get('frontend'))) {
        $url = "\n\n\nGérez les différentes demandes depuis : " . rtrim($domainConf->get('frontend'), '/') . $port . '/requests/';
      }

      foreach ($emails as $m) {
        Mail::to($m)->send(new Custom('Nouvelle note de frais', "Une nouvelle note de frais d'un montant de " . number_format($request->amount, 2, ',', ' ') . "€ a été déposée par " . $userName . ' pour le mois ' . $request->month . '/' . $request->year . ".\n\nMotif : " . $request->type . "\n\n" . $request->details . $url));
      }

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
      $user = auth()->user();
      if ($expense->user_id != $user->id) {
        $this->needPermission('expenses', 'show');
      }

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
      $user = auth()->user();
      if ($expense->user_id != $user->id) {
        $this->needPermission('expenses', 'edit');
      }

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

      if ($request->delete_file == 1) {
        $this->deleteFile($expense->file);
        $expense->file = null;
      }

      $file = $request->file('file');
      if (!empty($file)) {
        $this->deleteFile($expense->file);
        $file = str_replace('public/', '', $file->store('public/expenses/' . date('Y') . '/' . date('n')));
      } else {
        $file = $expense->file;
      }

      $accepted = 0;
      if ($user->can('request_management', 'edit')) {
        $accepted = $expense->accepted;
      }

      $expense->update([
        'user_id' => auth()->user()->id,
        'file' => $file,
        'details' => $request->details,
        'year' => $request->year,
        'month' => $request->month,
        'amount' => $request->amount,
        'type' => $request->type,
        'accepted' => $accepted,
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
      $this->needPermission('expenses', 'delete');
      $this->deleteFile($expense->file);
      $expense->delete();

      return response()->json([
        'success' => true,
      ]);
    }

    public function accept(Expense $expense) {
      $this->needPermission('request_management', 'edit');

      $expense->update([
        'accepted' => 1,
      ]);

      Mail::to($expense->user->email)->send(new Custom('Note de frais acceptée', 'Votre note de frais a été acceptée pour le mois ' . $expense->month . '/' . $expense->year));

      return response()->json([
        'success' => true,
      ]);
    }

    public function reject(Expense $expense) {
      $this->needPermission('request_management', 'edit');

      $expense->update([
        'accepted' => -1,
      ]);

      Mail::to($expense->user->email)->send(new Custom('Note de frais refusée', 'Votre note de frais a été refusée pour le mois ' . $expense->month . '/' . $expense->year));

      return response()->json([
        'success' => true,
      ]);
    }
}
