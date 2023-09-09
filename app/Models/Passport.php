<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\People;
use App\Models\Visa;

class Passport extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_date',
        'expiry_date',
    ];

    public function people(): BelongsTo
    {
        return $this->belongsTo(People::class);
    }

    public function visas(): HasMany
    {
        return $this->hasMany(Visa::class);
    }
}
