<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Repositories\ProductRepositoryInterface;
use App\Services\UploadService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(protected ProductRepositoryInterface $productRepository)
    {
    }

    /**
     * store
     *
     * @OA\Post(
     *      path="/product",
     *      operationId="storeProduct",
     *      tags={"Product"},
     *      security={{"bearer_token": {"*"}}},
     *      summary="Create Product",
     *      description="Create Product",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="title",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="quantity",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="price",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="image",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="description",
     *                  type="string",
     *              ),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *     )
     * 
     * @param  ProductRequest $request
     * @return JsonResponse
     */
    public function store(ProductRequest $request): JsonResponse
    {
        $data = $request->only(['title', 'description', 'price', 'published']);

        $image = $data['image'] ?? null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $dataImg = UploadService::uploadLocal($image);
            $data = array_merge($data, $dataImg);
        }

        $product = $this->productRepository->create($data);

        $res = $product->only(PRODUCT_COL_SELECT);

        return $this->createSuccess($res);
    }

    /**
     * update
     * 
     *  @OA\Put(
     *      path="/product/{id}",
     *      operationId="updateProduct",
     *      tags={"Product"},
     *      security={{"bearer_token": {"*"}}},
     *      summary="Update Product",
     *      description="Update Product",
     *      @OA\Parameter(
     *          name="id",
     *          description="Product id",
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
     *                  property="title",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="quantity",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="price",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="image",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="description",
     *                  type="string",
     *              ),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *     )
     * 
     *
     * @param  ProductRequest $request
     * @param  int $id
     * @return JsonResponse
     */
    public function update(ProductRequest $request, int $id): JsonResponse
    {
        $data = $request->only('title', 'description', 'price', 'published');

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $dataImg = UploadService::uploadLocal($image);
            $data = array_merge($data, $dataImg);
        }

        $product = $this->productRepository->findOrFail($id);
        $product->update($data);
        $results = $product->only(PRODUCT_COL_SELECT);

        return $this->updateSuccess($results);
    }

    /**
     * destroy
     *
     * @OA\Delete(
     *      path="/product/{id}",
     *      operationId="deleteProduct",
     *      tags={"Product"},
     *      security={{"bearer_token": {"*"}}},
     *      summary="Delete Product",
     *      description="Delete Product",
     *      @OA\Parameter(
     *          name="id",
     *          description="Product id",
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
        $this->productRepository->delete($id);

        return $this->deleteSuccess();
    }
}
