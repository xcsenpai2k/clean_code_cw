<?php

namespace App\Repositories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

interface CustomerRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * getLatestCustomers
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getLatestCustomers(): \Illuminate\Database\Eloquent\Collection|static;

    /**
     * getActiveCustomers
     *
     * @return Builder
     */
    public function getActiveCustomers(): Builder;

    /**
     * updateInfo
     *
     * @param  Request $info
     * @param  int $id
     * @return Customer
     */
    public function updateInfo(Request $info, int $id): Customer|null|bool;
}
