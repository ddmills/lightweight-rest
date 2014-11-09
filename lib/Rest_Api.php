<?php
require_once('Rest_Request.php');
require_once('Rest_Response.php');
require_once('Rest_Resource.php');

class Rest_Api {
    
    /**
     * Property: request
     * A Rest_Request object
     */
    protected $request;
    
    /**
     * Property: mapping
     * A URL mapping of resources and variables
     */
    protected $mapping = array();
    
    /**
     * Property: root
     * API Root
     */
    protected $root = '';
    
    /**
     * Property: resource_root
     * Root folder of Rest_Resource object
     */
    protected $resource_root = '';
    
    /*
     * Property: CORS_ENABLED
     * Cross-Origin Resource Sharing
     */
    protected $CORS_ENABLED = false;
    
    /*
     * @param String $resource_root The location of the resource folder
     */
    public function __construct($resource_root = '') {
        $this->resource_root = $resource_root;
    }
    
    public function set_resource_root($resource_root) {
        $this->resource_root = $resource_root;
    }
    
    public function cors_enabled() {
        return $this->CORS_ENABLED;
    }
    
    public function enable_cors() {
        $this->CORS_ENABLED = true;
    }
    
    public function disable_cors() {
        $this->CORS_ENABLED = false;
    }
    
    public function process() {
        try {
            $this->request = new Rest_Request();
                    
            $current = &$this->mapping;
            $count = count($this->request->endpoint) - 1;
            $inlined_parameters = array();
            
            $resource = '';
            
            foreach($this->request->endpoint as $index => $key) {
                            
                if (isset($current['dir'])) {
                    if (isset($current['dir'][$key])) {
                        $current = &$current['dir'][$key];
                    }
                } elseif (isset($current['num'])) {
                    if (is_numeric($key)) {
                        $inlined_parameters[$current['num']['vname']] = intval($key);
                    }
                    $current = &$current['num'];
                } elseif (isset($current['str'])) {
                    if (is_string($key)) {
                        $inlined_parameters[$current['str']['vname']] = $key;
                    }
                    $current = &$current['str'];
                }
                
                if ($index == $count) {
                    if (isset($current['res'])) {
                        $resource = $current['res'];
                        break;
                    }
                }

            }
            
            if ($resource == '') {
                throw new Exception('Resource not found.', 404);
            }
            
            $data = array();
            
            if (file_exists($this->resource_root . $resource)) {
                include_once($this->resource_root . $resource);
                $base = basename($resource, '.php');
                if (is_subclass_of($base, 'Rest_Resource')) {
                    $res = new $base($this->request);
                    $data = $res->perform_request();
                } else {
                    throw new Exception('Resource file is not a valid Rest_Resource object.', 500);
                }
            } else {
                throw new Exception('Resource file does not exist!', 404);
            }
            
            new Rest_Response($this, $data);
    
        } catch (Exception $e) {
            new Rest_Response($this, $e->getMessage(), $e->getCode());
        }
        
    }
    
    
    /*
     * Map a URL to a Rest_Resource Classname
     */
    public function map($url, $resource) {
        $parts = explode('/', $url);
        $count = count($parts) - 1;
        $location = [];
        foreach($parts as $index=>$part) {
            $var = substr($part, 0, 5);
            if ($var == '{num:') {
                if (substr($part, -1) == '}') {
                    /* is a number */
                    $varname = substr($part, 5, -1);
                    array_push($location, array('num', $varname));
                }
            } elseif ($var == '{str:') {
                if (substr($part, -1) == '}') {
                    /* is a string */
                    $varname = substr($part, 5, -1);
                    array_push($location, array('str', $varname));
                }
            } elseif ($index == $count) {
                /* end of route */
                array_push($location, array('res', $resource));
            } else {
                /* is a directory */
                array_push($location, array('dir', $part));
            }
        }

        $current = &$this->mapping;
        foreach($location as $index => $part) {
            if ($part[0] == 'dir') {
                if (isset($current['dir'])) {
                    if (!isset($current['dir'][$part[1]])) {
                        $current['dir'][$part[1]] = array();
                    }
                } else {
                    $current['dir'] = array();
                    $current['dir'][$part[1]] = array();
                }
                $current = &$current['dir'][$part[1]];
            } elseif ($part[0] == 'num') {
                if (isset($current['num'])) {
                    $current['num']['vname'] = $part[1];
                } else {
                    $current['num'] = array();
                    $current['num']['vname'] = $part[1];
                }
                $current = &$current['num'];
            } elseif ($part[0] == 'str') {
                if (isset($current['str'])) {
                    $current['str']['vname'] = $part[1];
                } else {
                    $current['str'] = array();
                    $current['str']['vname'] = $part[1];
                }
                $current = &$current['str'];     
            } elseif ($part[0] == 'res') {
                $current['res'] = $part[1];
            }
        }
    }

} ?>