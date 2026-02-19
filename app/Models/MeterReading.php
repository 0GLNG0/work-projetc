<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeterReading extends Model
{
    use SoftDeletes;

    protected $table = 'water_meter_readings';

    protected $fillable = [
    'lokasi',
    'jam',
    'tanggal',
    'meter_air',
    'meter_listrik',
    'meter_air_sebelumnya',  // <- Apakah ini ada di form? TIDAK
    'pemakaian_air',          // <- Apakah ini ada di form? TIDAK
    'meter_listrik_sebelumnya', // <- Apakah ini ada di form? TIDAK
    'pemakaian_listrik',       // <- Apakah ini ada di form? TIDAK
    'foto',                    // <- Ada
    'keterangan',              // <- Ada
    'status_meter',            // <- Ada
    'petugas'
    ];

     public static $statusMeter = [
        'normal' => '✅ Normal',
        'error' => '❌ Error / Rusak',
        'perbaikan' => '🔧 Dalam Perbaikan',
        'gangguan' => '⚠️ Gangguan Lainnya'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'meter_air' => 'decimal:2',
        'meter_listrik' => 'decimal:2',
        'pemakaian_air' => 'decimal:2',
        'pemakaian_listrik' => 'decimal:2'
    ];

    // Daftar lokasi dengan optgroup
    public static $lokasiOptions = [
        'Barat Sungai' => [
            'kuwak1' => 'Kuwak 1',
            'pesantren' => 'Pesantren',
            'tosaren' => 'Tosaren',
            'kleco' => 'Kleco',
            'ngronggo' => 'Ngronggo',
        ],
        'Timur Sungai' => [
            'tamanan' => 'Tamanan',
            'wilis utara' => 'Wilis Utara',
            'wilis selatan' => 'Wilis Selatan',
            'unik' => 'Unik',
            'pojok' => 'Pojok',
            'sukorame' => 'Sukorame',
            'gayam' => 'Gayam',
        ]
    ];

    // Ambil data pembacaan terakhir untuk lokasi yang sama
    public static function getPreviousReading($lokasi)
    {
        return self::where('lokasi', $lokasi)
            ->whereNotNull('meter_air')
            ->whereNotNull('meter_listrik')
            ->latest('tanggal')
            ->first();
    }

    // Get all lokasi options for validation
    public static function getAllLokasiOptions()
    {
        $all = [];
        foreach (self::$lokasiOptions as $group => $options) {
            $all = array_merge($all, array_keys($options));
        }
        return $all;
    }

    // Accessor untuk nama lokasi yang bagus
    public function getNamaLokasiAttribute()
    {
        foreach (self::$lokasiOptions as $group => $options) {
            if (isset($options[$this->lokasi])) {
                return $options[$this->lokasi];
            }
        }
        return $this->lokasi;
    }

       // Accessor untuk grup lokasi
    public function getGrupLokasiAttribute()
    {
        foreach (self::$lokasiOptions as $group => $options) {
            if (array_key_exists($this->lokasi, $options)) {
                return $group;
            }
        }
        return 'Lainnya';
    }
}