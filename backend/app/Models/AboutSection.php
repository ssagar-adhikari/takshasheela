<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutSection extends Model
{
    protected $fillable = ['slug', 'content', 'updated_by'];

    protected function casts(): array
    {
        return ['content' => 'array'];
    }

    public function imageUrl(?string $path): ?string
    {
        return $path ? asset('storage/'.$path) : null;
    }
}
