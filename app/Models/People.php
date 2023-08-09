<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class People extends Model
{
    use HasFactory;

    protected $fillable = [
        'passport_id',
        'gender',
        'firstname',
        'lastname',
        'type',
        'date_of_birth',
        'passport_issue_date',
        'passport_expiry_date',
        'arrival_date',
        'mobile_number',
        'police_arrival_reported_date',
        'police_departure_reported_date',
    ];
}
