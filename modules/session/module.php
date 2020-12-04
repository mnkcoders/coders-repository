<?php namespace CODERS\ArtPad\Session;

defined('ABSPATH') or die;
/**
 * 
 */
class SessionModule extends \ArtPad{
    
    protected final function __construct() {
        
        $this->include('Models.Account' )
                ->component('Models.Session' )
                ->component('Models.Invite' )
                ->component('Services.Mailer');
        
        parent::__construct();
    }
}
    