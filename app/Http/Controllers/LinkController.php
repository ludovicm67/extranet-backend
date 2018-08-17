<?php

namespace App\Http\Controllers;

use Validator;
use ludovicm67\Url\Explorer\Explorer;
use ludovicm67\Request\Exception\RequestException;
use App\Link;
use App\LinkCategory;
use App\LinkCategoryAssoc;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $this->needPermission('links', 'show');
      return response()->json([
        'success' => true,
        'data' => [
          'lasts' => Link::orderBy('id', 'desc')->take(5)->get(), // last 5
          'categories' => LinkCategory::all(), // all categories
        ]
      ]);
    }

    private function createCategoriesOnTheFly($id) {
      $category = LinkCategory::find($id);
      if (empty($category)) {
        $category = LinkCategory::where('name', $id)->first();
        if (empty($category)) {
          $category = LinkCategory::create([
            'name' => $id,
          ]);
        }
      }
      return $category->id;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->needPermission('links', 'add');
      $validator = Validator::make($request->all(), [
        'url' => 'required|url',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors' => $validator->errors()->all(),
        ], 400);
      }

      $link = Link::create([
        'user_id' => auth()->user()->id,
        'title' => $request->title,
        'description' => $request->description,
        'image_url' => $request->image_url,
        'url' => $request->url,
      ]);

      if (!empty($request->categories) && is_array($request->categories)) {
        foreach ($request->categories as $category) {
          if (empty($category)) continue;
          $categoryId = $this->createCategoriesOnTheFly($category);
          if (empty($categoryId)) continue;
          LinkCategoryAssoc::create([
            'link_id' => $link->id,
            'category_id' => $categoryId,
          ]);
        }
      }

      return response()->json([
        'success' => true,
      ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Link  $link
     * @return \Illuminate\Http\Response
     */
    public function show(Link $link)
    {
      $this->needPermission('links', 'show');
      return response()->json([
        'success' => true,
        'data' => $link->fresh(['categories']),
      ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Link  $link
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Link $link)
    {
      $this->needPermission('links', 'edit');
      $validator = Validator::make($request->all(), [
        'url' => 'required|url',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors' => $validator->errors()->all(),
        ], 400);
      }

      $link->update([
        'user_id' => auth()->user()->id,
        'title' => $request->title,
        'description' => $request->description,
        'image_url' => $request->image_url,
        'url' => $request->url,
      ]);

      LinkCategoryAssoc::where('link_id', $link->id)->delete();
      if (!empty($request->categories) && is_array($request->categories)) {
        foreach ($request->categories as $category) {
          if (empty($category)) continue;
          $categoryId = $this->createCategoriesOnTheFly($category);
          if (empty($categoryId)) continue;
          LinkCategoryAssoc::create([
            'link_id' => $link->id,
            'category_id' => $categoryId,
          ]);
        }
      }

      return response()->json([
        'success' => true,
      ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Link  $link
     * @return \Illuminate\Http\Response
     */
    public function destroy(Link $link)
    {
      $this->needPermission('links', 'delete');
      $link->delete();

      return response()->json([
        'success' => true,
      ]);
    }

    public function preview(Request $request) {
      $url = $request->url;
      $parsed = parse_url($url);
      if (empty($parsed['scheme'])) {
        $url = 'http://' . ltrim($url, '/');
      }

      try {
        $explorer = new Explorer($url);
      } catch (RequestException $e) {
        return response()->json([
          'success' => false,
          'message' => 'request error: please check if the provided URL is valid',
        ], 400);
      }

      return response()->json([
        'success' => true,
        'data' => $explorer->getResults(),
      ]);
    }
}
