<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'last_name'        => $this->last_name,
            'first_name'       => $this->first_name,
            'last_name_kana'   => $this->last_name_kana,
            'first_name_kana'  => $this->first_name_kana,
            'memo'             => $this->memo,
            'is_favorite'      => $this->is_favorite,
        ];
    }
}
