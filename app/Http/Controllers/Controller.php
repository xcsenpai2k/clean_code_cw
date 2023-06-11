<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Laravel OpenApi",
 *      description="L5 Swagger OpenApi description",
 *      @OA\Contact(
 *          email="admin@admin.com"
 *      ),
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )
 * 
 * @OAS\SecurityScheme(
 *      securityScheme="bearer_token",
 *      type="http",
 *      scheme="bearer"
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Test API"
 * )
 * @OA\PathItem(path="/api")
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * send api response Success
     *
     * @param  mixed $data
     * @param  int $code
     * @param  string $code
     * @return JsonResponse
     */
    public function sendSuccess($data, $code = 200, $message = ''): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'success' => true,
            'message' => $message,
        ], $code);
    }

    /**
     * send api response Error
     *
     * @param  mixed $data
     * @param  int $code
     * @param  string $message
     * @return JsonResponse
     */
    public function sendError($data = null, $code = 400, $message = ''): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'success' => false,
            'message' => $message,
        ], $code);
    }

    /**
     * send api response update success
     *
     * @param  mixed $data
     * @param  string $message
     * @return JsonResponse
     */
    public function updateSuccess($data, $message = 'Update Successfully'): JsonResponse
    {
        return $this->sendSuccess($data, 200, $message);
    }

    /**
     * send api response create success
     *
     * @param  mixed $data
     * @param  string $message
     * @return JsonResponse
     */
    public function createSuccess($data, $message = 'Create Successfully'): JsonResponse
    {
        return $this->sendSuccess($data, 201, $message);
    }

    /**
     * send api response delete success
     *
     * @param  string $message
     * @return JsonResponse
     */
    public function deleteSuccess($message = 'Delete Successfully'): JsonResponse
    {
        return $this->sendSuccess(null, 204, $message);
    }
}
