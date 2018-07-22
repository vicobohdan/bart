<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\Image;
use Storage;
use Illuminate\Http\UploadedFile;


class Gallery extends Model
{
	protected $fillable = ['name', 'path'];

	/**
     * Select all galeries.
     *
     * @param  Array  $columns
     * @return Illuminate\Support\Collection
     */
    static public function all($columns = []) 
    {
    	$dirs = Storage::directories(config('gallery.folder'));

    	// $galleries = [];
    	$galleries = new Collection;
		foreach ($dirs as $dir) {
			$galleries->push(new Gallery([
				'name' => basename($dir),
			]));
		}

		return $galleries;
    }

    /**
     * Select galery by name.
     *
     * @param  String  $name
     * @return App\Models\Gallery
     */
    static public function get(String $name)
    {
    	if (!Storage::exists(config('gallery.folder').$name)) {
    		return null;
    	}

    	return new Gallery(['name' => $name]);
    }

    /**
     * Create galery by name.
     *
     * @param  String  $name
     * @return App\Models\Gallery
     */
    static public function create(Array $attr = [])
    {
    	// make gallery
    	Storage::makeDirectory(config('gallery.folder').$attr['name']);

    	return new Gallery(['name' => $attr['name']]);
    }

    /**
     * Gallery exist?
     *
     * @param  String  $name
     * @return bool
     */
    static public function exists(String $name)
    {
    	return Storage::exists(config('gallery.folder').$name);
    }

    /**
     * Delete gallery.
     *
     * @return
     */
    public function delete()
    {
    	Storage::deleteDirectory(config('gallery.folder').$this->name);
    }

    /**
     * Add image to galery.
     *
     * @param  String  $name
     * @return App\Models\Image
     */
    public function addImage(UploadedFile $file)
    {
    	return Image::create($file, $this);

    	return new Gallery(['name' => $attr['name']]);
    }

    /**
     * Select all images from gallery.
     *
     * @return Illuminate\Support\Collection
     */
    public function images()
    {
    	$files = Storage::files(config('gallery.folder').$this->name);

    	$images = new Collection;
		foreach ($files as $file) {
			$images->push(new Image($file));
		}

		return $images;
    }

    /**
     * Select image from gallery by name.
     *
     * @return App\Models\Image
     */
    public function getImage(String $name)
    {
    	if (!Storage::exists(config('gallery.folder').$this->name."/$name")) {
    		return null;
    	}

    	return new Image(config('gallery.folder').$this->name."/$name");
    }

    /**
     * Get gallery store path.
     *
     * @return String
     */
    public function storePath()
    {
    	return config('gallery.folder')."{$this->name}/";
    }


    //========= ATTRIBUTES =====================================================

    public function getPathAttribute()
    {
    	return rawurlencode($this->name);
    }
}
