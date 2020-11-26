<?php namespace CODERS\Repository\Session;

defined('ABSPATH') or die;
/**
 * 
 */
class SessionModule extends \CodersRepo{
    
    
    protected final function __construct() {
        
        $this->component('Account', 'models' );
        $this->component('Session','models');
        
        parent::__construct();
    }
}
    