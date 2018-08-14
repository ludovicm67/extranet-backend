<?php

namespace App\Http\Controllers;

use Storage;
use Validator;
use App\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
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
      $this->needPermission('documents', 'show');
      return response()->json([
        'success' => true,
        'data' => Document::all(),
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
      $this->needPermission('documents', 'add');
      $validator = Validator::make($request->all(), [
        'user_id' => 'exists:users,id',
        'type' => 'nullable|string',
        'date' => 'required|string',
        'file' => 'required|file',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors' => $validator->errors()->all(),
        ], 400);
      }

      $type = $request->type;
      if ($type != 'pay' && $type != 'medical') {
        $pay = null;
      }

      $file = $request->file('file');
      if (!empty($file)) {
        $file = str_replace('public/', '', $file->store('public/documents/' . date('Y') . '/' . date('n')));
      }

      Document::create([
        'user_id' => $request->user_id,
        'file' => $file,
        'type' => $type,
        'details' => $request->details,
        'date' => $request->date,
      ]);

      return response()->json([
        'success' => true,
      ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function show(Document $document)
    {
      $this->needPermission('documents', 'show');
      return response()->json([
        'success' => true,
        'data' => $document->fresh(['user']),
      ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Document $document)
    {
      $this->needPermission('documents', 'edit');
      $validator = Validator::make($request->all(), [
        'user_id' => 'exists:users,id',
        'type' => 'nullable|string',
        'date' => 'required|string',
        'file' => 'required|file',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors' => $validator->errors()->all(),
        ], 400);
      }

      $type = $request->type;
      if ($type != 'pay' && $type != 'medical') {
        $pay = null;
      }

      $file = $request->file('file');
      if (!empty($file)) {
        $this->deleteFile($document->file);
        $file = str_replace('public/', '', $file->store('public/documents/' . date('Y') . '/' . date('n')));
      } else {
        $file = $document->file;
      }

      $document->update([
        'user_id' => $request->user_id,
        'file' => $file,
        'type' => $type,
        'details' => $request->details,
        'date' => $request->date,
      ]);

      return response()->json([
        'success' => true,
      ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function destroy(Document $document)
    {
      $this->needPermission('documents', 'delete');
      $this->deleteFile($document->file);
      $document->delete();

      return response()->json([
        'success' => true,
      ]);
    }
}
