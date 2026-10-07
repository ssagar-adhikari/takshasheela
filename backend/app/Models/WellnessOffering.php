<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WellnessOffering extends Model
{
    protected $fillable = [
        'wellness_category_id', 'wellness_subcategory_id', 'name', 'slug', 'eyebrow', 'duration', 'image_path',
        'image_alt', 'short_description', 'description', 'highlights', 'includes', 'itinerary',
        'ideal_for', 'secondary_title', 'secondary_content', 'notice', 'is_published',
        'sort_order', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'highlights' => 'array',
            'includes' => 'array',
            'itinerary' => 'array',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(WellnessCategory::class, 'wellness_category_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(WellnessSubcategory::class, 'wellness_subcategory_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->whereHas('subcategory', fn (Builder $query) => $query->published());
    }

    public function imageUrl(): ?string
    {
        return $this->image_path ? asset('storage/'.$this->image_path) : null;
    }

    public function publicUrl(): string
    {
        $category = $this->relationLoaded('category') ? $this->category : $this->category()->firstOrFail();

        return match ($category->slug) {
            'programs' => route('programs.show', $this->slug),
            'therapies' => route('therapies.show', $this->slug),
            'trainings' => route('trainings.show', $this->slug),
            default => route('wellness.offerings.show', [$category->slug, $this->slug]),
        };
    }
}
