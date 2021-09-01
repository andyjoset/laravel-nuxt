<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Auth extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'         => (int) $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'photo_url'  => $this->photo_url,

            $this->mergeWhen($this->relationLoaded('roles'), fn () => [
                'roles'       => $this->getRoleNames(),
                'permissions' => $this->getAllPermissions()->pluck('name'),
            ]),
        ];
    }
}
