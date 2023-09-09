<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Passport;

class People extends Model
{
    use HasFactory;

    protected $fillable = [
        'firstname',
        'lastname',
        'firstname_th',
        'lastname_th',
        'role',
        'gender',
        'mobile_number',
        'date_of_birth',
        'national_id_number',
    ];


    public function passports(): HasMany
    {
        return $this->hasMany(Passport::class);
    }
}
