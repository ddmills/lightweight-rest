<?php
class Rest_Inputs {
    
    /* Holds URI params, for example a request to
     * "/path/to/api/someresource/4123/image/5" will hold the variables 4123 and 5, with keys defined
     * by the api mapping */
    public $uri;
    
    /* Holds POSTed parameters. These are retrieved from php://input */
    public $body;
    
    /* Holds query paramters, for example a request to: 
     * "/path/to/api/someresource/accounts/?name=Peter" will hold "name" => "Peter" */
    public $query;
    
    public function __construct() {
        $this->uri = array();
        $this->body = @json_decode(file_get_contents('php://input'));
        $this->query = $this->sanitize_inputs($_REQUEST); /* TODO - should this be set to $_GET instead? */
    }
    
    /*
     * Check all inputs for given key. 
     * Precedence as follows: URI > body > query.
     * @param String $key key to search for
     * @return mixed null, or value of key requested
     */
    public function get($key) {
        if (isset($this->uri[$key])) { return $this->uri[$key]; }
        if (isset($this->body[$key])) { return $this->body[$key]; }
        if (isset($this->query[$key])) { return $this->query[$key]; }
        return null;
    }
    
    /*
     * Check uri array for given key.
     * @param String $key key to search for
     * @return mixed null, or value of key requested
     */
    public function uri($key) {
        return isset($this->uri[$key]) ? $this->uri[$key] : null;
    }
    
    /*
     * Check body array for given key.
     * @param String $key key to search for
     * @return mixed null, or value of key requested
     */
    public function body($key) {
        return isset($this->body[$key]) ? $this->body[$key] : null;
    }
    
    /*
     * Check query array for given key.
     * @param String $key key to search for
     * @return mixed null, or value of key requested
     */
    public function query($key) {
        return isset($this->query[$key]) ? $this->query[$key] : null;
    }
    
    private function sanitize_inputs($data) {
        $clean_input = array();
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $clean_input[$key] = $this->sanitize_inputs($value);
            }
        } else {
            $clean_input = $this->sanitize_input($data);
        }
        return $clean_input;
    }
    
    /* 
     * TODO - improve this?
     * Sanitize a single input */
    private function sanitize_input($data) {
        return htmlspecialchars($data);
    }
} ?>
    