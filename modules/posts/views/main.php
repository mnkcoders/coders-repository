<?php namespace CODERS\Repository\Posts;
/**
 * 
 */
final class MainView extends \CODERS\Repository\View{
    
    
    protected final function __construct() {
        
        parent::__construct();
        
        //register public view styles and scripts
        parent::attachStyles(array('style'),'posts');
        parent::attachScripts( array( 'script' => array('jquery' ) ),'posts' );
    }
}




