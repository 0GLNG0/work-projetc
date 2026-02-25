<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeterListrik extends Model
{
    use SoftDeletes;

    protected $table = 'meter_listrik_readings';

    protected $fillable = [
        'lokasi',
        'tanggal',
        'jam',
        'nomor_id',
        'meter_awal',
        'meter_akhir',
        'pemakaian',
        'foto',
        'keterangan',
        'status_meter',
        'petugas'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'meter_awal' => 'decimal:2',
        'meter_akhir' => 'decimal:2',
        'pemakaian' => 'decimal:2'
    ];

    // Daftar lokasi (sama seperti sebelumnya)
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
                'kuwak1' => ['Udin', 'Adi', 'Arif', 'Slamet'],
                'pesantren' => ['Udin', 'Adi', 'Arif', 'Slamet'],
                'tosaren' => ['Udin', 'Adi', 'Arif', 'Slamet'],
                'kleco' => ['Udin', 'Adi', 'Arif', 'Slamet'],
                'ngronggo' => ['Udin', 'Adi', 'Arif', 'Slamet'],
                'tamanan' => ['Alfin', 'Adit', 'Yudit', 'Rizki'],
                'wilis utara' => ['Alfin', 'Adit', 'Yudit', 'Rizki'],
                'wilis selatan' => ['Alfin', 'Adit', 'Yudit', 'Rizki'],
                'unik' => ['Alfin', 'Adit', 'Yudit', 'Rizki'],
                'pojok' => ['Alfin', 'Adit', 'Yudit', 'Rizki'],
                'sukorame' => ['Alfin', 'Adit', 'Yudit', 'Rizki'],
                'gayam' => ['Alfin', 'Adit', 'Yudit', 'Rizki'],
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