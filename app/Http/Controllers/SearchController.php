<?php

namespace App\Http\Controllers;

use DB;
use App\User;
use App\SellsyClient;
use App\SellsyContact;
use App\Project;
use App\Contact;
use App\Tag;
use Illuminate\Http\Request;

class SearchController extends Controller
{
  public function index(Request $request) {
    $search = '%' . $request->q . '%';
    $users = User::where('firstname', 'like', $search)
              ->orWhere('lastname', 'like', $search)
              ->orWhere(DB::raw("concat(firstname, ' ', lastname)"), 'like', $search)
              ->orWhere(DB::raw("concat(lastname, ' ', firstname)"), 'like', $search)
              ->orWhere('email', 'like', $search)->get();
    $clients = SellsyClient::where('fullName', 'like', $search)->get();
    $sellsyContacts = SellsyContact::where('fullName', 'like', $search)
                      ->orWhere('email', 'like', $search)
                      ->with('client')->get();
    $projects = Project::where('name', 'like', $search)
                ->orWhere('domain', 'like', $search)->get();
    $contacts = Contact::where('name', 'like', $search)->get();
    $tags = Tag::where('name', 'like', $search)->get();


    return response()->json([
      'users' => $users,
      'clients' => $clients,
      'projects' => $projects,
      'contacts' => $contacts,
      'sellsy_contacts' => $sellsyContacts,
      'tags' => $tags,
    ]);
  }
}
