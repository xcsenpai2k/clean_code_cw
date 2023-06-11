<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\CustomerRepositoryInterface;
use App\Repositories\OrderItemRepositoryInterface;
use App\Repositories\OrderRepositoryInterface;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        protected CustomerRepositoryInterface $customerRepository,
        protected ProductRepositoryInterface $productRepository,
        protected OrderRepositoryInterface $orderRepository,
        protected OrderItemRepositoryInterface $orderItemRepository,
    ) {
    }

    /**
     * index
     *
     * @OA\Get(
     *      path="/dashboard/count",
     *      operationId="showDashboardCount",
     *      tags={"Dashboard"},
     *      security={{"bearer_token": {"*"}}},
     *      summary="Get list count in dashboard",
     *      description="Get list count in dashboard",
     *      @OA\Parameter(
     *          name="d",
     *          description="search by day",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *     )
     * @param  Request $request
     * @return JsonResponse
     */
    public function count(Request $request)
    {
        $fromDate = getFromDate($request->d);

        $activeCustomers = $this->customerRepository
            ->getActiveCustomers()
            ->count();

        $activeProducts = $this->productRepository
            ->getPublishedProducts()
            ->count();

        $query = $this->orderRepository->getPaidOrders()
            ->filterDate($fromDate);

        $paidOrders = $query->count();

        $totalIncome = round($query->sum('total_price'));

        $ordersByCountry = $this->orderRepository->getOrderByCountry($fromDate);

        $latestCustomers = $this->customerRepository->getLatestCustomers();

        $latestOrders = $this->orderRepository->getLatestOrders();

        $data = [
            'active_customers' => $activeCustomers,
            'active_products' => $activeProducts,
            'paid_orders' => $paidOrders,
            'total_income' => $totalIncome,
            'orders_by_country' => $ordersByCountry,
            'latest_customers' => $latestCustomers,
            'latest_orders' => $latestOrders
        ];

        return $this->sendSuccess($data);
    }

    /**
     * topUserOrders
     *
     * @OA\Get(
     *      path="/dashboard/top-user-orders",
     *      operationId="showDashboardUserOrder",
     *      tags={"Dashboard"},
     *      security={{"bearer_token": {"*"}}},
     *      summary="top user orders",
     *      description="Get top user orders",
     *      @OA\Parameter(
     *          name="d",
     *          description="search by day",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *     )
     * @param  Request $request
     * @return JsonResponse
     */
    public function topUserOrders(Request $request): JsonResponse
    {
        $topUserOrder = [];
        $fromDate = getFromDate($request->d, null);
        $getAllUsers = $this->customerRepository
            ->getActiveCustomers()
            ->get();
        $orders = $this->orderRepository
            ->getCompletedOrders()
            ->filterDate($fromDate)
            ->get();
        foreach ($getAllUsers as $value) {
            $topUserOrder[] = getTopUserOrders($value, $orders);
        }
        $topUserOrder = collect($topUserOrder)->sortByDesc('countOrder')->slice(0, 5)->toArray();
        return $this->sendSuccess($topUserOrder);
    }

    /**
     * topSellers
     *
     * @OA\Get(
     *      path="/dashboard/top-sellers",
     *      operationId="showDashboardSellers",
     *      tags={"Dashboard"},
     *      security={{"bearer_token": {"*"}}},
     *      summary="get top sellers",
     *      description="get top sellers",
     *      @OA\Parameter(
     *          name="d",
     *          description="search by day",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *     )
     * @param  Request $request
     * @return JsonResponse
     */
    public function topSellers(Request $request): JsonResponse
    {
        $fromDate = getFromDate($request->d);
        $topSellers = [];
        $getAllOrderProduct = $this->orderItemRepository->all();
        $productsTopSellersIds = [];
        $orders = $this->orderRepository
            ->getCompletedOrders()
            ->filterDate($fromDate)
            ->get();
        $productsTopSellersIds = getProductTopSellers($getAllOrderProduct, $orders);
        arsort($productsTopSellersIds);
        $topSellers = $this->productRepository
            ->getPublishedProducts()
            ->whereIn('id', array_keys($productsTopSellersIds))
            ->get()
            ->transform(function ($product) use ($productsTopSellersIds) {
                $data = [
                    'product_id' => $product->id,
                    'product_name' => $product->title,
                    'sellers' => $productsTopSellersIds[$product->id],
                ];
                return $data;
            })->sortByDesc('sellers')
            ->splice(0, 5)
            ->values();

        return $this->sendSuccess($topSellers);
    }
}
