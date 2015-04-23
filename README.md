# Lightweight REST Service for PHP

Create a json REST service in php quickly. This library allows you to quickly route URL's to "resources", and within those resources you have CRUD operations.

## Directory Structure

```
website/
    |_ api/
    |    |_ .htaccess
    |    |_ index.php
    |    |_ resources/
    |        |_ SomeResource.php
    |        |_ SomeOtherResource.php
    |        |_ moreResources/
    |        |   |_ ...
    |        .
```

## .htaccess File (SETUP)
Create an .htaccess file in root of your api directory. In this example, the route is a folder called `api`. Configure the .htaccess file to route all requests under `api/` to the `index.php` file in the root. Note that you could all it `api.php` instead, as long as it matches in the .htaccess file.
```
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?_url=/$1 [QSA,L]
</IfModule>
```

This means that any incoming request to `www.example.com/api/some/odd/path/4` will be routed to `www.example.com/api/` where it can be processed by the library.

## Api root index.php
```php
<?php
/* Include the API library */
require_once('lib/Rest_Api.php');

/* Create a new API with resources located in given relative folder */
$api = new Rest_Api('resources/');

/* Define URLs to route to resources */
$api->map('decks/', 'DeckCollection_Resource.php');
$api->map('decks/{num:deck_id}/', 'Deck_Resource.php');
$api->map('decks/{num:deck_id}/cards/', 'CardCollection_Resource.php');
$api->map('decks/{num:deck_id}/cards/{num:card_id}', 'Card_Resource.php');

/* Lastly, process the request */
$api->process();
?>
```

## Resource files
A resource must extend the `Rest_Resource` class, and it should be located in your resources folder within the api. Note that i can be in subdirectories. This class will define four CRUD operations (POST, GET, PUT, DELETE). In this example we're making a flascard deck resource, called `Deck_Resource.php`. Note that the classname must match the file name.

```php
<?php
class Deck_Resource extends Rest_Resource {
    ...
}
?>
```

Within this resource class, you can overwrite any (or none) of the four methods:
```php
public function resource_post(Rest_Request $request) { ... }
public function resource_get(Rest_Request $request) { ... }
public function resource_put(Rest_Request $request) { ... }
public function resource_delete(Rest_Request $request) { ... }
```

Note that each of those methods is given a [Rest_Request](https://github.com/ddmills/lightweight-rest/blob/master/Rest_Request.php) object.

## The Rest_Request Object

The Rest_Request object provides some variables that you can use to process the request. For our example, if we defined in our api:

```php
$api->map('decks/{num:deck_id}/', 'Deck_Resource.php');
```

And our Deck_Resource.php looks like this:
```php
<?php
class Deck_Resource extends Rest_Resource {
    
    public function resource_get($request) {
        // get the variable called 'deck_id' defined in
        // the uri: www.example.com/api/decks/{deck_id}
        $deck_id = $request->inputs->uri('deck_id');
        
        // retrieve deck from database or something here
        // the database might return this object:
        $deck = array(
            'deck_id'    => $deck_id,
            'name'       => 'My Awesome Deck',
            'subject'    => 'North American Birds',
            'created'    => '2015-10-04 01:42:44',
            'creator_id' => 24,
            'cards'      => array(15, 25, 32)
        );
        
        // notice how we return a php object, and it is not
        // converted to json. The conversion will be done
        // automatically
        return $deck;
    }
}
?>
```

This line:
```php
$deck_id = $request->inputs->uri('deck_id');
```

Is setting the `$deck_id` php variable to the portion of the URI which we labelled as `deck_id` in the `index.php` file at the root of our api: `$api->map('decks/{num:deck_id}/', 'Deck_Resource.php')`. Also note that, `$deck_id` is an integer, as defined by the uri: `{num:deck_id}`.


When someone makes a `GET` request to `www.example.com/api/decks/58`, it means "Give me the deck with deck_id 58", and it would return some json:
```json 
{
    "status":"success",
    "code":200,
    "data":
    {
        "deck_id":58,
        "name":"My Awesome Deck",
        "subject":"North American Birds",
        "created":"2015-10-04 01:42:44",
        "creator_id":24,
        "cards":
        [
            15,
            25,
            32
        ]
    }
}
```
