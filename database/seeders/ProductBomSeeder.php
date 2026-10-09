<?php

namespace Database\Seeders;

use App\Models\BillOfMaterial;
use App\Models\Material;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductBomSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Contoh Bahan Baku
        $platBesi = Material::create([
            'code'          => 'MAT-001',
            'name'          => 'Plat Besi 2mm',
            'unit'          => 'kg',
            'current_stock' => 500.00,
            'safety_stock'  => 50.00,
        ]);

        $catHitam = Material::create([
            'code'          => 'MAT-002',
            'name'          => 'Cat Epoxy Hitam',
            'unit'          => 'liter',
            'current_stock' => 100.00,
            'safety_stock'  => 10.00,
        ]);

        // 2. Buat Contoh Produk Hasil
        $casingBox = Product::create([
            'code'        => 'PROD-101',
            'name'        => 'Casing Box Panel Listrik',
            'unit'        => 'pcs',
            'description' => 'Box besi siap cat',
        ]);

        // 3. Buat Resep BOM (Per 1 pcs Box butuh 2.5 kg Plat Besi & 0.2 liter Cat)
        BillOfMaterial::create([
            'product_id'        => $casingBox->id,
            'material_id'       => $platBesi->id,
            'quantity_required' => 2.5000,
        ]);

        BillOfMaterial::create([
            'product_id'        => $casingBox->id,
            'material_id'       => $catHitam->id,
            'quantity_required' => 0.2000,
        ]);
    }
}