<?php namespace CODERS\Repository\Admin;

defined('ABSPATH') or die;
/**
 * 
 */
class AdminModule extends \CodersRepo{
    
    
    protected final function __construct() {
        
        parent::__construct();

        //register styles and scripts using the helper within the view
        \CODERS\Repository\View::attachStyles(array('style.css'),'admin');
        \CODERS\Repository\View::attachScripts(array('script.js'=>array()),'admin');
        //add_filter( 'admin_body_class', 'coders-repository' );
    }
    
    /**
     * @param string $action
     */
    public final function run( $action = '' ) {
        
        parent::run( $action );
    }
}
    