<?php namespace CODERS\ArtPad\Invite;
/**
 * 
 */
final class LinkView extends \CODERS\ArtPad\View{
    
    protected final function __construct() {
        
        parent::__construct();
        
    }
    
    protected final function getUrl(){
        
        return \CODERS\ArtPad\Request::url('profile');
    }
    /**
     * @return \CODERS\ArtPad\View
     */
    public final function display() {
        
        //get_header();
        parent::display();
        //get_footer();
        
        return $this;
    }
}


