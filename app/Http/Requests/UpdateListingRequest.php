<?php

namespace App\Http\Requests;

class UpdateListingRequest extends StoreListingRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['photos'] = ['sometimes', 'array'];
        $rules['photos.*'] = ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'];

        $rules['delete_images'] = ['sometimes', 'array'];
        $rules['delete_images.*'] = ['integer'];

        return $rules;
    }
}