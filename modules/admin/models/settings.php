<?php namespace CODERS\ArtPad\Admin;
/**
 * 
 */
final class SettingsModel extends \CODERS\ArtPad\Model{
    /**
     * @param array $data
     */
    protected final function __construct(array $data = array()) {
        
        $this->define('root_path',self::TYPE_TEXT,array('value' => 'public' ));
        
        
        parent::__construct($data);
    }
    /**
     * @return array
     */
    protected final function listRootPaths(){
        
        return array(
            'private' => __('Out of WWW root Folder','coders_artpad'),
            'public' => __('WP Uploads Folder','coders_artpad'),
        );
    }
}



