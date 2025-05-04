<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    protected $table = 'jadwal';
    protected $fillable = ['lampu_id', 'hari', 'waktu_nyala', 'waktu_mati'];
    
    public function lampu()
    {
        return $this->belongsTo(Lampu::class);
    }
}
