<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug,
            'price' => $this->price,
            'stock' => $this->stock,
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Get the status label for display
     */
    private function getStatusLabel(): string
    {
        return match($this->status) {
            \App\Enums\ProductStatus::DRAFT => 'Borrador',
            \App\Enums\ProductStatus::ACTIVE => 'Activo',
            \App\Enums\ProductStatus::WITHOUT_STOCK => 'Sin Stock',
            \App\Enums\ProductStatus::DELETED => 'Eliminado',
            \App\Enums\ProductStatus::DISCONTINUED => 'Descontinuado',
            default => 'Desconocido'
        };
    }
}
