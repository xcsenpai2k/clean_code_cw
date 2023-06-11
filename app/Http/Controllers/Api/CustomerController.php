<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Repositories\CustomerRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(
        protected CustomerRepositoryInterface $customerRepository,
    ) {
    }

    /**
     * index
     *
     * @OA\Get(
     *      path="/customer",
     *      operationId="showListCustomer",
     *      tags={"Customer"},
     *      security={{"bearer_token": {"*"}}},
     *      summary="Get list customers",
     *      description="Get list customers",
     *      @OA\Parameter(
     *          name="sortField",
     *          description="sort by sortDirection",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="sortDirection",
     *          description="sort by sortDirection",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="search",
     *          description="search by username, email",
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
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $data = $this->customerRepository
            ->getActiveCustomers()
            ->with(['user' => ['orders'], 'address'])
            ->sortByColumn($request)
            ->findCustomer($request)
            ->paginate(get_per_page($request->per_page));

        $data->getCollection()->transform(fn ($c) => getCustomerInto($c));

        return $this->sendSuccess($data);
    }

    /**
     * store
     *
     * @OA\Put(
     *      path="/customer/{id}",
     *      operationId="updateCustomer",
     *      tags={"Customer"},
     *      security={{"bearer_token": {"*"}}},
     *      summary="Update Customer",
     *      description="Update Customer",
     *      @OA\Parameter(
     *          name="id",
     *          description="customer id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="first_name",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="last_name",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="phone",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="shippingAddress",
     *                  type="array",
     *                  @OA\Items(
     *                     @OA\Property(
     *                         property="address1",
     *                         type="string",
     *                     ),
     *                     @OA\Property(
     *                         property="address2",
     *                         type="string",
     *                     ),
     *                      @OA\Property(
     *                         property="city",
     *                         type="string",
     *                     ),
     *                      @OA\Property(
     *                         property="state",
     *                         type="string",
     *                     ),
     *                      @OA\Property(
     *                         property="zipcode",
     *                         type="string",
     *                     ),
     *                      @OA\Property(
     *                         property="country_code",
     *                         type="string",
     *                     ),
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="billingAddress",
     *                  type="array",
     *                  @OA\Items(
     *                     @OA\Property(
     *                         property="address1",
     *                         type="string",
     *                     ),
     *                     @OA\Property(
     *                         property="address2",
     *                         type="string",
     *                     ),
     *                      @OA\Property(
     *                         property="city",
     *                         type="string",
     *                     ),
     *                      @OA\Property(
     *                         property="state",
     *                         type="string",
     *                     ),
     *                      @OA\Property(
     *                         property="zipcode",
     *                         type="string",
     *                     ),
     *                      @OA\Property(
     *                         property="country_code",
     *                         type="string",
     *                     ),
     *                  )
     *              ),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *     )
     * 
     * @param  CustomerRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(CustomerRequest $request,int $id): JsonResponse
    {
        $customer = $this->customerRepository->updateInfo($request, $id);

        return $this->updateSuccess($customer);
    }

    /**
     * destroy
     *
     * @OA\Delete(
     *      path="/customer/{id}",
     *      operationId="deleteCustomer",
     *      tags={"Customer"},
     *      security={{"bearer_token": {"*"}}},
     *      summary="Delete Customer",
     *      description="Delete Customer",
     *      @OA\Parameter(
     *          name="id",
     *          description="Customer id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation",
     *       ),
     *     )
     * 
     * @param  int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $this->customerRepository->findOrFail($id)->delete();
        return $this->deleteSuccess();
    }
}
