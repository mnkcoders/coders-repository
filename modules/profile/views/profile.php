<?php namespace CODERS\Repository\Profile;
/**
 * 
 */
final class ProfileView extends \CODERS\Repository\View{
    
    protected final function __construct() {
        
        \CODERS\Repository\View::attachStyles(array('style.css'),$this->module(TRUE));
        \CODERS\Repository\View::attachScripts(array(
            //'vue.js'=>array(),
            'script.js'=>array(),
            ),$this->module(TRUE));
        
        parent::__construct();
        
    }
    /**
     * @return \CODERS\Repository\View
     */
    public final function display() {
        
        get_header();
        parent::display();
        get_footer();
        
        return $this;
    }
}


