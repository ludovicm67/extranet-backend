<?php

namespace App\Http\Controllers;

use Validator;
use App\Project;
use App\ProjectContact;
use App\ProjectOrder;
use App\ProjectUser;
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
        //
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
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors' => $validator->errors()->all(),
        ], 400);
      }

      // fields that can be nullable
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
      ]);

      if (!empty($request->contacts)) {
        foreach (array_unique($request->contacts) as $contact) {
          ProjectContact::create([
            'project_id' => $project->id,
            'contact_id' => intval($contact, 10),
          ]);
        }
      }

      if (!empty($request->orders)) {
        foreach (array_unique($request->orders) as $order) {
          ProjectOrder::create([
            'project_id' => $project->id,
            'order_id' => intval($order, 10),
          ]);
        }
      }

      if (!empty($request->users)) {
        foreach (array_unique($request->users) as $user) {
          ProjectUser::create([
            'project_id' => $project->id,
            'user_id' => intval($user, 10),
          ]);
        }
      }

      return response()->json([
        'success' => true,
        'debug' => $project,
      ]);
    }



    public function new()
  {
        if (
          isset($_POST['tagName']) &&
          isset($_POST['tagName']) &&
          is_array($_POST['tagName']) == is_array($_POST['tagValue']) &&
          count($_POST['tagName']) &&
          count($_POST['tagValue'])
        ) {
          for ($i = 0; $i < count($_POST['tagName']); $i++) {
            $tagId = $this->createTagsOnThFly(
              $this->input->post('tagName')[$i]
            );
            $tagVal = strip_tags(trim($this->input->post('tagValue')[$i]));
            if (!empty($tagId)) {
              $this->db->insert('project_tags', [
                'project_id' => $projectId,
                'tag_id' => $tagId,
                'value' => $tagVal
              ]);
              $contentToLog['tags'][] = [
                'tag_id' => $tagId,
                'value' => $tagVal
              ];
            }
          }
        }

        if (
          isset($_POST['urlName']) &&
          isset($_POST['urlName']) &&
          is_array($_POST['urlName']) == is_array($_POST['urlValue']) &&
          count($_POST['urlName']) &&
          count($_POST['urlValue'])
        ) {
          for ($i = 0; $i < count($_POST['urlName']); $i++) {
            $urlName = strip_tags(trim($this->input->post('urlName')[$i]));
            $urlValue = strip_tags(trim($this->input->post('urlValue')[$i]));
            if (!empty($urlName) || !empty($urlValue)) {
              $this->db->insert('project_urls', [
                'project_id' => $projectId,
                'name' => $urlName,
                'value' => $urlValue,
                'order' => $i
              ]);
              $contentToLog['urls'][] = [
                'name' => $urlName,
                'value' => $urlValue,
                'order' => $i
              ];
            }
          }
        }
  }

    /**
     * Display the specified resource.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        //
    }
}
