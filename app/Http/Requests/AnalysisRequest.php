<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnalysisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device_id' => ['required', 'exists:devices,id'],
            'tanggal' => ['required', 'date'],
            'jam_pemakaian' => ['required', 'numeric', 'min:0.1'],
        ];
    }
}
