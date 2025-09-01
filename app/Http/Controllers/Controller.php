<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    protected function success($data = null , $message = 'Successful Request' , $statusCode = Response::HTTP_OK)
    {
        return response()->json(
            [
                'status' => 'Success',
                'data' => $data,
                'errors' => null, 
                'message' => $message,
            ] , $statusCode
        );
    }

    protected function error($message = 'Something went wrong.', $errors = null, $statusCode = Response::HTTP_BAD_REQUEST)
    {
        return response()->json(
            [
                'status'  => 'Error',
                'data'    => null,
                'errors'  => $errors,
                'message' => $message,
            ], 
            $statusCode
        );
    }
}
