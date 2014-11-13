<?php
class Rest_Response {
    public function __construct($api, $data, $code = 200) {
        $codes = array(
            200 => 'OK',
            201 => 'Created',
            204 => 'No Content',
            400 => 'Bad Request',
            301 => 'Moved Permanently',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            409 => 'Conflict',
            431 => 'Request Header Fields Too Large',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            503 => 'Service Unavailable'
        );
        
        $code = array_key_exists($code, $codes) ? $code : 500;
        
        if ($api->cors_enabled()) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
        }
        header('Content-Type: application/json; charset=utf-8');
        header('HTTP/1.1 ' . $code . ' ' . $codes[$code]);
        
        $resp = array(
            'status' => $code == 200 ? 'success' : 'failure',
            'code' => $code,
            'data' => $data
        );
        
        echo json_encode($resp);
    }
} ?>