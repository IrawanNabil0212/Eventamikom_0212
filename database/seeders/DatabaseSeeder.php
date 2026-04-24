<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Event;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Admin Utama
        User::updateOrCreate(
            ['email' => 'admin@amikom.ac.id'],
            [
                'name' => 'Admin Amikom',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // 2. Insert 3 Kategori Event (Syarat Minimal Tugas)
        $itCategory = Category::firstOrCreate(
            ['slug' => 'it-programming'],
            ['name' => 'IT & Programming']
        );

        $entCategory = Category::firstOrCreate(
            ['slug' => 'entertainment'],
            ['name' => 'Entertainment']
        );

        $sportCategory = Category::firstOrCreate(
            ['slug' => 'e-sports'],
            ['name' => 'E-Sports']
        );

        // 3. Insert 6 Sampel Events (Syarat Minimal Tugas)
        $events = [
            // Kategori IT
            [
                'category_id' => $itCategory->id,
                'title' => 'UI/UX Masterclass: From Zero to Hero',
                'description' => 'Belajar desain antarmuka modern bersama mentor industri ternama.',
                'date' => '2026-05-15 09:00:00',
                'location' => 'Cinema Amikom',
                'price' => 75000,
                'stock' => 50,
                'poster_path' => 'posters/uiux.png',
            ],
            [
                'category_id' => $itCategory->id,
                'title' => 'Laravel Advanced Workshop',
                'description' => 'Bedah tuntas fitur terbaru Laravel untuk aplikasi skala besar.',
                'date' => '2026-06-10 13:00:00',
                'location' => 'Lab ICT Amikom',
                'price' => 150000,
                'stock' => 30,
                'poster_path' => 'posters/laravel.png',
            ],
            // Kategori Entertainment
            [
                'category_id' => $entCategory->id,
                'title' => 'Amikom Jazz Night 2026',
                'description' => 'Nikmati malam syahdu dengan alunan musik jazz di halaman kampus.',
                'date' => '2026-07-20 19:30:00',
                'location' => 'Amikom Baru',
                'price' => 50000,
                'stock' => 200,
                'poster_path' => 'posters/jazz.png',
            ],
            [
                'category_id' => $entCategory->id,
                'title' => 'Indie Pop Fest',
                'description' => 'Konser musik indie lokal Yogyakarta yang penuh energi.',
                'date' => '2026-08-05 16:00:00',
                'location' => 'Gedung Olahraga',
                'price' => 35000,
                'stock' => 500,
                'poster_path' => 'posters/indie.png',
            ],
            // Kategori E-Sports
            [
                'category_id' => $sportCategory->id,
                'title' => 'E-Sport U-Champ: Mobile Legends',
                'description' => 'Turnamen bergengsi antar mahasiswa untuk memperebutkan gelar MVP.',
                'date' => '2026-09-12 10:00:00',
                'location' => 'Student Center',
                'price' => 25000,
                'stock' => 16,
                'poster_path' => 'posters/mlbb.png',
            ],
            [
                'category_id' => $sportCategory->id,
                'title' => 'Valorant Campus Championship',
                'description' => 'Tunjukkan akurasi aim-mu di turnamen Valorant terbesar tahun ini.',
                'date' => '2026-10-01 08:00:00',
                'location' => 'Inkubator Amikom',
                'price' => 50000,
                'stock' => 32,
                'poster_path' => 'posters/valorant.png',
            ],
        ];

        foreach ($events as $eventData) {
            Event::updateOrCreate(
                ['title' => $eventData['title']],
                $eventData
            );
        }
    }
}