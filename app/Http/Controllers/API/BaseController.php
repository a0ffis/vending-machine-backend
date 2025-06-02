<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    /**
     * Success response method.
     *
     * @param  mixed  $result
     * @param  string $message
     * @param  int    $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResponse($result, string $message, int $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];
        return response()->json($response, $code);
    }

    /**
     * Error response method.
     *
     * @param  string $error
     * @param  array  $errorMessages
     * @param  int    $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendError(string $error, array $errorMessages = [], int $code = 404): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
            'data'    => !empty($errorMessages) ? $errorMessages : [], // ถ้าไม่มี errorMessages ให้เป็น array ว่าง
        ];

        return response()->json($response, $code);
    }
}
