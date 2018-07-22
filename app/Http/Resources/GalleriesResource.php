<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\GalleryResource;
use App\Http\Resources\ImageResource;

class GalleriesResource extends JsonResource
{
    /**
     * Transform the Galleries into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // append first image to gallery if exist
        return array_merge(
                (new GalleryResource($this))->toArray($request),    
                ($this->images()->isNotEmpty() ? ['image' => new ImageResource($this->images()->first())] : [])
            );
    }
}
