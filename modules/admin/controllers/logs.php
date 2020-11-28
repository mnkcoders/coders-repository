<?php namespace CODERS\ArtPad\Admin;
/**
 * 
 */
final class LogsController extends \CODERS\ArtPad\Response{
    
    protected function default_action(\CODERS\ArtPad\Request $request) {
        
        $this->importView('admin.logs')->setLayout('logs')->display();
        
        return TRUE;
    }

}


