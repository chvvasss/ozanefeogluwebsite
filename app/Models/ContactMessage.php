<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    public const STATUSES = ['new', 'read', 'replied', 'spam'];

    protected $fillable = [
        'name',
        'email',
        'subject',
        'body',
        'status',
        'read_at',
        'retention_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'retention_expires_at' => 'datetime',
        ];
    }
}
