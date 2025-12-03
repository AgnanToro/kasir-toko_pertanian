<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Pupuk',
                'description' => 'Berbagai jenis pupuk untuk pertanian',
                'icon' => '🌱',
                'is_active' => true,
            ],
            [
                'name' => 'Benih',
                'description' => 'Benih tanaman berkualitas',
                'icon' => '🌾',
                'is_active' => true,
            ],
            [
                'name' => 'Pestisida',
                'description' => 'Pestisida dan fungisida',
                'icon' => '🧪',
                'is_active' => true,
            ],
            [
                'name' => 'Herbisida',
                'description' => 'Pembasmi gulma dan rumput liar',
                'icon' => '💊',
                'is_active' => true,
            ],
            [
                'name' => 'Alat Pertanian',
                'description' => 'Peralatan dan perlengkapan pertanian',
                'icon' => '🔧',
                'is_active' => true,
            ],
            [
                'name' => 'Insektisida',
                'description' => 'Pembasmi hama serangga',
                'icon' => '🦟',
                'is_active' => true,
            ],
            [
                'name' => 'Pupuk Organik',
                'description' => 'Pupuk alami dan organik',
                'icon' => '♻️',
                'is_active' => true,
            ],
            [
                'name' => 'Media Tanam',
                'description' => 'Tanah, kompos, dan media tanam lainnya',
                'icon' => '🪴',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
