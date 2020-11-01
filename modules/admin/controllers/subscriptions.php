<?php namespace CODERS\Repository\Admin;
/**
 * 
 */
final class SubscriptionsController extends \CODERS\Repository\Response{
    
    protected final function __construct() {
        
        parent::__construct();
        
    }

    protected function default_action(\CODERS\Repository\Request $request) {
        
        var_dump($request);
        
        return TRUE;
    }

}


