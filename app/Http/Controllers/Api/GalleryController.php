<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;
use Validator;

use App\Models\Gallery;
use App\Http\Resources\GalleryResource;
use App\Http\Resources\ImageResource;
use App\Http\Resources\GalleriesResource;
use Illuminate\Support\Collection;

use App\Exceptions\ApiNotFoundException;
use App\Exceptions\ApiValidationException;
use App\Exceptions\ApiGalleryExistException;

class GalleryController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return [
            'galeries' => GalleriesResource::collection( Gallery::all() )
        ];
    }

    /**
     * Display the specified gallery.
     *
     * @param  string  $path
     * @return \Illuminate\Http\Response
     */
    public function show($path)
    {
        if (!($gallery = Gallery::get($path))) {
            throw new ApiNotFoundException;
        }

        return [
            'gallery' => new GalleryResource($gallery),
            'images' => ImageResource::collection($gallery->images()),
        ];
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
            'name' => 'required|string|min:1|not_regex:@/@'
        ]);

    	if ($validator->fails()) {
            throw new ApiValidationException($validator->messages()->toArray());
    	}

    	if (Gallery::exists($request->input('name'))) {
            throw new ApiGalleryExistException;
    	}

        $gallery = Gallery::create(['name' => $request->input('name')]);

    	return response()->json(new GalleryResource($gallery), 201);
    }


    /**
     * Upload images to specific gallery.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $path
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request, $path)
    {
        $validator = Validator::make($request->all(), [
            'filename' => 'required|array',
            'filename.*' => 'required|image|mimes:'.config('gallery.permited_images_type')
        ]);

        if ($validator->fails()) {
            throw new ApiValidationException($validator->messages()->toArray());
    	}

    	if (!Gallery::exists($path)) {
            throw new ApiNotFoundException;
    	}

        $gallery = Gallery::get($path);

    	// store files
    	$images = new Collection;
    	foreach ($request->file('filename') as $file) {
            $images->push($gallery->addImage($file));
    	}

    	return response()->json(['uploaded' => ImageResource::collection($images)], 201);
    }

    /**
     * Remove the specified resource from gallery.
     *
     * @param  string  $path
     * @param  string  $file
     * @return \Illuminate\Http\Response
     */
    public function destroy($path, $file = null)
    {
        if (!Gallery::exists($path) || $file && !Gallery::get($path)->getImage($file)) {
            throw new ApiNotFoundException;
        }

    	if ($file) {
    		Gallery::get($path)->getImage($file)->delete();
    	} else {
        	Gallery::get($path)->delete();
    	}

        return response()->json();
    }
}
