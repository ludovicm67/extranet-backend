<?php

namespace App\Http\Controllers;

use Validator;
use App\Project;
use App\Wiki;
use Illuminate\Http\Request;

class WikiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $this->needPermission('projects', 'show');

      return response()->json([
        'success' => true,
        'data' => Wiki::all(),
      ]);
    }

    public function indexProject(Project $project)
    {
      $user = auth()->user();
      if (!in_array($project->id, $user->user_projects)) {
        $this->needPermission('projects', 'show');
      }

      return response()->json([
        'success' => true,
        'data' => Wiki::with(['user'])
                  ->where('project_id', $project->id)
                  ->orderBy('updated_at', 'desc')
                  ->get(),
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
        'title' => 'string|max:255',
        'project_id' => 'exists:projects,id',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors' => $validator->errors()->all(),
        ], 400);
      }

      $user = auth()->user();
      if (!in_array($request->project_id, $user->user_projects)) {
        $this->needPermission('projects', 'edit');
      }

      Wiki::create([
        'title' => $request->title,
        'content' => $request->content,
        'user_id' => auth()->user()->id,
        'project_id' => $request->project_id,
      ]);

      return response()->json([
        'success' => true,
      ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Wiki  $wiki
     * @return \Illuminate\Http\Response
     */
    public function show(Wiki $wiki)
    {
      $user = auth()->user();
      if (!in_array($wiki->project_id, $user->user_projects)) {
        $this->needPermission('projects', 'show');
      }

      return response()->json([
        'success' => true,
        'data' => $wiki->fresh(['user']),
      ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Wiki  $wiki
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Wiki $wiki)
    {
      $validator = Validator::make($request->all(), [
        'title' => 'string|max:255',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors' => $validator->errors()->all(),
        ], 400);
      }

      $user = auth()->user();
      if (!in_array($wiki->project_id, $user->user_projects)) {
        $this->needPermission('projects', 'edit');
      }

      $wiki->update([
        'title' => $request->title,
        'content' => $request->content,
        'user_id' => auth()->user()->id, // last author
      ]);

      return response()->json([
        'success' => true,
      ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Wiki  $wiki
     * @return \Illuminate\Http\Response
     */
    public function destroy(Wiki $wiki)
    {
      $user = auth()->user();
      if (!in_array($wiki->project_id, $user->user_projects)) {
        $this->needPermission('projects', 'edit');
      }

      $wiki->delete();

      return response()->json([
        'success' => true,
      ]);
    }
}
