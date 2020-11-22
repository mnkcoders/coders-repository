<?php namespace CODERS\Repository\Admin;
/**
 * 
 */
final class SettingsController extends \CODERS\Repository\Response{

    protected function default_action(\CODERS\Repository\Request $request) {

        //$settings = $this->importModel('admin.settings');
        
        $this->importView('admin.settings')->display();
        
        return TRUE;
    }
}