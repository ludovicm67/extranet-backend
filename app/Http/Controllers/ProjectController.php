<?php

namespace App\Http\Controllers;

use Validator;
use App\Project;
use App\ProjectContact;
use App\ProjectOrder;
use App\ProjectUser;
use App\ProjectTag;
use App\ProjectUrl;
use App\ProjectFavorite;
use App\Tag;
use App\Identifier;
use App\ProjectIdentifier;
use Illuminate\Http\Request;

class ProjectController extends Controller
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
        'data' => Project::orderBy('end_at', 'desc')->get()->sortByDesc('favorited')->values()->all(),
      ]);
    }

    // create a tag when needed; returns the tag id
    private function createTagsOnThFly($id) {
      $tag = Tag::find($id);
      if (empty($tag)) {
        $tag = Tag::where('name', $id)->first();
        if (empty($tag)) {
          $tag = Tag::create([
            'name' => $id,
          ]);
        }
      }
      return $tag->id;
    }

    private function createAssociations(Request $request, Project $project) {
      if (!empty($request->contacts)) {
        foreach (array_unique($request->contacts) as $contact) {
          ProjectContact::create([
            'project_id' => $project->id,
            'contact_id' => intval($contact, 10),
          ]);
        }
      }

      if (!empty($request->orders) && is_array($request->orders)) {
        foreach (array_unique($request->orders) as $order) {
          ProjectOrder::create([
            'project_id' => $project->id,
            'order_id' => intval($order, 10),
          ]);
        }
      }

      if (!empty($request->users) && is_array($request->users)) {
        foreach (array_unique($request->users) as $user) {
          ProjectUser::create([
            'project_id' => $project->id,
            'user_id' => intval($user, 10),
          ]);
        }
      }

      if (!empty($request->tags) && is_array($request->tags)) {
        foreach ($request->tags as $tag) {
          if (empty($tag['id'])) continue;
          $tagId = $this->createTagsOnThFly($tag['id']);
          if (empty($tagId)) continue;
          ProjectTag::create([
            'project_id' => $project->id,
            'tag_id' => $tagId,
            'value' => $tag['value'],
          ]);
        }
      }

      if (!empty($request->urls) && is_array($request->urls)) {
        $orderUrl = 0;
        foreach ($request->urls as $url) {
          if (empty($url['name']) && empty($url['value'])) continue;
          ProjectUrl::create([
            'project_id' => $project->id,
            'name' => $url['name'],
            'value' => $url['value'],
            'order' => $orderUrl++,
          ]);
        }
      }
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
        'parent_id' => 'nullable|exists:projects,id',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors' => $validator->errors()->all(),
        ], 400);
      }

      // fields that can be nullable
      $parent = empty($request->parent_id) ? null : $request->parent_id;
      $domain = empty($request->domain) ? null : $request->domain;
      $clientId = empty($request->client_id) ? null : $request->client_id;
      $nextAction = empty($request->next_action) ? null : $request->next_action;
      $endAt = $request->end_at
        ? date('Y-m-d', strtotime($request->end_at))
        : null;

      $project = Project::create([
        'name' => $request->name,
        'domain' => $domain,
        'client_id' => $clientId,
        'next_action' => $nextAction,
        'end_at' => $endAt,
        'parent_id' => $parent,
      ]);

      $this->createAssociations($request, $project);

      return response()->json([
        'success' => true,
      ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
      return response()->json([
        'success' => true,
        'data' => $project->fresh([
          'users', 'orders', 'contacts', 'tags', 'urls', 'client', 'parent',
        ]),
      ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
      $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'parent_id' => 'nullable|exists:projects,id',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors' => $validator->errors()->all(),
        ], 400);
      }

      // delete all previous associations
      ProjectContact::where('project_id', $project->id)->delete();
      ProjectOrder::where('project_id', $project->id)->delete();
      ProjectUser::where('project_id', $project->id)->delete();
      ProjectTag::where('project_id', $project->id)->delete();
      ProjectUrl::where('project_id', $project->id)->delete();

      // fields that can be nullable
      $parent = empty($request->parent_id) ? null : $request->parent_id;
      $domain = empty($request->domain) ? null : $request->domain;
      $clientId = empty($request->client_id) ? null : $request->client_id;
      $nextAction = empty($request->next_action) ? null : $request->next_action;
      $endAt = $request->end_at
        ? date('Y-m-d', strtotime($request->end_at))
        : null;

      $project->update([
        'name' => $request->name,
        'domain' => $domain,
        'client_id' => $clientId,
        'next_action' => $nextAction,
        'end_at' => $endAt,
        'parent_id' => $parent,
      ]);

      $this->createAssociations($request, $project);

      return response()->json([
        'success' => true,
      ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
      $project->delete();
      return response()->json([
        'success' => true,
      ]);
    }

    public function fav(Request $request, Project $project) {
      $pf = ProjectFavorite::where([
        'project_id' => $project->id,
        'user_id' => auth()->user()->id,
      ])->first();

      if (empty($pf)) {
        ProjectFavorite::create([
          'project_id' => $project->id,
          'user_id' => auth()->user()->id,
        ]);
      }

      return response()->json([
        'success' => true,
      ]);
    }

    public function unfav(Request $request, Project $project) {
      ProjectFavorite::where([
        'project_id' => $project->id,
        'user_id' => auth()->user()->id,
      ])->delete();

      return response()->json([
        'success' => true,
      ]);
    }

    public function identifiers(Project $project) {
      return response()->json([
        'success' => true,
        'data' => $project->fresh(['identifiers']),
      ]);
    }

    public function newIdentifier(Request $request, Project $project) {
      $identifierId = null;

      if (!empty($request->identifier_id)) {
        $identifier = Identifier::find($request->identifier_id);

         // if no corresponding identifier was found, create one
        if (empty($identifier)) {
          $identifier = Identifier::where('name', $request->identifier_id)->first();
          if (empty($identifier)) {
            $identifier = Identifier::create([
              'name' => $request->identifier_id,
            ]);
          }
        }
        $identifierId = $identifier->id;
      }

      ProjectIdentifier::create([
        'project_id' => $project->id,
        'identifier_id' => $identifierId,
        'value' => $request->value,
        'confidential' => ($request->confidential == 1 ? 1 : 0),
      ]);

      return response()->json([
        'success' => true,
      ]);
    }

    public function updateIdentifier(Request $request, Projectidentifier $project_identifier) {
      $identifierId = null;

      if (!empty($request->identifier_id)) {
        $identifier = Identifier::find($request->identifier_id);

         // if no corresponding identifier was found, create one
        if (empty($identifier)) {
          $identifier = Identifier::where('name', $request->identifier_id)->first();
          if (empty($identifier)) {
            $identifier = Identifier::create([
              'name' => $request->identifier_id,
            ]);
          }
        }
        $identifierId = $identifier->id;
      }

      $project_identifier->update([
        'identifier_id' => $identifierId,
        'value' => $request->value,
        'confidential' => ($request->confidential == 1 ? 1 : 0),
      ]);

      return response()->json([
        'success' => true,
      ]);
    }

    public function deleteIdentifier(Projectidentifier $project_identifier) {
      $project_identifier->delete();

      return response()->json([
        'success' => true,
      ]);
    }

    public function showIdentifier(Projectidentifier $project_identifier) {
      return response()->json([
        'success' => true,
        'data' => $project_identifier,
      ]);
    }
}
