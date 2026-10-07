<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    public const STATUSES = [
        'new' => 'New',
        'read' => 'Read',
        'archived' => 'Archived',
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'enquiry_type',
        'enquiry_type_label',
        'interest',
        'message',
        'consent',
        'status',
        'emailed_at',
        'mail_error',
    ];

    protected function casts(): array
    {
        return [
            'consent' => 'boolean',
            'emailed_at' => 'datetime',
        ];
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }
}
