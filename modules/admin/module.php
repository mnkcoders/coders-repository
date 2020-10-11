<?php namespace CODERS\Repository\Admin;

defined('ABSPATH') or die;
/**
 * 
 */
class AdminModule extends \CodersRepo{
    
    
    protected final function __construct() {
        
        //register styles and scripts using the helper within the view
        \CODERS\Repository\View::attachScripts('admin',
                array('style'),
                array('script'=>array('jquery')));
        //add_filter( 'admin_body_class', 'coders-repository' );
    
        parent::__construct();
    }
    
    /**
     * @param string $action
     */
    public final function run( $action ) {
        
        parent::run( $action );
    }
}
    