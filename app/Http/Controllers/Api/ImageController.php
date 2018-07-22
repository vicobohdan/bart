<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \Storage;
use App\Models\Gallery;
use App\Models\Image;

use App\Exceptions\ApiNotFoundException;

class ImageController extends Controller
{
    /**
     * Display the specified image.
     *
     * @param  int  $w
     * @param  int  $h
     * @param  string  $gallery
     * @param  string  $image
     * @return \Illuminate\Http\Response
     */
    public function show($w, $h, $gallery, $image)
    {

        if (!Gallery::exists($gallery) || !Gallery::get($gallery)->getImage($image)) {
            throw new ApiNotFoundException;
        }        

        $img = Gallery::get($gallery)->getImage($image);

        return response($img->blob($w, $h), 200)->header('Content-type', $img->mime);
    }
}
