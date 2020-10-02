<?php namespace CODERS\Repository\Posts;
/**
 * 
 */
final class MainController extends \CODERS\Repository\Response{
    
    
    protected final function __construct() {
        
        parent::__construct();
        
    }
    /**
     * @param \CODERS\Repository\Request $request
     * @return boolean
     */
    protected final function default_action(\CODERS\Repository\Request $request) {
        
        $model = \CODERS\Repository\Model::create('pub.test');
        $view = \CODERS\Repository\View::create('pub.main');
        $view->setModel( $model )->display();
        
        
        
        
        return TRUE;
    }
    
    protected final function collection_action( \CODERS\Repository\Request $request ){
        
        var_dump(\CodersRepo::collection('default'));
        
        return TRUE;
    }
}


