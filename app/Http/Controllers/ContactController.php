<?php

namespace App\Http\Controllers;

use Validator;
use App\Contact;
use App\Type;
use Illuminate\Http\Request;

class ContactController extends Controller
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
        'data' => Contact::all(),
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
        'name' => 'required|string|max:255',
        'mail' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:255',
        'address' => 'nullable|string|max:255',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors' => $validator->errors()->all(),
        ], 400);
      }

      // basic informations
      $contactName = e($request->name);
      $contactMail = e($request->mail);
      $contactPhone = e($request->phone);
      $contactAddress = e($request->address);
      $contactOther = e($request->other);
      $contactTypeId = null;

      if (!empty($request->type_id)) {
        $type = Type::find($request->type_id);

         // if no corresponding type was found, create one
        if (empty($type)) {
          $type = Type::where('name', $request->type_id)->first();
          if (empty($type)) {
            $type = Type::create([
              'name' => $request->type_id,
            ]);
          }
        }
        $contactTypeId = $type->id;
      }

      Contact::create([
        'name' => $contactName,
        'mail' => $contactMail,
        'phone' => $contactPhone,
        'address' => $contactAddress,
        'other' => $contactOther,
        'type_id' => $contactTypeId,
      ]);

      return response()->json([
        'success' => true,
      ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact)
    {
      return response()->json([
        'success' => true,
        'data' => $contact,
      ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contact $contact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contact $contact)
    {
      $contact->delete();

      return response()->json([
        'success' => true,
      ]);
    }
}
