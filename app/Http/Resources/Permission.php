<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Permission extends JsonResource
{
    use Helpers;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'name' => $this->whenNotNull($this->name),
            'module' => $this->whenNotNull($this->module),
            'description' => $this->whenNotNull($this->description),
            'roles' => Role::collection($this->whenLoaded('roles')),
        ];
    }
}
