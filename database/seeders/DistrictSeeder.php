<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $districts = [
            'Baksa', 'Barpeta', 'Biswanath', 'Bongaigaon', 'Cachar',
            'Charaideo', 'Chirang', 'Darrang', 'Dhemaji', 'Dhubri',
            'Dibrugarh', 'Dima Hasao', 'Goalpara', 'Golaghat', 'Hailakandi',
            'Hojai', 'Jorhat', 'Kamrup', 'Kamrup Metropolitan', 'Karbi Anglong',
            'Karimganj', 'Kokrajhar', 'Lakhimpur', 'Majuli', 'Morigaon',
            'Nagaon', 'Nalbari', 'Sivasagar', 'Sonitpur', 'South Salmara-Mankachar',
            'Tinsukia', 'Udalguri', 'West Karbi Anglong', 'Haflong', 'Bakalia'
        ];

        foreach ($districts as $district) {
            if (!DB::table('districts')->where('name', $district)->exists()) {
                DB::table('districts')->insert([
                    'name' => $district,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }
        }
    }
}
