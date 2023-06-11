<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Jobs\SendMailOrder;
use App\Repositories\CustomerAddressRepositoryInterface;
use App\Repositories\OrderDetailRepositoryInterface;
use App\Repositories\OrderItemRepositoryInterface;
use App\Repositories\OrderRepositoryInterface;
use App\Repositories\ProductRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(
        protected OrderRepositoryInterface $orderRepository,
        protected OrderItemRepositoryInterface $orderItemRepository,
        protected OrderDetailRepositoryInterface $orderDetailRepository,
        protected UserRepositoryInterface $userRepository,
        protected CustomerAddressRepositoryInterface $customerAddressRepo,
        protected ProductRepositoryInterface $productRepository,
    ) {
    }

    /**
     * show
     *
     * @OA\Get(
     *      path="/order/{id}",
     *      operationId="showOrder",
     *      tags={"Order"},
     *      security={{"bearer_token": {"*"}}},
     *      summary="Show details of order",
     *      description="Show details of order",
     *      @OA\Parameter(
     *          name="id",
     *          description="order id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *     )
     * 
     * @param  int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $res = $this->orderRepository->getDetailOrder($id);

        return $this->sendSuccess($res);
    }

    /**
     * changeStatusOrder
     *
     * @OA\Put(
     *      path="/orders/change-status/{id}",
     *      operationId="changeStatusOrder",
     *      tags={"Order"},
     *      security={{"bearer_token": {"*"}}},
     *      summary="Change status order",
     *      description="Change status order",
     *      @OA\Parameter(
     *          name="id",
     *          description="order id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="status",
     *          description="status order",
     *          required=true,
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
     * 
     * @param  int $id
     * @return JsonResponse
     */
    public function changeStatusOrder(OrderRequest $request, int $id): JsonResponse
    {
        $order = $this->orderRepository->findOrFail($id);

        $order->update(['status' => $request->status]);

        dispatch(new SendMailOrder($order));

        return $this->updateSuccess($order);
    }

    /**
     * store
     *
     * @OA\Post(
     *      path="/order",
     *      operationId="storeOrder",
     *      tags={"Order"},
     *      security={{"bearer_token": {"*"}}},
     *      summary="Create Order",
     *      description="Create Order",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="carts",
     *                  type="array",
     *                  @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="product_id",
     *                         type="integer",
     *                     ),
     *                     @OA\Property(
     *                         property="quantity",
     *                         type="integer",
     *                     ),
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="order_detail",
     *                  type="object",
     *                  @OA\Property(
     *                     property="first_name",
     *                     type="string",
     *                  ),
     *                  @OA\Property(
     *                     property="last_name",
     *                     type="string",
     *                  ),
     *                  @OA\Property(
     *                     property="phone",
     *                     type="string",
     *                  ),
     *                  @OA\Property(
     *                     property="address1",
     *                     type="string",
     *                  ),
     *                  @OA\Property(
     *                     property="address2",
     *                     type="string",
     *                  ),
     *                  @OA\Property(
     *                     property="city",
     *                     type="string",
     *                  ),
     *                  @OA\Property(
     *                     property="zipcode",
     *                     type="string",
     *                  ),
     *                  @OA\Property(
     *                     property="country_code",
     *                     type="string",
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Successful operation",
     *          @OA\JsonContent()
     *      )
     * )
     * 
     * @param  OrderRequest $request
     * @return JsonResponse
     */
    public function store(OrderRequest $request): JsonResponse
    {
        $totalPrice = 0;
        $orderDetail = $request->order_detail;
        $cartItems = collect($request->carts);
        $productIds = $cartItems->pluck('product_id');
        $products = $this->productRepository->eloquentBuilder()->whereIn('id', $productIds)->get();

        $productItems = $products->map(function ($product) use ($cartItems, &$totalPrice) {
            $data = [
                'product_id' => $product->id,
                'quantity' => $cartItems->firstWhere('product_id', $product->id)['quantity'],
                'unit_price' => $product?->price
            ];
            $totalPrice += $data['quantity'] * $data['unit_price'];
            return $data;
        })->keyBy('product_id');

        $res = DB::transaction(function () use ($totalPrice, $orderDetail, $productItems, $cartItems, $products) {
            $order = $this->orderRepository->create(['total_price' => $totalPrice]);
            $order->detail()->create($orderDetail);
            $order->products()->attach($productItems);
            $products->each(fn ($p) => $p->decrement('quantity', $cartItems->firstWhere('product_id', $p->id)['quantity']));
            return $order;
        });

        return $this->createSuccess($res);
    }

    /**
     * index
     *
     * @OA\Get(
     *      path="/order",
     *      operationId="showListOrders",
     *      tags={"Order"},
     *      security={{"bearer_token": {"*"}}},
     *      summary="Show list of orders",
     *      description="Show list of orders",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *     )
     * 
     * @param  mixed $request
     * @return void
     */
    public function index(Request $request): JsonResponse
    {
        $orders = $this->orderRepository
            ->eloquentBuilder()
            ->findByStatus($request)
            ->findByUserName($request)
            ->paginate(get_per_page($request->per_page));

        return $this->sendSuccess($orders);
    }
}
