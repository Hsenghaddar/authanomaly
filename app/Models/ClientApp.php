<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientApp extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'environment',
        'api_key',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function authEvents(): HasMany
    {
        return $this->hasMany(AuthEvent::class);
    }

    public function rules(): HasMany
    {
        return $this->hasMany(DetectionRule::class);
    }

    public function cases(): HasMany
    {
        return $this->hasMany(SecurityCase::class);
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }
}