<?php namespace CODERS\ArtPad\Invite;

defined('ABSPATH') or die;
/**
 * 
 */
class InviteModule extends \ArtPad{
    /**
     * 
     */
    protected final function __construct() {
        
        $this->include('Models.invite');
        
        parent::__construct();

    }
    /**
     * @param string $route
     * @return boolean
     */
    public final function run( $route = '' ) {
        
        $request = \CODERS\ArtPad\Request::route('invite.link');
        
        $link = $request->get('link','');
        
        $display = \CODERS\ArtPad\View::create('invite.link');
        
        if(strlen($link)){
            $invite = \CODERS\ArtPad\Invite::load($link);
            if( FALSE !== $invite && $invite->activate( ) ){
                $display->setModel($invite)->setLayout('activate')->display();
                return TRUE;
            }
        }
        
        $display->setLayout('invalid')->display();
        return FALSE;
    }
}
    