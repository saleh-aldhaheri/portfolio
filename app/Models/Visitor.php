<?php

namespace App\Models;

use Database\Factories\VisitorFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visitor extends Model
{
    /** @use HasFactory<VisitorFactory> */
    use HasFactory;

    use Prunable;

    protected $fillable = [
        'uuid',
        'name',
        'company',
        'phone',
        'occupation',
        'ip_address',
        'browser',
        'os',
        'email',
        'city',
        'country',
        'language',
        'referrer',
    ];

    public function prunable(): Builder
    {
        return static::where(
            'updated_at',
            '<',
            now()->subMonths(3)
        );
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AnalyticsLog::class);
    }

    public function contactMessages(): HasMany
    {
        return $this->hasMany(ContactMessage::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
