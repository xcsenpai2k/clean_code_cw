<?php

namespace App\Rules;

use App\Models\Api\Product;
use Illuminate\Contracts\Validation\Rule;

class CheckProductQuantity implements Rule
{
    protected $product;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($product_id)
    {
        $this->product = Product::query()->findOrFail($product_id);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($value > $this->product->quantity || $value <= 0) return false;

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->product->title . ' not enough quantity (' . $this->product->quantity . ')';
    }
}
