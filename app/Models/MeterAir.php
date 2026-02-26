<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeterAir extends Model
{
    use SoftDeletes;

    protected $table = 'meter_air_readings';

    protected $fillable = [
        'lokasi',
        'tanggal',
        'jam',
        'meter_awal',
        'meter_akhir',
        'pemakaian',
        'foto',
        'keterangan',
        'status_meter',
        'petugas',
        'liter_per_detik'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'meter_awal' => 'decimal:2',
        'meter_akhir' => 'decimal:2',
        'pemakaian' => 'decimal:2'
    ];

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

    public static function getAllLokasiOptions()
    {
        $all = [];
        foreach (self::$lokasiOptions as $group => $options) {
            $all = array_merge($all, array_keys($options));
        }
        return $all;
    }

    public function getNamaLokasiAttribute()
    {
        foreach (self::$lokasiOptions as $group => $options) {
            if (isset($options[$this->lokasi])) {
                return $options[$this->lokasi];
            }
        }
        return $this->lokasi;
    }

    public function getGrupLokasiAttribute()
    {
        foreach (self::$lokasiOptions as $group => $options) {
            if (array_key_exists($this->lokasi, $options)) {
                return $group;
            }
        }
        return 'Lainnya';
    }

    public static function getLastReading($lokasi)
    {
        return self::where('lokasi', $lokasi)
            ->whereNotNull('meter_akhir')
            ->latest('tanggal')
            ->first();
    }
    
}