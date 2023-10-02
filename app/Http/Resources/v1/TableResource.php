<?php

namespace App\Http\Resources\v1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TableResource extends JsonResource
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
                'number' => $this->number,
                'deleted' => Carbon::parse($this->deleted_at)->format('d/m/Y - H:i:s')
            ];
        }

        return [
            'number' => $this->number,
        ];
    }
}
