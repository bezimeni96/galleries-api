<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGalleryRequest;
use App\Models\Gallery;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $galleries = Gallery::with('author', 'images')
            ->orderBy('created_at', 'desc')->limit(10)->get();

        return $galleries;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateGalleryRequest $request)
    {
        $data = $request->validated();
        $gallery = Gallery::create([
            "title" => $data['title'],
            "description" => $data['description'],
            "author_id" => 5
        ]);
        
        $count = 1;
        foreach ($data['images'] as $image_url) {
            $image = Image::create([
                "url" => $image_url,
                "order_index" => $count,
                "gallery_id" => $gallery['id'],
            ]);
            $count++;
        };

        return $gallery;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $gallery = Gallery::with('author', 'images')->findOrFail($id);
        return $gallery;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Gallery $gallery)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $gallery = Gallery::findOrFail($id);
        
        Image::where('gallery_id', $id)->delete();
        $gallery->delete();

        return $gallery;
    }
}
