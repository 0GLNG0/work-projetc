<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    protected $table = 'lokasis';
    
    protected $fillable = [
        'nama_lokasi',
        'waktu_aktif_pompa' // Variabel kunci untuk rumus Excel kamu
    ];
}
