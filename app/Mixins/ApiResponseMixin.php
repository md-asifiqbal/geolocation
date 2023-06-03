<?php

namespace App\Mixins;

class ApiResponseMixin
{
    public function apiSuccess()
    {
        return function ($message = null, $data = [], $statusCode = 200) {
            return response()->json([
                'status' => true,
                'message' => __($message),
                'data' => $data,
            ], $statusCode);
        };
    }

    public function apiError()
    {
        return function ($message = null, $statusCode = 500, $data = []) {
            return response()->json([
                'status' => false,
                'message' => __($message),
                'data' => $data,
            ], isset($statusCode) && $statusCode != 0 ? $statusCode : 500);
        };
    }
}
