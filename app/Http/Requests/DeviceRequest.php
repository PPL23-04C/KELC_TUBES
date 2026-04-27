<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_device' => ['required', 'string', 'max:100'],
            'daya_watt' => ['required', 'integer', 'min:1'],
            'jumlah_unit' => ['required', 'integer', 'min:1'],
        ];
    }
}
