<?php namespace CODERS\ArtPad\Session;

defined('ABSPATH') or die;
/**
 * 
 */
class SessionModule extends \ArtPad{
    
    
    protected final function __construct() {
        
        $this->component('Account', 'models' );
        $this->component('Session','models');
        
        parent::__construct();
    }
    
    
    
    
    
    public final function run($route = '') {
        
        return parent::run($route);

    }
}
    