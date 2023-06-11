<?php

namespace App\Repositories\Eloquent;

use App\Models\Api\Product;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        $this->model = $model;
    }
    
    /**
     * getPublishedProducts
     *
     * @return Builder
     */
    public function getPublishedProducts(): Builder
    {
        return $this->eloquentBuilder()
            ->where('published', '=', ACTIVE);
    }
}
