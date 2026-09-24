<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Mengecek apakah user admin sudah ada sebelum membuat
        $adminEmail = 'admin@admin.com';
        
        if (!User::where('email', $adminEmail)->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => $adminEmail,
                'password' => Hash::make('password'),
                'status' => 1, // Admin / Akses Penuh
                'status_admin' => 1, 
                // 'toko_id' => null, 
            ]);
        }

        // Menambahkan role Sales (Hanya akses penjualan & cetak)
        if (!User::where('email', 'sales@admin.com')->exists()) {
            User::create([
                'name' => 'Agus (Sales)',
                'email' => 'sales@admin.com',
                'password' => Hash::make('password'),
                'status' => 2, // Karyawan biasa
                'toko_id' => 1, // Harus ditetapkan ke cabang 1
                'user_menu' => 'Penjualan,Invoice,Quotation'
            ]);
        }

        // Menambahkan role Gudang (Hanya akses inventaris)
        if (!User::where('email', 'gudang@admin.com')->exists()) {
            User::create([
                'name' => 'Budi (Gudang)',
                'email' => 'gudang@admin.com',
                'password' => Hash::make('password'),
                'status' => 2, 
                'toko_id' => 1,
                'user_menu' => 'Barang,Kategori Barang,Barang Masuk,Stock,Pindah Toko,Serial Number'
            ]);
        }

        // Menambahkan role Manager Cabang (Karyawan dengan akses nyaris penuh di cabangnya)
        if (!User::where('email', 'manager@admin.com')->exists()) {
            User::create([
                'name' => 'Citra (Manager)',
                'email' => 'manager@admin.com',
                'password' => Hash::make('password'),
                'status' => 2, 
                'toko_id' => 1,
                'user_menu' => 'Barang,User,Toko,Supplier,Barang Masuk,Stock,Setting,Serial Number,Search,Report,Quotation,Purchase Order,Pindah Toko,Penjualan,Kategori Barang,Invoice'
            ]);
        }
    }
}
