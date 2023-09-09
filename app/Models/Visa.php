<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\People;
use App\Models\Passport;
use Illuminate\Database\Eloquent\Factories\BelongsToRelationship;

class Visa extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_date',
        'expiry_date',
    ];

    public function passport(): BelongsTo
    {
        return $this->belongsTo(Passport::class);
    }

}
