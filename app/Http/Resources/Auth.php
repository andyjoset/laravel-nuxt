<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Resources\Json\JsonResource;

class Auth extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => (int) $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'photo_url'  => $this->photo_url,

            'email_verified_at' => $this->when($this->resource instanceof MustVerifyEmail, $this->email_verified_at),

            $this->mergeWhen($this->relationLoaded('roles'), fn () => [
                'roles'       => $this->getRoleNames(),
                'permissions' => $this->getAllPermissions()->pluck('name'),
            ]),
        ];
    }
}
