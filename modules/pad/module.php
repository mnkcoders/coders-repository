<?php namespace CODERS\ArtPad\Pad;

defined('ABSPATH') or die;
/**
 * 
 */
class PadModule extends \ArtPad{
    /**
     * 
     */
    protected final function __construct() {
        
        parent::include('Models.Session');
                
        parent::__construct();

    }
    /**
     * @param string $route
     * @return boolean
     */
    public final function run( $route = '' ) {

        $SID =\CODERS\ArtPad\Request::SID();
        
        if( strlen($SID) && \CODERS\ArtPad\Session::Resume( $SID ) ){
            return parent::run($route);
        }

        //redirect to loign
        \CODERS\ArtPad\View::create('session')->setLayout('login')->display();
        
        return TRUE;
    }
}
    