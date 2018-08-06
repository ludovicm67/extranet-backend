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
      $contactName = $request->name;
      $contactMail = $request->mail;
      $contactPhone = $request->phone;
      $contactAddress = $request->address;
      $contactOther = $request->other;
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
        'data' => $contact->fresh(['type']),
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
      $contactName = $request->name;
      $contactMail = $request->mail;
      $contactPhone = $request->phone;
      $contactAddress = $request->address;
      $contactOther = $request->other;
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

      $contact->name = $contactName;
      $contact->mail = $contactMail;
      $contact->phone = $contactPhone;
      $contact->address = $contactAddress;
      $contact->other = $contactOther;
      $contact->type_id = $contactTypeId;

      $contact->save();

      return response()->json([
        'success' => true,
      ]);
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

    public function exportData(Request $request) {
      $type = intval($request->type); // type id
      $tag = intval($request->tag); // tag id
      $value = trim($request->value); // tag value

      $contacts = Contact::with([
        'type',
        'projects',
        'projects.tags',
      ]);

      if (!empty($type)) {
        $contacts->where('type_id', $type);
      }
      $contacts = $contacts->get();

      $res = [];
      foreach ($contacts as $c) {
        foreach ($c->projects as $p) {
          $p = json_decode(json_encode($p));
          $tags = array_map(function ($t) {
            return $t->pivot->value;
          }, $p->tags);

          if (!empty($value) && !in_array($value, $tags)) continue;
          $type = $c->type;
          if (!empty($type)) $type = $c->type->name;

          $uniqueKey = $c->id . '-' . $p->id;

          $res[$uniqueKey] = (object) [
            'key' => $uniqueKey,
            'id' => $c->id,
            'mail' => $c->mail,
            'name' => $c->name,
            'phone' => $c->phone,
            'address' => $c->address,
            'project_id' => $p->id,
            'project_name' => $p->name,
            'project_domain' => $p->domain,
            'type' => $type,
          ];
        }
      }

      return array_values($res);
    }

    public function export(Request $request) {
      $data = $this->exportData($request);

      return response()->json([
        'success' => true,
        'data' => $data,
      ]);
    }

    public function csv(Request $request) {
      $data = $this->exportData($request);

      ob_start();
      $out = fopen('php://output', 'w');
      foreach ($data as $contact) {
        fputcsv($out, [
          $contact->mail,
          $contact->name,
          $contact->phone,
          $contact->address,
          $contact->project_name,
          $contact->project_domain,
          $contact->type,
        ]);
      }
      fclose($out);
      $content = ob_get_clean();

      return response($content)->withHeaders([
        'Content-type' => 'text/plain',
        'Content-Disposition' => 'attachment; filename=contacts.csv',
      ]);
    }
}
