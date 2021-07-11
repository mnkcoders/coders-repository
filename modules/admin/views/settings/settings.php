<?php namespace CODERS\ArtPad\Admin;
/**
 * 
 */
final class SettingsView extends \CODERS\ArtPad\View{
    /**
     * @return string
     */
    protected final function getSettingsUrl(){
        return \CODERS\ArtPad\Request::url('admin.settings');
    }
    /**
     * @return string|HTML
     */
    protected final function displayRootPath(){
        
        $options = $this->hasModel() ? $this->model()->list_root_paths : array();
        $value = $this->hasModel() ? $this->model()->root_path : 'public';
        
        return self::renderDropDown('root_path', $options, $value ) ;
    }
}