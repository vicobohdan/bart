<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Storage;
use Illuminate\Http\UploadedFile;


class Image extends Model
{
	protected $fillable = ['name', 'path', 'storage'];

	// path to file
	protected $fullPath;

    public function __construct($path)
    {
    	$this->fullPath = $path;
    }


    /**
     * Create image.
     *
     * @param  UploadedFile  $fiel
     * @param  Gallery  $gallery
     * @return App\Models\Image
     */
    static public function create(UploadedFile $file, Gallery $gallery)
    {
    	$name = self::generateName($file, $gallery);
		
		$file_path = $file->storeAs($gallery->storePath(), $name);
    	
    	return new Image($file_path);
    }

    /**
     * Get image blob.
     *
     * @param  int x
     * @param  int y
     * @return ImageBlob
     */
    public function blob(int $x, int $y)
    {
    	if ($x || $y) {
    		return $this->resize($x, $y);
    	} else {
    		return Storage::get($this->fullPath);
    	}
    }

    /**
     * Delete image.
     *
     * @return
     */
    public function delete()
    {
    	Storage::delete($this->fullPath);
    }


    // ===== ATTRIBUTES ======================================

    public function getPathAttribute()
    {
    	return pathinfo($this->fullPath)['basename'];
    }

	public function getFullpathAttribute()
    {
    	$pathinfo = pathinfo($this->fullPath);
    	return rawurlencode(str_replace(config('gallery.folder'), '', $pathinfo['dirname'])).'/'.$pathinfo['basename'];
    }    

    public function getNameAttribute()
    {
    	return pathinfo($this->fullPath)['filename'];
    }

    public function getModifiedAttribute()
    {
    	return (new \DateTime)->setTimestamp(Storage::lastModified($this->fullPath)); //->format(\DateTime::ISO8601)
    }

    public function getMimeAttribute()
    {
    	return Storage::mimeType($this->fullPath);
    }

    // -----------------------------------------------------------------------

    static private function generateName(UploadedFile $file, Gallery $gallery)
    {
    	$filename = str_slug(pathinfo($file->getClientOriginalName())['filename']);

    	$name = $filename.".".$file->getClientOriginalExtension();

    	$i = 2;
    	while ($gallery->getImage($name)) {
    		$name = $filename."-$i.".$file->getClientOriginalExtension();
    		$i++;
    	}

    	return $name;
    }


    private function resize($w, $h)
    {
        $img = imagecreatefromstring(Storage::get($this->fullPath));

        $ratio = imagesx($img) / imagesy($img);
        // count dimensions
        if (!$h) {
            // count w & resize & return 
            $h = $w / $ratio;
        }

        if (!$w) {
            // count h & resize & return 
            $w = $h * $ratio;
        }

        $new_ratio = $w / $h;

        // crop&resize by aspect ratio
        if ($ratio < $new_ratio) {
            // resize 
            $img = imagescale($img, $w);

            // crop 
            $img = imagecrop($img, [
                'x' => 0,
                'y' => (imagesy($img) - $h)/2 + 0.5,
                'width' => $w,
                'height' => $h
            ]);

        } else {
            // resize
            $img = imagescale($img, $h * $ratio + 0.5);

            // crop
            $img = imagecrop($img, [
                'x' => (imagesx($img) - $w)/2 + 0.5,
                'y' => 0,
                'width' => $w,
                'height' => $h
            ]);
        }

        ob_start();
        imagejpeg($img); 

        return ob_get_clean();
    }

}
