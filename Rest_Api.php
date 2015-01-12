<?php
require_once('Rest_Request.php');
require_once('Rest_Response.php');
require_once('Rest_Resource.php');
require_once('Rest_Inputs.php');

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
   * Property: headers_sent
   * set true when headers have been sent
   */
  protected $headers_sent = false;

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
   * Property: FORMAT_OUTPUT
   * should output be formatted to {status:"", code: "", data: ""}
   * or just output data
   */
  protected $FORMAT_OUTPUT = false;

  /*
   * @param String $resource_root The location of the resource folder
   */
  public function __construct($resource_root = '') {
    $this->resource_root = $resource_root;
    register_shutdown_function(array($this, '_send_fatal_error_shutdown'));
  }

  public function set_resource_root($resource_root) {
    $this->resource_root = $resource_root;
  }

  public function _send_fatal_error_shutdown() {
    $error = error_get_last();
    if ($error) {
      new Rest_Response($this, $error, 500);
    }
  }

  public function format_output($val = null) {
    if (!is_null($val)) {
      $this->FORMAT_OUTPUT = $val ? true : false;
    }
    return $this->FORMAT_OUTPUT;
  }

  /* Check if CORS is enabled */
  public function cors_enabled($val = null) {
    if (!is_null($val)) {
      $this->CORS_ENABLED = $val ? true : false;
    }
    return $this->CORS_ENABLED;
  }

  /* Check if headers have been sent yet */
  public function headers_sent() {
    return $this->headers_sent;
  }

  /* signal that the headers have been sent */
  public function set_headers_sent() {
    $this->headers_sent = true;
  }

  /* Process the current request */
  public function process() {
    try {
      $this->request = new Rest_Request();

      $current = &$this->mapping;
      $count = count($this->request->endpoint) - 1;
      $inlined_parameters = array();

      $resource = '';

      foreach($this->request->endpoint as $index => $key) {

        if (isset($current['dir']) && isset($current['dir'][$key])) {
          $current = &$current['dir'][$key];
        } elseif (isset($current['num']) && is_numeric($key)) {
          $this->request->inputs->uri[$current['num']['vname']] = intval($key);
          $current = &$current['num'];
        } elseif (isset($current['str']) && is_string($key)) {
          $this->request->inputs->uri[$current['str']['vname']] = $key;
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
        throw new Exception('Resource file does not exist!', 500);
      }

      new Rest_Response($this, $data);

    } catch (Exception $e) {

      new Rest_Response($this, array('error' => $e->getMessage()), $e->getCode());
    }
  }


  /*
   * TODO - consolidate two for loops into one!
   * Map a URL to a Rest_Resource Classname
   */
  public function map($url, $resource) {
    $parts    = explode('/', $url);
    $count    = count($parts) - 1;
    $location = array();

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