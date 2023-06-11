<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;

interface OrderRepositoryInterface extends BaseRepositoryInterface
{    
    /**
     * getOrderByCountry
     *
     * @param  mixed $fromDate
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getOrderByCountry($fromDate = null): \Illuminate\Database\Eloquent\Collection|static;
    
    /**
     * getLatestOrders
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getLatestOrders(): \Illuminate\Database\Eloquent\Collection|static;
        
    /**
     * getPaidOrders
     *
     * @return Builder
     */
    public function getPaidOrders(): Builder;
        
    /**
     * getCompletedOrders
     *
     * @return Builder
     */
    public function getCompletedOrders(): Builder;
    
    /**
     * getDetailOrder
     *
     * @param  int $id
     * @return array
     */
    public function getDetailOrder(int $id): array;
}
