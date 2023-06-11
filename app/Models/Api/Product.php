<?php

namespace App\Models\Api;

class Product extends \App\Models\Product
{
    public function getRouteKeyName()
    {
        return 'id';
    }

    protected static function booted()
    {
        static::creating(function ($product) {
            $product->created_by = auth()->id();
            $product->updated_by = auth()->id();
        });

        static::updating(function ($product) {
            $product->updated_by = auth()->id();
        });
    }

    public function getImageUrlAttribute($value)
    {
        return $value ?? null;
    }

    public function getPublishedAttribute($value)
    {
        return boolval($value);
    }
}
