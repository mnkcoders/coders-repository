<?php namespace CODERS\Repository\User;
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
        $model = $this->importModel('user.account');
        $view = $this->importView('user.profile');
        $view->setModel($model)->setLayout('profile')->display();
        return TRUE;
    }
}


