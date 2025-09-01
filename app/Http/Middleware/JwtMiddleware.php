<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use Closure;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JwtMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return ApiResponse::send(null, 'User not found', Response::HTTP_NOT_FOUND, 'error', null);
            }
        } catch (TokenExpiredException $e) {
            return ApiResponse::send(null, 'Token has expired', Response::HTTP_UNAUTHORIZED, 'error');
        } catch (TokenInvalidException $e) {
            return ApiResponse::send(null, 'Token is invalid', Response::HTTP_UNAUTHORIZED, 'error');
        } catch (JWTException $e) {
            return ApiResponse::send(null, 'Token is missing or not provided', Response::HTTP_UNAUTHORIZED, 'error');
        }

        return $next($request);
    }
}
