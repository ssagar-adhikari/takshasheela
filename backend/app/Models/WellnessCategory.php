<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WellnessCategory extends Model
{
    protected $fillable = [
        'name', 'slug', 'nav_description', 'hero_eyebrow', 'hero_title', 'hero_description',
        'hero_image_path', 'hero_image_alt', 'section_eyebrow', 'section_title',
        'information_eyebrow', 'information_title', 'information_body',
        'cta_eyebrow', 'cta_title', 'cta_body', 'is_published', 'sort_order', 'updated_by',
    ];

    protected function casts(): array
    {
        return ['is_published' => 'boolean', 'sort_order' => 'integer'];
    }

    public function subcategories(): HasMany
    {
        return $this->hasMany(WellnessSubcategory::class)->orderBy('sort_order')->orderBy('name');
    }

    public function offerings(): HasMany
    {
        return $this->hasMany(WellnessOffering::class)->orderBy('sort_order')->orderBy('name');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function heroImageUrl(): ?string
    {
        return $this->hero_image_path ? asset('storage/'.$this->hero_image_path) : null;
    }

    public function publicUrl(): string
    {
        return match ($this->slug) {
            'programs' => route('programs.index'),
            'therapies' => route('therapies.index'),
            'trainings' => route('trainings.index'),
            default => route('wellness.categories.show', $this->slug),
        };
    }
}
