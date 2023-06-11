<?php 

namespace App\Repositories\Eloquent;

use App\Models\Payment;
use App\Repositories\PaymentRepositoryInterface;

class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface 
{
    public function __construct(Payment $model)
    {
        $this->model = $model;
    }
}