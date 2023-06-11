<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\OrderRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function __construct(Order $model)
    {
        $this->model = $model;
    }

    /**
     * getOrderByCountry
     *
     * @param  mixed $fromDate
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getOrderByCountry($fromDate = null): \Illuminate\Database\Eloquent\Collection|static
    {
        return $this->eloquentBuilder()
            ->select(['c.name', DB::raw('count(orders.id) as count')])
            ->join('users', 'created_by', '=', 'users.id')
            ->join('customer_addresses AS a', 'users.id', '=', 'a.customer_id')
            ->join('countries AS c', 'a.country_code', '=', 'c.code')
            ->where('status', ORDER_STATUS_PAID)
            ->where('a.type', 'billing')
            ->when($fromDate, fn ($q) => $q->where('orders.created_at', '>', $fromDate))
            ->groupBy('c.name')
            ->get();
    }

    /**
     * getLatestOrders
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getLatestOrders(): \Illuminate\Database\Eloquent\Collection|static
    {
        return $this->eloquentBuilder()
            ->select(['o.id', 'o.total_price', 'o.created_at', DB::raw('COUNT(oi.id) AS items'), 'c.user_id', 'c.first_name', 'c.last_name'])
            ->from('orders AS o')
            ->join('order_items AS oi', 'oi.order_id', '=', 'o.id')
            ->join('customers AS c', 'c.user_id', '=', 'o.created_by')
            ->where('o.status', ORDER_STATUS_PAID)
            ->limit(10)
            ->orderBy('o.created_at', 'desc')
            ->groupBy('o.id', 'o.total_price', 'o.created_at', 'c.user_id', 'c.first_name', 'c.last_name')
            ->get();
    }

    /**
     * getPaidOrders
     *
     * @return Builder
     */
    public function getPaidOrders(): Builder
    {
        return $this->eloquentBuilder()
            ->where('status', ORDER_STATUS_PAID);
    }

    /**
     * getCompletedOrders
     *
     * @return Builder
     */
    public function getCompletedOrders(): Builder
    {
        return $this->eloquentBuilder()
            ->where('status', ORDER_STATUS_COMPLETED);
    }

    /**
     * getDetailOrder
     *
     * @param  int $id
     * @return array
     */
    public function getDetailOrder(int $id): array
    {
        $order = $this->findOrFail($id);

        $order->load(['items' => ['product'], 'user' => ['customer' => ['addresses']]]);

        $orderData = $order->only('id', 'status', 'total_price', 'created_at', 'updated_at');
        $data['list_status'] =  ORDER_STATUS_LIST;
        $data['items'] = $order->items->transform(function ($item) {
            $item->product = $item->product->only('id', 'slug', 'title', 'image');
            return $item->only('id', 'unit_price', 'quantity', 'product');
        });

        $customer = [
            'id' => $order->user->id,
            'email' => $order->user->email,
            'first_name' => $order->user?->customer->first_name,
            'last_name' => $order->user?->customer->last_name,
            'phone' => $order->user?->customer->phone,
        ];

        $customerAddress = $order->user->customer->addresses;

        $customer['shippingAddress'] = $customerAddress?->where('type', '=', 'shipping')->values();
        $customer['billingAddress'] = $customerAddress?->where('type', '=', 'billing')->values();
        $data['customer'] = $customer;
        $data = array_merge($orderData, $data);

        return $data;
    }
}
