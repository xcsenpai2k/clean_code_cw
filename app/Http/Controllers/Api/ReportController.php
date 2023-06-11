<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\OrderRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(protected OrderRepositoryInterface $orderRepository)
    {
    }

    /**
     * index
     *
     * @OA\Get(
     *      path="/report",
     *      operationId="showListReport",
     *      tags={"Report"},
     *      security={{"bearer_token": {"*"}}},
     *      summary="Show list of reports",
     *      description="Show list of reports",
     *      @OA\Parameter(
     *          name="d",
     *          description="search by date",
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
     * @param  Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $fromDate = getFromDate($request->d, now()->subDays(30));
        $orderList = $this->orderRepository->eloquentBuilder()
            ->whereBetween('created_at', [$fromDate, now()])
            ->get();

        $data = getDataReportByDate($fromDate, $orderList);

        return $this->sendSuccess($data);
    }

    /**
     * OrdersReport
     *
     * @OA\Get(
     *      path="/report/orders",
     *      operationId="showListOrderReport",
     *      tags={"Report"},
     *      security={{"bearer_token": {"*"}}},
     *      summary="Show list of order reports",
     *      description="Show list of order reports",
     *      @OA\Parameter(
     *          name="d",
     *          description="search by date",
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
     * @param  Request $request
     * @return JsonResponse
     */
    public function OrdersReport(Request $request): JsonResponse
    {
        $fromDate = getFromDate($request->d, now()->subDays(30));
        $days = [];
        $labels = [];
        $orders = $this->orderRepository
            ->eloquentBuilder()
            ->whereBetween('created_at', [$fromDate, now()])
            ->get();
        while ($fromDate < now()) {
            $key = $fromDate->toDateString();
            $labels[] = $key;
            $count = $orders->filter(fn ($item) => Carbon::parse($item->created_at)->toDateString() == $key)->count();
            $fromDate = $fromDate->addDay(1);
            $days[] = $count;
        }
        
        return $this->sendSuccess([
            'labels' => $labels,
            'datasets' => [
                'label' => 'Orders By Day',
                'backgroundColor' => '#f87979',
                'data' => $days
            ]
        ]);
    }
}
