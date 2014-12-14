<?php
/**
 * A class that defines the CRUD operations on a resource
 */
abstract class Rest_Resource {

  protected $request;

  public function __construct($request) {
    $this->request = $request;
  }

  public function perform_request() {
    $method = 'resource_' . strtolower($this->request->method);
    if (method_exists($this, $method)) {
      return call_user_func_array(array($this, $method), array($this->request));
    } else {
      throw new Exception('Unexpected header request method. Expected POST, GET, PUT, or DELETE.', 405);
    }
  }

  /* CREATE */
  protected function resource_post($request) { throw new Exception('The POST request is not available for this resource', 405); }

  /* READ */
  protected function resource_get($request) { throw new Exception('The GET request is not available for this resource', 405); }

  /* UPDATE */
  protected function resource_put($request) { throw new Exception('The PUT request is not available for this resource', 405); }

  /* DELETE */
  protected function resource_delete($request) { throw new Exception('The DELETE request is not available for this resource', 405); }
} ?>