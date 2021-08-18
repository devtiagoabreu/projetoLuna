<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAppointments extends Model
{
    use HasFactory;

    protected $table = 'user_appointments';
    public $timestamps = false;
}
