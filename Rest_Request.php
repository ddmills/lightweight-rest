<?php
class Rest_Request {

  public $method;
  public $endpoint;
  public $inputs;

  public function __construct() {
    $this->method = $_SERVER['REQUEST_METHOD'];

    if (isset($_REQUEST['_url'])) {
      $this->endpoint = explode('/', trim($_REQUEST['_url'], '/'));
      unset($_REQUEST['_url']);
    } else {
      throw new Exception('Invalid URL request', 404);
    }

    if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
      if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
        $this->method = 'DELETE';
      } elseif ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
        $this->method = 'PUT';
      } else {
        throw new Exception('Unexpected Header', 405);
      }
    }

    $this->inputs = new Rest_Inputs();

    return true;
  }
} ?>