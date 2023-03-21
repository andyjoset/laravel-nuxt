<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'     => (int) $this->id,
            'name'   => $this->name,
            'email'  => $this->email,
            'active' => $this->active,
            'roles'  => Role::collection($this->whenLoaded('roles')),
        ];
    }
}
