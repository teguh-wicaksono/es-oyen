<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\StokBahan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password123');

        User::query()->updateOrCreate(
            ['email' => 'owner@wanacafe.com'],
            ['name' => 'Owner Wana', 'username' => 'owner', 'password' => $password, 'role' => 'owner']
        );

        User::query()->updateOrCreate(
            ['email' => 'kasir@wanacafe.com'],
            ['name' => 'Kasir Satu', 'username' => 'kasir', 'password' => $password, 'role' => 'kasir']
        );

        User::query()->updateOrCreate(
            ['email' => 'dapur@wanacafe.com'],
            ['name' => 'Dapur Utama', 'username' => 'dapur', 'password' => $password, 'role' => 'dapur']
        );

        $kategori = collect(['Minuman', 'Makanan', 'Snack', 'Dessert'])
            ->mapWithKeys(fn (string $nama) => [
                $nama => Kategori::query()->updateOrCreate(['nama' => $nama]),
            ]);

        $produk = [
            ['Minuman', 'Es Kopi Susu', 'Espresso blend dengan susu segar dan gula aren', 22000, 50, 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?auto=format&fit=crop&w=900&q=80'],
            ['Minuman', 'Matcha Latte', 'Matcha premium Jepang dengan susu oat', 25000, 40, 'https://images.unsplash.com/photo-1515823662972-da6a2e4d3002?auto=format&fit=crop&w=900&q=80'],
            ['Minuman', 'Americano', 'Double shot espresso dengan air panas', 18000, 60, 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=900&q=80'],
            ['Minuman', 'Teh Tarik', 'Teh kental dengan susu kental manis', 15000, 80, 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?auto=format&fit=crop&w=900&q=80'],
            ['Minuman', 'Cokelat Hangat', 'Dark chocolate blend dengan susu full cream', 20000, 45, 'https://images.unsplash.com/photo-1542990253-a781e04c0082?auto=format&fit=crop&w=900&q=80'],
            ['Makanan', 'Nasi Goreng Cafe', 'Nasi goreng spesial dengan telur dan ayam', 28000, 30, 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?auto=format&fit=crop&w=900&q=80'],
            ['Makanan', 'Pisang Bakar', 'Pisang kepok bakar dengan keju dan cokelat', 18000, 35, 'https://images.unsplash.com/photo-1528825871115-3581a5387919?auto=format&fit=crop&w=900&q=80'],
            ['Makanan', 'Roti Bakar', 'Roti tawar bakar dengan selai dan mentega', 16000, 40, 'https://images.unsplash.com/photo-1484723091739-30a097e8f929?auto=format&fit=crop&w=900&q=80'],
            ['Snack', 'Kentang Goreng', 'French fries crispy dengan saus sambal', 15000, 50, 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?auto=format&fit=crop&w=900&q=80'],
            ['Snack', 'Risoles Mayo', 'Risoles isi sayur dengan mayonaise', 12000, 60, 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?auto=format&fit=crop&w=900&q=80'],
            ['Dessert', 'Pancake', 'Fluffy pancake dengan sirup maple dan buah', 22000, 25, 'https://images.unsplash.com/photo-1528207776546-365bb710ee93?auto=format&fit=crop&w=900&q=80'],
            ['Dessert', 'Es Krim 2 Scoop', 'Pilihan 2 rasa ice cream premium', 18000, 30, 'https://images.unsplash.com/photo-1567206563064-6f60f40a2b57?auto=format&fit=crop&w=900&q=80'],
        ];

        foreach ($produk as [$namaKategori, $nama, $deskripsi, $harga, $stok, $gambar]) {
            Produk::query()->updateOrCreate(
                ['nama' => $nama],
                [
                    'kategori_id' => $kategori[$namaKategori]->id,
                    'deskripsi' => $deskripsi,
                    'harga' => $harga,
                    'stok' => $stok,
                    'gambar' => $gambar,
                    'aktif' => true,
                ]
            );
        }

        $stokBahan = [
            ['Kopi Espresso', 2.5, 'kg', 0.5],
            ['Susu Full Cream', 10, 'ltr', 2],
            ['Gula Aren', 3, 'kg', 0.5],
            ['Matcha Powder', 1, 'kg', 0.2],
            ['Cokelat Bubuk', 2, 'kg', 0.5],
            ['Tepung Terigu', 5, 'kg', 1],
            ['Telur Ayam', 60, 'pcs', 10],
            ['Kentang', 10, 'kg', 2],
            ['Pisang Kepok', 50, 'pcs', 10],
            ['Es Batu', 20, 'kg', 5],
        ];

        foreach ($stokBahan as [$nama, $jumlah, $satuan, $stokMinimum]) {
            StokBahan::query()->updateOrCreate(
                ['nama' => $nama],
                ['jumlah' => $jumlah, 'satuan' => $satuan, 'stok_minimum' => $stokMinimum]
            );
        }
    }
}
