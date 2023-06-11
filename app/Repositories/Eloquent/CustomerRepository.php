<?php

namespace App\Repositories\Eloquent;

use App\Models\Customer;
use App\Repositories\CustomerRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerRepository extends BaseRepository implements CustomerRepositoryInterface
{
    public function __construct(Customer $model)
    {
        $this->model = $model;
    }

    /**
     * getLatestCustomers
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getLatestCustomers(): \Illuminate\Database\Eloquent\Collection|static
    {
        return $this->eloquentBuilder()
            ->select(['id', 'first_name', 'last_name', 'u.email', 'phone', 'u.created_at'])
            ->join('users AS u', 'u.id', '=', 'customers.user_id')->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * getActiveCustomers
     *
     * @return Builder
     */
    public function getActiveCustomers(): Builder
    {
        return $this->eloquentBuilder()
            ->where('status', 'active');
    }

    /**
     * updateInfo
     *
     * @param  Request $info
     * @param  int $id
     * @return Customer|null|bool
     */
    public function updateInfo(Request $info, int $id): Customer|null|bool
    {
        $customerData = $info->only('first_name', 'last_name', 'phone', 'status', 'shippingAddress', 'billingAddress');
        $customer = $this->findOrFail($id);

        $customerData['updated_by'] = auth()->id();
        $customerData['status'] = $customerData['status'] ? 'active' : 'disabled';

        $res = DB::transaction(function () use ($customer, $customerData) {
            $customer->update($customerData);
            $customer->address()->updateOrCreate([
                'type' => 'shipping'
            ], $customerData['shippingAddress']);

            $customer->address()->updateOrCreate([
                'type' => 'billing'
            ], $customerData['billingAddress']);

            return $customer->load(['shippingAddress', 'billingAddress']);
        });

        return $res;
    }
}
