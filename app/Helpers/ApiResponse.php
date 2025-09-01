<?php

namespace App\Helpers;

use Illuminate\Http\Response;

class ApiResponse
{
    public static function send($data = null, $message = 'Successful request.', $code = Response::HTTP_OK, $status = 'success', $error = null)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
            'error' => $error,
        ], $code);
    }
}
