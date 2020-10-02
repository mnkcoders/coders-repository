<?php namespace CODERS\Repository\Pub;
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
        
        print 'IK';
        var_dump($this);
        
        return TRUE;
    }
    
    protected final function collection_action( \CODERS\Repository\Request $request ){
        
        var_dump(\CodersRepo::collection('default'));
        
        return TRUE;
    }
}


