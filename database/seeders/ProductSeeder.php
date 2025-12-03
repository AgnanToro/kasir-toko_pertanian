<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $pupuk = Category::where('slug', 'pupuk')->first();
        $benih = Category::where('slug', 'benih')->first();
        $pestisida = Category::where('slug', 'pestisida')->first();
        $herbisida = Category::where('slug', 'herbisida')->first();
        $alat = Category::where('slug', 'alat-pertanian')->first();
        $insektisida = Category::where('slug', 'insektisida')->first();
        $organik = Category::where('slug', 'pupuk-organik')->first();
        $media = Category::where('slug', 'media-tanam')->first();

        $products = [
            // Pupuk
            [
                'category_id' => $pupuk->id,
                'name' => 'Pupuk Urea 50kg',
                'sku' => 'PRD-UREA50',
                'barcode' => '8991001234567',
                'description' => 'Pupuk Urea untuk tanaman padi dan jagung',
                'purchase_price' => 150000,
                'selling_price' => 175000,
                'stock' => 50,
                'minimum_stock' => 10,
                'unit' => 'karung',
            ],
            [
                'category_id' => $pupuk->id,
                'name' => 'Pupuk NPK Phonska 50kg',
                'sku' => 'PRD-NPK50',
                'barcode' => '8991001234568',
                'description' => 'Pupuk NPK lengkap untuk semua jenis tanaman',
                'purchase_price' => 180000,
                'selling_price' => 210000,
                'stock' => 40,
                'minimum_stock' => 10,
                'unit' => 'karung',
            ],
            [
                'category_id' => $pupuk->id,
                'name' => 'Pupuk TSP 50kg',
                'sku' => 'PRD-TSP50',
                'barcode' => '8991001234569',
                'description' => 'Pupuk Triple Super Phosphate',
                'purchase_price' => 160000,
                'selling_price' => 190000,
                'stock' => 30,
                'minimum_stock' => 10,
                'unit' => 'karung',
            ],
            [
                'category_id' => $pupuk->id,
                'name' => 'Pupuk KCL 50kg',
                'sku' => 'PRD-KCL50',
                'barcode' => '8991001234570',
                'description' => 'Pupuk Kalium Chloride',
                'purchase_price' => 170000,
                'selling_price' => 200000,
                'stock' => 25,
                'minimum_stock' => 8,
                'unit' => 'karung',
            ],

            // Benih
            [
                'category_id' => $benih->id,
                'name' => 'Benih Padi Hibrida 5kg',
                'sku' => 'PRD-PADI5',
                'barcode' => '8991002234567',
                'description' => 'Benih padi hibrida unggul hasil tinggi',
                'purchase_price' => 120000,
                'selling_price' => 150000,
                'stock' => 60,
                'minimum_stock' => 15,
                'unit' => 'kg',
            ],
            [
                'category_id' => $benih->id,
                'name' => 'Benih Jagung Hibrida 2kg',
                'sku' => 'PRD-JAGUNG2',
                'barcode' => '8991002234568',
                'description' => 'Benih jagung hibrida tahan penyakit',
                'purchase_price' => 80000,
                'selling_price' => 110000,
                'stock' => 45,
                'minimum_stock' => 10,
                'unit' => 'kg',
            ],
            [
                'category_id' => $benih->id,
                'name' => 'Benih Cabai Merah 100gr',
                'sku' => 'PRD-CABAI100',
                'barcode' => '8991002234569',
                'description' => 'Benih cabai merah keriting',
                'purchase_price' => 45000,
                'selling_price' => 65000,
                'stock' => 100,
                'minimum_stock' => 20,
                'unit' => 'pack',
            ],
            [
                'category_id' => $benih->id,
                'name' => 'Benih Tomat 50gr',
                'sku' => 'PRD-TOMAT50',
                'barcode' => '8991002234570',
                'description' => 'Benih tomat buah besar',
                'purchase_price' => 35000,
                'selling_price' => 50000,
                'stock' => 80,
                'minimum_stock' => 15,
                'unit' => 'pack',
            ],

            // Pestisida
            [
                'category_id' => $pestisida->id,
                'name' => 'Fungisida Antracol 1kg',
                'sku' => 'PRD-ANTRA1',
                'barcode' => '8991003234567',
                'description' => 'Fungisida untuk mencegah penyakit tanaman',
                'purchase_price' => 85000,
                'selling_price' => 110000,
                'stock' => 35,
                'minimum_stock' => 10,
                'unit' => 'kg',
            ],
            [
                'category_id' => $pestisida->id,
                'name' => 'Fungisida Dithane 1kg',
                'sku' => 'PRD-DITHA1',
                'barcode' => '8991003234568',
                'description' => 'Fungisida spektrum luas',
                'purchase_price' => 90000,
                'selling_price' => 115000,
                'stock' => 30,
                'minimum_stock' => 8,
                'unit' => 'kg',
            ],

            // Herbisida
            [
                'category_id' => $herbisida->id,
                'name' => 'Herbisida Roundup 500ml',
                'sku' => 'PRD-ROUND500',
                'barcode' => '8991004234567',
                'description' => 'Herbisida sistemik pembasmi gulma',
                'purchase_price' => 65000,
                'selling_price' => 85000,
                'stock' => 50,
                'minimum_stock' => 12,
                'unit' => 'botol',
            ],
            [
                'category_id' => $herbisida->id,
                'name' => 'Herbisida Ally 200gr',
                'sku' => 'PRD-ALLY200',
                'barcode' => '8991004234568',
                'description' => 'Herbisida untuk padi',
                'purchase_price' => 55000,
                'selling_price' => 75000,
                'stock' => 40,
                'minimum_stock' => 10,
                'unit' => 'pack',
            ],

            // Insektisida
            [
                'category_id' => $insektisida->id,
                'name' => 'Insektisida Decis 250ml',
                'sku' => 'PRD-DECIS250',
                'barcode' => '8991005234567',
                'description' => 'Insektisida pembasmi hama serangga',
                'purchase_price' => 75000,
                'selling_price' => 95000,
                'stock' => 45,
                'minimum_stock' => 10,
                'unit' => 'botol',
            ],
            [
                'category_id' => $insektisida->id,
                'name' => 'Insektisida Curacron 500ml',
                'sku' => 'PRD-CURA500',
                'barcode' => '8991005234568',
                'description' => 'Insektisida sistemik kontak',
                'purchase_price' => 85000,
                'selling_price' => 110000,
                'stock' => 35,
                'minimum_stock' => 8,
                'unit' => 'botol',
            ],

            // Alat Pertanian
            [
                'category_id' => $alat->id,
                'name' => 'Cangkul Baja',
                'sku' => 'PRD-CANGKUL',
                'barcode' => '8991006234567',
                'description' => 'Cangkul baja berkualitas tinggi',
                'purchase_price' => 45000,
                'selling_price' => 65000,
                'stock' => 25,
                'minimum_stock' => 5,
                'unit' => 'pcs',
            ],
            [
                'category_id' => $alat->id,
                'name' => 'Sprayer Manual 16 Liter',
                'sku' => 'PRD-SPRAY16',
                'barcode' => '8991006234568',
                'description' => 'Alat semprot manual kapasitas 16 liter',
                'purchase_price' => 150000,
                'selling_price' => 200000,
                'stock' => 15,
                'minimum_stock' => 5,
                'unit' => 'pcs',
            ],
            [
                'category_id' => $alat->id,
                'name' => 'Sabit Panen',
                'sku' => 'PRD-SABIT',
                'barcode' => '8991006234569',
                'description' => 'Sabit untuk panen padi',
                'purchase_price' => 25000,
                'selling_price' => 35000,
                'stock' => 30,
                'minimum_stock' => 8,
                'unit' => 'pcs',
            ],
            [
                'category_id' => $alat->id,
                'name' => 'Selang Air 20 Meter',
                'sku' => 'PRD-SELANG20',
                'barcode' => '8991006234570',
                'description' => 'Selang air untuk irigasi',
                'purchase_price' => 75000,
                'selling_price' => 100000,
                'stock' => 20,
                'minimum_stock' => 5,
                'unit' => 'roll',
            ],

            // Pupuk Organik
            [
                'category_id' => $organik->id,
                'name' => 'Pupuk Kompos 20kg',
                'sku' => 'PRD-KOMPOS20',
                'barcode' => '8991007234567',
                'description' => 'Pupuk kompos organik',
                'purchase_price' => 30000,
                'selling_price' => 45000,
                'stock' => 60,
                'minimum_stock' => 15,
                'unit' => 'karung',
            ],
            [
                'category_id' => $organik->id,
                'name' => 'Pupuk Kandang 25kg',
                'sku' => 'PRD-KANDANG25',
                'barcode' => '8991007234568',
                'description' => 'Pupuk kandang sapi matang',
                'purchase_price' => 25000,
                'selling_price' => 40000,
                'stock' => 70,
                'minimum_stock' => 20,
                'unit' => 'karung',
            ],

            // Media Tanam
            [
                'category_id' => $media->id,
                'name' => 'Tanah Humus 10kg',
                'sku' => 'PRD-HUMUS10',
                'barcode' => '8991008234567',
                'description' => 'Tanah humus subur untuk media tanam',
                'purchase_price' => 15000,
                'selling_price' => 25000,
                'stock' => 100,
                'minimum_stock' => 20,
                'unit' => 'karung',
            ],
            [
                'category_id' => $media->id,
                'name' => 'Sekam Bakar 10kg',
                'sku' => 'PRD-SEKAM10',
                'barcode' => '8991008234568',
                'description' => 'Sekam padi bakar untuk media tanam',
                'purchase_price' => 10000,
                'selling_price' => 18000,
                'stock' => 80,
                'minimum_stock' => 15,
                'unit' => 'karung',
            ],
            [
                'category_id' => $media->id,
                'name' => 'Cocopeat 5kg',
                'sku' => 'PRD-COCO5',
                'barcode' => '8991008234569',
                'description' => 'Sabut kelapa untuk media tanam',
                'purchase_price' => 20000,
                'selling_price' => 30000,
                'stock' => 50,
                'minimum_stock' => 10,
                'unit' => 'pack',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
