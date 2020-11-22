<?php namespace CODERS\Repository\Admin;
/**
 * 
 */
final class SettingsView extends \CODERS\Repository\View{

    protected final function getSettingsUrl(){
        return \CODERS\Repository\Request::url('admin.settings');
    }
}