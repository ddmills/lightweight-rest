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