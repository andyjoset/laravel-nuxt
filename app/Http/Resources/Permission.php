<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Permission extends JsonResource
{
    use Helpers;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
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
