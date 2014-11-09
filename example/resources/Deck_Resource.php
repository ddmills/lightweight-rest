<?php
class Deck_Resource extends Rest_Resource {
    
    protected $request;
    
    public function __construct($request) {
        $this->request = $request;
    }
    
    public function resource_post() {
        return 'Created an object';
    }
    
    public function resource_get() {
        return array('asdfasdf' => 'Gettin an object via get');
    }
    
    public function resource_put() {
        return array('WATUDO' => 'UDATED THE THING VIA PUT');
    }
    
    public function resource_delete() {
        return 'deleted this deck';
    }
} ?>