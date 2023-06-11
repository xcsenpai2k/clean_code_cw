<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
            'first_name' => 'required|max:191',
            'last_name' => 'required|max:191',
            'phone' => 'required|max:191',
            'shippingAddress' => 'required',
            'billingAddress' => 'required',
            'shippingAddress.address1' => 'required|max:255',
            'shippingAddress.address2' => 'required|max:255',
            'shippingAddress.city' => 'required|max:255',
            'shippingAddress.state' => 'nullable|string|max:45',
            'shippingAddress.zipcode' => 'required|max:25',
            'shippingAddress.country_code' => 'required|max:3|min:3|exists:countries,code',
            'billingAddress.address1' => 'required|max:255',
            'billingAddress.address2' => 'required|max:255',
            'billingAddress.city' => 'required|max:255',
            'billingAddress.state' => 'nullable|string|max:45',
            'billingAddress.zipcode' => 'required|max:25',
            'billingAddress.country_code' => 'required|max:3|min:3|exists:countries,code',
        ];
    }
}
