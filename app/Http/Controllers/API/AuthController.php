<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;


class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     description="Registers a new user with the provided credentials",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RegisterUserRequestSchema")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", ref="#/components/schemas/UserResource")
     *             ),
     *             @OA\Property(property="errors", type="object", nullable=true),
     *         ),
     *     )
     * )
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create($data);

        $token = JWTAuth::fromUser($user);

        return $this->success([
            'user'  => new UserResource($user),
            'token' => $token,
        ], 'User Registered Successfully' , Response::HTTP_CREATED);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="User Login",
     *     description="Authenticates a user and returns a JWT token if credentials are valid.",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful.",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", description="JWT token", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1Q...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid credentials")
     *         )
     *     ),
     * )
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (! $token = JWTAuth::attempt($credentials)) {
            return $this->error('Invalid Credentials', 'Invalid Credentials' , Response::HTTP_UNAUTHORIZED);
        }

        return $this->success([
            'user'  => new UserResource(auth()->user()),
            'token' => $token,
        ]);
    }

    public function logout()
    {
        auth()->logout();

        return $this->success(null , 'Logged out successfully');
    }

    public function profile()
    {
        return $this->success(new UserResource(auth()->user()));
    }
}
