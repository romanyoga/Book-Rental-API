<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RentalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->whenLoaded('user'),
            'book_id' => $this->whenLoaded('book'),
            'rented_at' => $this->rented_at,
            'due_at' => $this->due_at,
            'returned_at' => $this->returned_at ? $this->returned_at : null,
            'is_completed' => (bool) $this->is_completed,

        ];
    }
}
