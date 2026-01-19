<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            // Design info
            'design' => $this->when($this->relationLoaded('design'), [
                'id' => $this->design?->id,
                'name' => $this->design?->name,
                'images' => $this->design?->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'url' => asset('storage/' . $image->image_path),
                        'is_primary' => $image->is_primary,
                    ];
                }),
            ]),

            // Quantity & Pricing
            'quantity' => $this->quantity,
            'unit_price' => (float) $this->unit_price,
            'total_price' => (float) $this->total_price,

            // Design snapshot (at time of order)
            'design_snapshot' => $this->when($this->design_snapshot, $this->design_snapshot),

            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
