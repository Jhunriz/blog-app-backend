<?php

if (! function_exists('API_SUCCESS')) {
    function API_SUCCESS(string $message, $data = null)
    {
        $response = [
            'status' => true,
            'message' => $message,
        ];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, 200);
    }
}

if (! function_exists('API_ERROR')) {
    function API_ERROR(string $message, $errors = null, int $code = 401)
    {
        $response = [
            'status' => false,
            'message' => $message,
        ];

        if (!is_null($errors)) {
            $response['data'] = $errors;
        }

        return response()->json($response, $code);
    }
}
