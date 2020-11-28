<?php namespace CODERS\ArtPad\Profile;
/**
 * 
 */
final class ProfileView extends \CODERS\ArtPad\View{
    
    protected final function __construct() {
        
        \CODERS\ArtPad\View::attachStyles(array('style.css'),$this->module(TRUE));
        \CODERS\ArtPad\View::attachScripts(array(
            //'vue.js'=>array(),
            'script.js'=>array(),
            ),$this->module(TRUE));
        
        parent::__construct();
        
    }
    /**
     * @return \CODERS\ArtPad\View
     */
    public final function display() {
        
        get_header();
        parent::display();
        get_footer();
        
        return $this;
    }
}


