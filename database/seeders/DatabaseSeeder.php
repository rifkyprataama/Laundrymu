<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Service;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Data Layanan Laundry
        Service::create([
            'name' => 'Cuci Komplit (Reguler)',
            'price' => 7000,
            'unit' => 'kg'
        ]);

        Service::create([
            'name' => 'Cuci Kering (Setrika)',
            'price' => 5000,
            'unit' => 'kg'
        ]);

        Service::create([
            'name' => 'Cuci Satuan (Bed Cover)',
            'price' => 25000,
            'unit' => 'pcs'
        ]);

        // 2. Buat Data Pelanggan Contoh
        Customer::create([
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'address' => 'Jl. Mawar No. 12',
            'is_member' => true // Kita tes fitur member
        ]);

        Customer::create([
            'name' => 'Siti Aminah',
            'phone' => '089876543210',
            'address' => 'Jl. Melati No. 5',
            'is_member' => false
        ]);
    }
}