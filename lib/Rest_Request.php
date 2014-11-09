<?php
class Rest_Request {
    
    public $method;
    public $endpoint;
    public $body;
    
    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->endpoint = explode('/', trim($_REQUEST['_url'], '/'));
        
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } elseif ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
                throw new Exception('Unexpected Header', 405);
            }
        }
        
        $this->inputs = $this->clean_inputs($_REQUEST);
        $this->body = file_get_contents('php://input');
        
        return true;
    }
    
    private function clean_inputs($data) {
        $clean_input = array();
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $clean_input[$key] = $this->clean_inputs($value);
            }
        } else {
            $clean_input = trim(strip_tags($data));
        }
        return $clean_input;
    }

} ?>