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
public function resource_post(Rest_Request $request)
public function resource_get(Rest_Request $request)
public function resource_put(Rest_Request $request)
public function resource_delete(Rest_Request $request)
```

Note that each of those methods is given a [Rest_Request](https://github.com/ddmills/lightweight-rest/blob/master/Rest_Request.php) object.

## The Rest_Request Object

The Rest_Request object provides some variables that you can use to process the request. For our example, if we defined in our api:

```php
$api->map('decks/{num:deck_id}/', 'Deck_Resource.php');
```

if someone makes a `GET` request to `www.example.com/api/decks/58`, meaning "Give me the deck with id 58", we would have:

```php
<?php
class Deck_Resource extends Rest_Resource {
    ...
}
?>
```


## Step 2
Setup your resources. These are objects that extend the Rest_Resource class which define the CRUD operations.

If we were to have a deck of cards, we might have four resources: collection of decks, deck, collection of cards, and a card.

Anything that the CRUD methods return will be converted into JSON. This is what the Deck_Resource might look like:
```php
<?php
class Deck_Resource extends Rest_Resource {
    /* CREATE */
    public function resource_post($request) {
        /* get inputs called "name" and "cards" */
        $name = $request->inputs->get('name');
        $cards = $request->inputs->get('cards');
        
        if ($name && $cards) {
            return 'Created an object called ' . $name;
        } else {
            throw new Exception('Missing paramters', 400);
        }
    }
    
    /* READ */
    public function resource_get($request) {
        /* This is where you would perform database operations */
    
        /* retrieve the variable from the URI.
         * alternatively, use inputs->get($key) */
        $deck_id = $request->inputs->uri('deck_id');
        return 'You want a deck with id: ' . $deck_id;
    }
    
    /* UPDATE */
    public function resource_put($request) {
        /* Return arrays of data, which will later be converted to json */
        return array(
            'update_time' => '.025 seconds',
            'changed' => '5 properties changed',
            'last_update' => '10/4/1942'
        );
    }
    
    /* DELETE */
    public function resource_delete($request) {
        /* you can throw an exception if you don't want this action to be performed. 
         *Alternatively, if you simply leave the method out, it will also throw an exception when called. */
        throw new Exception('This deck cannot be deleted', 405);
    }
}
?>
```
To help with organization, you can put your resources in folders organized however you want.


##Step 3
index.php from example folder:
```php
<?php
/* Include the API library */
require_once('../lib/Rest_Api.php');

/* Create a new API with resources located in given folder */
$api = new Rest_Api('resources/');

/* Disable Cross-Origin Resource Sharing (read more at http://enable-cors.org/) */
$api->disable_cors();

/* Define URL routes to resources */
$api->map('decks/', 'DeckCollection_Resource.php');
$api->map('decks/{num:deck_id}/', 'Deck_Resource.php');
$api->map('decks/{num:deck_id}/cards/', 'CardCollection_Resource.php');
$api->map('decks/{num:deck_id}/cards/{num:card_id}', 'Card_Resource.php');

/* Lastly, process the request! */
$api->process();
?>
```

If you perform a PUT request to path/to/api/decks/12452/ , it will return this JSON:
```
{"status":"success","code":200,"data":{"update_time":".025 seconds","changed":"5 properties changed","last_update":"10/4/1942"}}
```


