<?php

namespace App\Repositories;

interface CustomerAddressRepositoryInterface extends BaseRepositoryInterface
{
    public function getCustomerAddress(int $customer_id);
}
