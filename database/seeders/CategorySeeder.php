<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Kamera',
                'description' => 'Kategori kamera untuk kebutuhan fotografi.',
            ],
            [
                'name' => 'Lensa',
                'description' => 'Kategori lensa untuk berbagai kebutuhan pemotretan.',
            ],
            [
                'name' => 'Pencahayaan',
                'description' => 'Peralatan pencahayaan untuk fotografi.',
            ],
            [
                'name' => 'Aksesori',
                'description' => 'Aksesori pendukung kegiatan fotografi.',
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                [
                    'description' => $category['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
