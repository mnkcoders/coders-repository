<?php namespace CODERS\Repository\Admin;
/**
 * 
 */
final class LogsController extends \CODERS\Repository\Response{
    
    protected function default_action(\CODERS\Repository\Request $request) {
        
        $this->importView('admin.logs')->setLayout('logs')->display();
        
        return TRUE;
    }

}


