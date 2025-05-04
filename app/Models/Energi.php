<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Energi extends Model
{
    use HasFactory;

    protected $table = 'energi'; // Atur agar tidak menjadi 'energis'
}

