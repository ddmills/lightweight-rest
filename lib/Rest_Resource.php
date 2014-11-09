<?php
/**
 * An class that defines the CRUD operations on a resource
 */
abstract class Rest_Resource {
    
    protected $request;
    
    public function perform_request() {
        $method = 'resource_' . strtolower($this->request->method);
        if (method_exists($this, $method)) {
            return call_user_func(array($this, $method));
        } else {
            throw new Exception('Unexpected header request method. Expected POST, GET, PUT, or DELETE.', 405);
        }
    }
    
    /* CREATE */
    abstract protected function resource_post();
    
    /* READ */
    abstract protected function resource_get();
    
    /* UPDATE */
    abstract protected function resource_put();
    
    /* DELETE */
    abstract protected function resource_delete();

} ?>