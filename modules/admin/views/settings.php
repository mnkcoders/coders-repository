<?php namespace CODERS\ArtPad\Admin;
/**
 * 
 */
final class SettingsView extends \CODERS\ArtPad\View{

    protected final function getSettingsUrl(){
        return \CODERS\ArtPad\Request::url('admin.settings');
    }
}