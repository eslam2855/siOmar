<?php

namespace App\Traits;

use Illuminate\Support\Facades\Validator;

trait ApiResponseTrait
{
    /**
     * Handle validation errors and return first error message
     */
    protected function handleValidationErrors($validator)
    {
        $errors = $validator->errors();
        $firstError = collect($errors)->first();
        $firstErrorMessage = is_array($firstError) ? $firstError[0] : $firstError;
        
        return response()->json([
            'success' => false,
            'message' => $firstErrorMessage,
        ], 422);
    }

    /**
     * Return success response
     */
    protected function successResponse($data = null, $message = 'Success', $statusCode = 200)
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return error response
     */
    protected function errorResponse($message = 'Error', $statusCode = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $statusCode);
    }
}
