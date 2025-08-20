<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreListingRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title' => ['required','string','max:120'],
            'brand' => ['required','string','max:50'],
            'model' => ['required','string','max:80'],
            'year'  => ['nullable','integer','between:2007,2099'],
            'price' => ['required','numeric','min:0'],
            'os'    => ['nullable','in:iOS,Android'],
            'condition' => ['required','in:New,Like New,Good,Fair,Needs Repair'],
            'color' => ['nullable','string','max:40'],
            'city'  => ['required','string','max:80'],
            'description' => ['nullable','string','max:5000'],
            'contact_phone' => ['nullable','string','max:30'],
            'contact_email' => ['nullable','email','max:120'],
            'photos' => ['nullable','array'],
            'photos.*' => ['image','mimes:jpg,jpeg,png,webp','max:4096'],
            'featured' => ['sometimes','boolean'],
        ];
    }
}
