<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Bank;

class BankSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Bank::truncate();

        $data = [
            [
                'bank_name' => 'Bank Central Asia',
                'short_name' => 'BCA',
            ],
            [
                'bank_name' => 'Bank Mandiri',
                'short_name' => 'MANDIRI',
            ],
            [
                'bank_name' => 'Bank Rakyat Indonesia',
                'short_name' => 'BRI',
            ],
            [
                'bank_name' => 'Bank Negara Indonesia',
                'short_name' => 'BNI',
            ],
            [
                'bank_name' => 'Bank Tabungan Negara',
                'short_name' => 'BTN',
            ],
            [
                'bank_name' => 'CIMB Niaga',
                'short_name' => 'CIMB',
            ],
            [
                'bank_name' => 'Bank Danamon',
                'short_name' => 'DANAMON',
            ],
            [
                'bank_name' => 'Bank Permata',
                'short_name' => 'PERMATA',
            ],
            [
                'bank_name' => 'Bank Syariah Indonesia',
                'short_name' => 'BSI',
            ],
            [
                'bank_name' => 'Bank Mega',
                'short_name' => 'MEGA',
            ],
            [
                'bank_name' => 'Bank OCBC NISP',
                'short_name' => 'OCBC',
            ],
            [
                'bank_name' => 'Panin Bank',
                'short_name' => 'PANIN',
            ],
            [
                'bank_name' => 'Bank Bukopin',
                'short_name' => 'BUKOPIN',
            ],
            [
                'bank_name' => 'Bank Mayapada',
                'short_name' => 'MAYAPADA',
            ],
            [
                'bank_name' => 'Bank Sinarmas',
                'short_name' => 'SINARMAS',
            ],
            [
                'bank_name' => 'Bank BTPN',
                'short_name' => 'BTPN',
            ],
            [
                'bank_name' => 'Bank Jago',
                'short_name' => 'JAGO',
            ],
            [
                'bank_name' => 'Bank Neo Commerce',
                'short_name' => 'NEO',
            ],
            [
                'bank_name' => 'SeaBank Indonesia',
                'short_name' => 'SEABANK',
            ],
            [
                'bank_name' => 'GoPay / GoTo Financial',
                'short_name' => 'GOPAY',
            ],
            [
                'bank_name' => 'OVO',
                'short_name' => 'OVO',
            ],
            [
                'bank_name' => 'DANA',
                'short_name' => 'DANA',
            ],
            [
                'bank_name' => 'LinkAja',
                'short_name' => 'LINKAJA',
            ],
        ];

        Bank::insert($data);
    }
}
