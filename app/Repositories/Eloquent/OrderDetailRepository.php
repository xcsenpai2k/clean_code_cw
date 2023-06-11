<?php 

namespace App\Repositories\Eloquent;

use App\Models\OrderDetail;
use App\Repositories\OrderDetailRepositoryInterface;

class OrderDetailRepository extends BaseRepository implements OrderDetailRepositoryInterface 
{
    public function __construct(OrderDetail $model)
    {
        $this->model = $model;
    }
}