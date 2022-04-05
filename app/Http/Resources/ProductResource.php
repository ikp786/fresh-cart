<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'pav_price'         => $this->pav_price,
            'half_kg_price'     => $this->half_kg_price,
            'kg_price'          => $this->kg_price,
            'description'       => $this->description,
            'images'            => ProductImagesResource::collection($this->allImages)
        ];
    }
}
