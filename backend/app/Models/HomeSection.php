<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSection extends Model
{
    protected $fillable = ['slug', 'content', 'updated_by'];

    protected function casts(): array
    {
        return ['content' => 'array'];
    }

    public function mediaUrl(string $key): ?string
    {
        $path = $this->content[$key] ?? null;
        if (! $path) {
            return null;
        }

        return str_starts_with($path, 'assets/') ? asset($path) : asset('storage/'.$path);
    }
}
