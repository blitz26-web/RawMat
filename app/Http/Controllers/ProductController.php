<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Mengembalikan data BOM (resep) suatu produk dalam bentuk JSON untuk AJAX
     */
    public function getBom(Product $product)
    {
        $product->load('bomItems.material');

        $bom = $product->bomItems->map(function ($item) {
            return [
                'material_id'       => $item->material_id,
                'material_name'     => $item->material->name,
                'material_code'     => $item->material->code,
                'current_stock'     => (float) $item->material->current_stock,
                'unit'              => $item->material->unit,
                'quantity_required' => (float) $item->quantity_required,
            ];
        });

        return response()->json([
            'product' => $product,
            'bom'     => $bom,
        ]);
    }
}