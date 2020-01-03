<?php


namespace App\Func;


class ResponseJson
{
    public static function getSuccess($data = [], $total = 1, $status = 200){
        return self::getResponse(true, $data, $total, $status);
    }

    public static function getError($error = [], $status = 419) {
        return self::getResponse(true, $error, 1, $status);
    }

    private static function getResponse($success, $data = [], $total = 1, $status = 200) {
        $response['success'] = $success;
        if ($success) {
            $response['data'] = $data;
            $response['total'] = $total;
        } else {
            $response['error'] = $data;
        }

        return response()->json($response, $status);
    }
}