<?php

namespace App\Http\Resources\v1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->deleted_at != null) {
            return [
                'name' => $this->name,
                'slug' => $this->slug,
                'deleted' => Carbon::parse($this->deleted_at)->format('d/m/Y - H:i:s')
            ];
        }

        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'order' => $this->order,
        ];
    }
}
