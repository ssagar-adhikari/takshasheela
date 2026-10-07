<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WellnessSubcategory extends Model
{
    protected $fillable = [
        'wellness_category_id', 'name', 'slug', 'nav_description', 'description', 'is_published', 'sort_order', 'updated_by',
    ];

    protected function casts(): array
    {
        return ['is_published' => 'boolean', 'sort_order' => 'integer'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(WellnessCategory::class, 'wellness_category_id');
    }

    public function offerings(): HasMany
    {
        return $this->hasMany(WellnessOffering::class)->orderBy('sort_order')->orderBy('name');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
