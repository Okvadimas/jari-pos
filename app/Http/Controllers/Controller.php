<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Return success JSON response
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($message, $result = [])
    {
        $response = [
            'status' => true,
            'message' => $message,
            'data'    => $result,
        ];

        return response()->json($response, 200);
    }

    /**
     * Return error JSON response
     *
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse($error, $code = 400)
    {
        $response = [
            'status'    => false,
            'data'      => [],
            'message'   => $error,
        ];

        return response()->json($response, $code);
    }

}

