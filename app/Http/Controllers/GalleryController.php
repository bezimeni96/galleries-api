<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGalleryRequest;
use App\Models\Gallery;
use App\Models\Image;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->get('word', '');
        $skip = $request->get('skip', 0);

        $galleriesQuery = Gallery::query();
        $galleriesQuery->with('author', 'images');
        
        $galleriesQuery->where( functioN($query) use ($search) {
            $query->where('title', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->orwhereHas('author', function($que) use ($search) {
                    $que->where('first_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%');
                });
        });

        
        $galleries = $galleriesQuery->orderBy('created_at', 'desc')
            ->skip(($skip) * 10)
            ->take(10)
            ->get();
        
        $count = $galleriesQuery->count();
        return [$galleries, $count];
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
        $user = auth()->user()->id;

        $gallery = Gallery::create([
            "title" => $data['title'],
            "description" => $data['description'],
            "author_id" => $user
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
        $gallery = Gallery::with('author', 'images', 'comments.user')->findOrFail($id);
        return $gallery;
    }


    public function showAuthor(Request $request, $id)
    {
        $search = $request['word'];

        $galleriesQuery = Gallery::query();
        $galleriesQuery->with('author', 'images')->where('author_id', $id);
        
        
        $galleriesQuery->where( functioN($query) use ($search) {
            $query->where('title', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->orwhereHas('author', function($que) use ($search) {
                    $que->where('first_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%');
                });
        });
        

        $galleries = $galleriesQuery->orderBy('created_at', 'desc')->limit(10)->get();
        
        return $galleries;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user()->id;

        $gallery = Gallery::find($id);
        $gallery->title = $request->title;
        $gallery->description = $request->description;
        $gallery->user_id = $user;
        $gallery->save(); 
        
        $count=1;
        foreach(request('images') as $img) {
            $image = Image::find($img->id);
            if (!$image) {
                $image = Image::create([
                    "url" => $img->url,
                    "order_index" => $count,
                    "gallery_id" => $gallery['id'],
                ]);
            } else {
                $image->url = $img->url;
                $image->order_index = $count;
            };
            $count++;
        }
        return $gallery;
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
        $user = auth()->user()->id;

        if ( $gallery['author_id'] === $user) {
            Image::where('gallery_id', $id)->delete();
            Comment::where('gallery_id', $id)->delete();
            $gallery->delete();
        }


        return $gallery;
        
    }
}
