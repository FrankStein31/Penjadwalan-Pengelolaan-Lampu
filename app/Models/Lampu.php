<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lampu extends Model
{
    use HasFactory;

    protected $table = 'lampu'; 
    protected $fillable = ['nama_lampu', 'lokasi', 'status', 'intensitas'];
    
    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }
    
    public function energi()
    {
        return $this->hasMany(Energi::class);
    }
    
    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class);
    }
}
