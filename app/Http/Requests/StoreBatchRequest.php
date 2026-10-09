<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBatchRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'batch_number'           => 'required|string|max:50|unique:production_batches,batch_number',
            'product_name'           => 'required|string|max:255',
            'target_qty'             => 'required|numeric|gt:0',
            'start_date'             => 'nullable|date',
            'materials'              => 'required|array|min:1',
            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.qty_planned' => 'required|numeric|gt:0',
        ];
    }
}