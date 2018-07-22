<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ImageResource;


class GalleryResource extends JsonResource
{
    /**
     * Transform the Gallery into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        self::withoutWrapping();

        return [
            'name' => $this->name,
            'path' => $this->path,
        ];
    }
}
