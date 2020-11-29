<?php namespace CODERS\ArtPad\Session;

defined('ABSPATH') or die;
/**
 * 
 */
class SessionModule extends \ArtPad{
    
    protected final function __construct() {
        
        $this->component('models.Account' );
        $this->component('models.Session' );
        $this->component('models.Invite' );
        $this->component('services.Mailer');
        
        parent::__construct();
    }
}
    