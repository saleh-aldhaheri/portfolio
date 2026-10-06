<?php

namespace App\Models;

use Database\Factories\VisitorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visitor extends Model
{
    /** @use HasFactory<VisitorFactory> */
    use HasFactory;

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

    public function logs(): HasMany
    {
        return $this->hasMany(AnalyticsLog::class);
    }
}
