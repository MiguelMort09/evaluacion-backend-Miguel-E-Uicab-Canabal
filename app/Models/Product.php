<?php

namespace App\Models;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'slug',
        'price',
        'stock',
        'status',
        'user_id',
    ];

    protected function casts()
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'status' => ProductStatus::class,
        ];
    }

    public function scopeFilter($builder, array $filter)
    {
        // Búsqueda por texto en nombre y descripción
        if (isset($filter['search']) && !empty($filter['search'])) {
            $builder->where(function ($query) use ($filter) {
                $query->where('name', 'like', '%' . $filter['search'] . '%')
                      ->orWhere('description', 'like', '%' . $filter['search'] . '%')
                      ->orWhere('slug', 'like', '%' . $filter['search'] . '%');
            });
        }

        // Filtro por stock disponible
        if (isset($filter['has_stock']) && $filter['has_stock'] === 'true') {
            $builder->where('stock', '>', 0);
        }

        // Filtro por usuario
        if (isset($filter['user_id']) && is_numeric($filter['user_id'])) {
            $builder->where('user_id', $filter['user_id']);
        }

        // Ordenamiento
        if (isset($filter['sort_by']) && in_array($filter['sort_by'], ['name', 'price', 'stock', 'created_at'])) {
            $direction = isset($filter['sort_direction']) && $filter['sort_direction'] === 'desc' ? 'desc' : 'asc';
            $builder->orderBy($filter['sort_by'], $direction);
        } else {
            // Ordenamiento por defecto
            $builder->orderBy('created_at', 'desc');
        }

        return $builder;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
