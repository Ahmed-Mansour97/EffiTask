<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;


class AuthController extends Controller
{
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

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
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
