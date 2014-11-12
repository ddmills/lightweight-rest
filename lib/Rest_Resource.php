<?php
/**
 * An class that defines the CRUD operations on a resource
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
    protected function resource_post($request) { throw new Exception('Method not allowed', 405); }
    
    /* READ */
    protected function resource_get($request) { throw new Exception('Method not allowed', 405); }
    
    /* UPDATE */
    protected function resource_put($request) { throw new Exception('Method not allowed', 405); }
    
    /* DELETE */
    protected function resource_delete($request) { throw new Exception('Method not allowed', 405); }
    
    /* Utility function for checking for inputs */
    protected function require_input($input) {
        if (isset($this->request->inputs[$input])) {
            return $this->request->inputs[$input];
        } else {
            throw new Exception('Variable "' . $input . '" was not provided', 400);
        }
    }
    
} ?>