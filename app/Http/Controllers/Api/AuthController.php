<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(protected UserRepositoryInterface $userRepository)
    {
    }

    /**
     * login
     * 
     * @OA\Post(
     *  path="/login",
     *   tags={"Auth"},
     *   summary="Login",
     *   operationId="login",
     *   @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="email",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="password",
     *                  type="string",
     *              )
     *          )
     *   ),
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *   )
     *)
     *
     * @param  LoginRequest $request
     * @return JsonResponse
     */

    public function login(LoginRequest $request): JsonResponse
    {
        // $user = DB::select("select * from users where email = '$request->email' limit 1"); -> sql injection
        // solution: DB::select("select * from users where email = ? limit 1", [$request->email])
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return $this->sendError(null, 401, 'The provided credentitals are incorrect.');
        }

        $user = $this->userRepository->getByEmail($request->email);

        $token = $user->createToken('main')->plainTextToken;

        return $this->sendSuccess([
            'user' => $user->only('id', 'name', 'email', 'created_at'),
            'token' => $token
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     *  @OA\Get(
     *  path="/logout",
     *   tags={"Auth"},
     *   summary="Logout",
     *   operationId="logout",
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   )
     *)
     * 
     * @return JsonResponse
     */

    public function logout(): JsonResponse
    {
        $user = Auth::user();
        $user->currentAccessToken()->delete();
        return $this->deleteSuccess('');
    }

    /**
     * Get the authenticated User.
     *
     *  @OA\Get(
     *  path="/user",
     *   tags={"Auth"},
     *   summary="show user",
     *   operationId="showMe",
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   )
     *)
     * 
     * @return JsonResponse
     */

    public function getUser(Request $request): JsonResponse
    {
        $user = $request->user();
        $user = $user->only('id', 'name', 'email', 'created_at');
        return $this->sendSuccess($user);
    }
}
