<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompleteBatchRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'actual_qty'                   => 'required|numeric|gte:0',
            'materials_used'               => 'required|array|min:1',
            'materials_used.*.material_id' => 'required|exists:materials,id',
            'materials_used.*.qty_used'    => 'required|numeric|gte:0',
            'scrap'                        => 'nullable|array',
            'scrap.*.material_id'          => 'required_with:scrap.*.scrap_qty|exists:materials,id',
            'scrap.*.scrap_qty'            => 'nullable|numeric|gte:0',
            'scrap.*.scrap_reason'         => 'nullable|string|max:255',
        ];
    }
}