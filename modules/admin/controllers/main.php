<?php namespace CODERS\Repository\Admin;
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
        $model = $this->importModel('admin.repository');
        $view = $this->importView('admin.dashboard');
        $view->setModel($model)->setLayout('repository')->display();
        return TRUE;
    }
    
    protected final function dashboard_action( \CODERS\Repository\Request $request ){
        
        return TRUE;
    }
    
    protected final function remove_action( \CODERS\Repository\Request $request ){
        
        return TRUE;
    }
    
    protected final function create_action( \CODERS\Repository\Request $request ){
        
        return TRUE;
    }
    
    protected final function upload_action( \CODERS\Repository\Request $request ){
        
        return TRUE;
    }
    
    protected final function collection_action( \CODERS\Repository\Request $request ){
        
        var_dump(\CodersRepo::collection('default'));
        
        return TRUE;
    }
}


