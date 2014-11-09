<?php
/* Include the API library */
require_once('../lib/Rest_Api.php');

/* Create a new API with resources located in given folder */
$api = new Rest_Api('resources/');

/* Disable Cross-Origin Resource Sharing (read more at http://enable-cors.org/) */
$api->disable_cors();

/* Define URL routes to resources */
$api->map('decks/{num:deck_id}/', 'Deck_Resource.php');

/* Lastly, process the request! */
$api->process();
?>