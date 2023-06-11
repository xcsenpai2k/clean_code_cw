<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{    
    /**
     * getPublishedProducts
     *
     * @return Builder
     */
    public function getPublishedProducts(): Builder;
}
