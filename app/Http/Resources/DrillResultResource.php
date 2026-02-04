<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DrillResultResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'hole_id' => $this->hole_id,
            'intercept_m' => $this->intercept_m !== null ? (float) $this->intercept_m : null,
            'grade' => $this->grade !== null ? (float) $this->grade : null,
            'unit' => $this->unit,
            'commodity' => $this->whenLoaded('commodity', fn () => $this->commodity ? [
                'id' => $this->commodity->id,
                'name' => $this->commodity->name,
                'slug' => $this->commodity->slug,
            ] : null),
        ];
    }
}
