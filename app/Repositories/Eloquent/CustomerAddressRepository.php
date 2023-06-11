<?php

namespace App\Repositories\Eloquent;

use App\Models\CustomerAddress;
use App\Repositories\CustomerAddressRepositoryInterface;

class CustomerAddressRepository extends BaseRepository implements CustomerAddressRepositoryInterface
{
    public function __construct(CustomerAddress $model)
    {
        $this->model = $model;
    }

    /**
     * getCustomerAddress
     *
     * @param  int $customer_id
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getCustomerAddress(int $customer_id): \Illuminate\Database\Eloquent\Collection|static
    {
        return $this->eloquentBuilder()
            ->where('customer_id', $customer_id)
            ->get();
    }
}
