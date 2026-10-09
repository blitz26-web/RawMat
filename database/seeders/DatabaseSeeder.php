<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Data Bahan Baku
        $matTepung = Material::create([
            'name' => 'Tepung Terigu Cakra Kembar',
            'current_stock' => 1500.00,
            'min_stock' => 200.00,
            'unit' => 'kg',
            'unit_price' => 13500,
        ]);

        $matGula = Material::create([
            'name' => 'Gula Pasir Industri',
            'current_stock' => 40.00, // Stok Kritis
            'min_stock' => 100.00,
            'unit' => 'kg',
            'unit_price' => 16500,
        ]);

        $matMinyak = Material::create([
            'name' => 'Minyak Goreng Sawit',
            'current_stock' => 500.00,
            'min_stock' => 80.00,
            'unit' => 'liter',
            'unit_price' => 18000,
        ]);

        $matMentega = Material::create([
            'name' => 'Mentega Margarin Super',
            'current_stock' => 18.00, // Stok Kritis
            'min_stock' => 40.00,
            'unit' => 'kg',
            'unit_price' => 48000,
        ]);

        $matCokelat = Material::create([
            'name' => 'Cokelat Bubuk Premium',
            'current_stock' => 250.00,
            'min_stock' => 50.00,
            'unit' => 'kg',
            'unit_price' => 72000,
        ]);

        $matTelur = Material::create([
            'name' => 'Telur Ayam Segar',
            'current_stock' => 300.00,
            'min_stock' => 60.00,
            'unit' => 'kg',
            'unit_price' => 28000,
        ]);

        $matRagi = Material::create([
            'name' => 'Ragi Instan Dry Yeast',
            'current_stock' => 10.00, // Stok Kritis
            'min_stock' => 20.00,
            'unit' => 'kg',
            'unit_price' => 55000,
        ]);

        // 2. Data Produk Master
        $prodRoti = Product::create([
            'code' => 'PRD-001',
            'name' => 'Roti Tawar Gandum Premium',
            'description' => 'Roti tawar gandum segar kemasan 400g',
        ]);

        $prodBiskuit = Product::create([
            'code' => 'PRD-002',
            'name' => 'Biskuit Cokelat Renyah',
            'description' => 'Biskuit cokelat panggang renyah isi 200g',
        ]);

        $prodBolu = Product::create([
            'code' => 'PRD-003',
            'name' => 'Bolu Gulung Cokelat',
            'description' => 'Bolu lembut gulung isi selai cokelat',
        ]);

        $prodDonat = Product::create([
            'code' => 'PRD-004',
            'name' => 'Donat Kentang Meses',
            'description' => 'Donat kentang topping meses cokelat',
        ]);

        // 3. Relasi Resep BOM Produk (opsional jika ada relasi pivot materials)
        if (method_exists($prodRoti, 'materials')) {
            $prodRoti->materials()->syncWithoutDetaching([
                $matTepung->id => ['quantity' => 0.5],
                $matGula->id => ['quantity' => 0.1],
                $matRagi->id => ['quantity' => 0.01],
            ]);

            $prodBiskuit->materials()->syncWithoutDetaching([
                $matTepung->id => ['quantity' => 0.3],
                $matGula->id => ['quantity' => 0.15],
                $matCokelat->id => ['quantity' => 0.1],
                $matMentega->id => ['quantity' => 0.08],
            ]);

            $prodBolu->materials()->syncWithoutDetaching([
                $matTepung->id => ['quantity' => 0.4],
                $matGula->id => ['quantity' => 0.2],
                $matTelur->id => ['quantity' => 0.3],
                $matCokelat->id => ['quantity' => 0.15],
            ]);

            $prodDonat->materials()->syncWithoutDetaching([
                $matTepung->id => ['quantity' => 0.35],
                $matMinyak->id => ['quantity' => 0.1],
                $matMentega->id => ['quantity' => 0.05],
            ]);
        }
    }
}