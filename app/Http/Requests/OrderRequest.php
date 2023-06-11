<?php

namespace App\Http\Requests;

use App\Rules\CheckProductQuantity;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
        if (request()->method() == 'PUT') {
            return [
                'status' => 'required|in:' . implode(',', ORDER_STATUS_LIST)
            ];
        }

        $rules = [
            'order_detail' => 'required',
            'order_detail.first_name' => 'required|max:191',
            'order_detail.last_name' => 'required|max:191',
            'order_detail.phone' => 'required|max:191',
            'order_detail.address1' => 'required|max:255',
            'order_detail.address2' => 'required|max:255',
            'order_detail.city' => 'required|max:255',
            'order_detail.zipcode' => 'required|max:25',
            'order_detail.country_code' => 'required|max:3|min:3|exists:countries,code',
            'carts' => 'required|array|min:1',
            'carts.*.product_id' => 'required|exists:products,id',
        ];

        foreach (request('carts', []) as $i => $value) {
            $rules['carts.' . $i . '.quantity'] = [
                'required',
                'min:1',
                new CheckProductQuantity($value['product_id'])
            ];
        }

        return $rules;
    }
}
