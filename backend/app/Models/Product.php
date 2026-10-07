<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'category',
        'size',
        'price',
        'image_path',
        'image_alt',
        'short_description',
        'description',
        'ingredients',
        'usage',
        'is_published',
        'sort_order',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'ingredients' => 'array',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function imageUrl(): ?string
    {
        return $this->image_path ? asset('storage/'.$this->image_path) : null;
    }
}
