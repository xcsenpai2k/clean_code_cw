<?php

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

function getFromDate(string|null $input, $value_default = null)
{
    return match ($input) {
        '1d' => now()->subDay(),
        '1k' => now()->subDays(7),
        '2k' => now()->subDays(14),
        '1m' => now()->subDays(30),
        '3m' => now()->subDays(60),
        '6m' => now()->subDays(180),
        default => $value_default
    };
}

function getCustomerInto($customer)
{
    return [
        'id' => $customer->user_id,
        'first_name' => $customer->first_name,
        'last_name' => $customer->last_name,
        'email' => $customer->user->email,
        'phone' => $customer->phone,
        'zipcode' => $customer->address?->zipcode,
        'order_total' => $customer->user->orders->where('status', ORDER_STATUS_COMPLETED)->count(),
        'status' => $customer->status,
        'created_at' => $customer->created_at,
        'updated_at' => $customer->updated_at,
    ];
}

function getDataReportByDate(Carbon $fromDate, Collection $orders)
{
    $data = [];
    while ($fromDate < now()) {
        $key = $fromDate->toDateString();

        $order = $orders->filter(function ($item) use ($key) {
            return Carbon::parse($item->created_at)->toDateString() == $key;
        });

        $grouped = $order->groupBy('created_by');
        $totalUnPaid = $order->where('status', ORDER_STATUS_UNPAID)->count();
        $totalPaid = $order->where('status', ORDER_STATUS_PAID)->count();
        $totalCancelled = $order->where('status', ORDER_STATUS_CANCELLED)->count();
        $sumShipped = $order->where('status', ORDER_STATUS_SHIPPED)->count();
        $totalCompleted = $order->where('status', ORDER_STATUS_COMPLETED)->count();
        $data[$key] = [
            'count_customer' =>  $grouped->count(),
            'count_order' => $order->count(),
            'unpaid' => $totalUnPaid,
            'paid' => $totalPaid,
            'cancelled' => $totalCancelled,
            'shipped' => $sumShipped,
            'completed' => $totalCompleted,
            'rate_completed' => $order ? 0 : round(($totalCompleted / $order->count()) * 100) . '%',
        ];
        $fromDate = $fromDate->addDay(1);
    }
    return $data;
}

function getTopUserOrders($user, $orders)
{
    return [
        'user_id ' => $user->user_id,
        'user_name' => $user->first_name . ' ' . $user->last_name,
        'countOrder' => $orders->where('created_by', $user->user_id)->count(),
    ];
}

function getProductTopSellers($products, $orders)
{
    $data = [];
    foreach ($products as $key => $value) {
        $query = $orders->where('id', $value->order_id)->first();
        if ($query) {
            $product_id = $value->product_id;
            $data[$product_id] = ($data[$product_id] ?? 0) + $value->quantity;
        }
    }
    return $data;
}

function get_per_page($per_page = null)
{
    $per_page = $per_page <= 100 ? $per_page : 100;

    return $per_page;
}
