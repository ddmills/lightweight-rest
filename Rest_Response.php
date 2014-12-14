<?php
class Rest_Response {
  public function __construct($api, $data, $code = 200) {
    if (!$api->headers_sent()) {
      $codes = array(
        200 => array('success' => true, 'message' => 'OK'),
        201 => array('success' => true, 'message' => 'Created'),
        204 => array('success' => true, 'message' => 'No Content'),
        400 => array('success' => false, 'message' => 'Bad Request'),
        301 => array('success' => false, 'message' => 'Moved Permanently'),
        401 => array('success' => false, 'message' => 'Unauthorized'),
        403 => array('success' => false, 'message' => 'Forbidden'),
        404 => array('success' => false, 'message' => 'Not Found'),
        405 => array('success' => false, 'message' => 'Method Not Allowed'),
        409 => array('success' => false, 'message' => 'Conflict'),
        431 => array('success' => false, 'message' => 'Request Header Fields Too Large'),
        500 => array('success' => false, 'message' => 'Internal Server Error'),
        501 => array('success' => false, 'message' => 'Not Implemented'),
        503 => array('success' => false, 'message' => 'Service Unavailable')
      );

      $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
      $code     = array_key_exists($code, $codes) ? $code : 500;
      $message  = $codes[$code]['message'];

      if ($api->cors_enabled()) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: *');
      }

      header('Content-Type: application/json; charset=utf-8');
      header($protocol . ' ' . $code . ' ' . $message);

      $resp = '';

      if ($api->format_output()) {
        $resp = array(
          'status' => $codes[$code]['success'] ? 'success' : 'failure',
          'code' => $code,
          'data' => $data
        );
      } else {
        $resp = $data;
      }

      echo json_encode($resp);
      $api->set_headers_sent();
    }
  }
} ?>