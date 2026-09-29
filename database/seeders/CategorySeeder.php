<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Jaringan', 'Hardware', 'Software', 'Akun/Akses'] as $name) {
            Category::query()->firstOrCreate(['name' => $name]);
        }
    }
}
