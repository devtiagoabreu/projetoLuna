<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessionalAvailability extends Model
{
    use HasFactory;

    protected $table = 'professional_availability';
    public $timestamps = false;
}
