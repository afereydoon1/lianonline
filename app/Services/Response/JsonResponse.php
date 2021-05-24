<?php


namespace App\Services\Response;


class JsonResponse
{
    /**
     * @param array $data
     * @param string $message
     * @param int $code
     * @param int $httpStatus
     * @param array $options
     * @return \Illuminate\Http\JsonResponse
     */
    public static function send($data = [], $message = '', $code = 0, $httpStatus = 200, $options = [])
    {
        $httpStatus = 200;
        return response()->json([
            'returnCode' => $code,
            'message' => $message,
            'data' => $data
        ], $httpStatus);
    }
}
