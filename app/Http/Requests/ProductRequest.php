<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'required|max:1024',
            'description' => 'nullable|max:1024',
            'price' => 'required|integer',
            'image' => 'nullable|image',
            'published' => 'nullable|integer|between:0,1',
        ];
    }
}
