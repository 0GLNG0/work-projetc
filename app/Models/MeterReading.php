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
            'kuwak2' => 'Kuwak 2',
            'kuwak3' => 'Kuwak 3',
            'tosaren' => 'Tosaren',
            'ngronggo' => 'Ngronggo',
            'kleco' => 'Kleco',
            'balowerti' => 'Balowerti',
            'rusunawa' => 'Rusunawa',
            'balai kota' => 'Balai Kota',
        ],
        'Timur Sungai' => [
            'gayam' => 'Gayam',
            'ngampel' => 'Ngampel',
            'sukorame' => 'Sukorame',
            'pojok' => 'Pojok',
            'bnn' => 'BNN',
            'unik' => 'Unik',
            'goa barong' => 'Goa Barong',
            'wilis utara' => 'Wilis Utara',
            'wilis selatan' => 'Wilis Selatan',
            'tamanan1' => 'Tamanan1',
            'tamanan2' => 'Tamanan2',
        ]
    ];

    public static $petugasPerLokasi = [
                'kuwak1' => ['Rizki Bagus'],
                'kuwak2' => ['Rizki Bagus'],
                'kuwak3' => ['Rizki Bagus'],
                'balowerti' => ['Rizki Bagus'], 
                'rusunawa' => ['Rizki Bagus'],
                'balai kota' => ['Rizki Bagus'],
                'gayam' => ['Agus Salim'],
                'ngampel' => ['Agus Salim'],
                'wilis selatan' => ['Agus Salim'],
                'wilis utara' => ['Agus Salim'],
                'tamanan1' => ['Agus Salim'],
                'tamanan2' => ['Agus Salim'],
                'sukorame' => ['Fadkul Adim'],
                'pojok' => ['Fadkul Adim'],
                'bnn' => ['Fadkul Adim'],
                'unik' => ['Fadkul Adim'],
                'tosaren' => ['Sigit Santoso'],
                'ngronggo' => ['Sigit Santoso'],
                'kleco' => ['Sigit Santoso'],
                
                'goa barong' => ['Fadkul Adim'], //belum ada nama
            ];
            
            // Method untuk ambil petugas berdasarkan lokasi
            public static function getPetugasByLokasi($lokasi)
            {
                return self::$petugasPerLokasi[$lokasi] ?? [];
            }

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

    // Daftar petugas per lokasi
// Daftar petugas per lokasi (bisa ditambah/diedit)
}