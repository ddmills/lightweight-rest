<?php
class Rest_Response {
       
    public function __construct($api, $data, $code = 200) {
        $codes = array(
            200 => 'OK',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error'
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