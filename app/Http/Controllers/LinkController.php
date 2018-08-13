<?php

namespace App\Http\Controllers;

use ludovicm67\Url\Explorer\Explorer;
use ludovicm67\Request\Exception\RequestException;
use App\Link;
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
      return response()->json([
        'success' => true,
        'data' => Link::all(),
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Link  $link
     * @return \Illuminate\Http\Response
     */
    public function show(Link $link)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Link  $link
     * @return \Illuminate\Http\Response
     */
    public function destroy(Link $link)
    {
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
