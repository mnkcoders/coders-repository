<?php namespace CODERS\ArtPad\Admin;
/**
 * 
 */
final class ProjectView extends \CODERS\ArtPad\View{
    
    protected final function __construct() {
        
        parent::__construct();
        
    }
    /**
     * @return string
     */
    protected final function displayImage(){
        
        $ID = $this->hasModel() ? $this->model()->image_id : 0;
        
        if( $ID ){
            
            return $this->displayWPImage($ID);
        }
        
        return self::__HTML('span',
                array('class'=>'no-image'),
                __('No image!','coders_artpad'));
    }
    /**
     * @param string $label
     * @return string
     */
    protected final function displayTitle( $label = '' ){
        
        return self::__HTML('input', array(
            'type' => 'text',
            'name' => 'title',
            'value' => $this->hasModel() ? $this->model()->title : '',
            'placeholder' => $label,
            'class' => 'title borderless',
        ) );
    }
    /**
     * @return string
     */
    protected final function displayStatus(){
        
        $status = $this->hasModel() ? $this->model()->status : 0;
        
        $options = \CODERS\ArtPad\Project::listStatus();
        
        return self::renderDropDown('status', $options, $status);
    }
    /**
     * @return string
     */
    protected final function displayCollection(){
        
        $collections = \CODERS\ArtPad\Resource::collection();
        $value = $this->hasModel() ? $this->model()->collection_id : 0;
        $options = array();
        foreach( $collections as $data ){
            $options[ $data['ID'] ] = strlen($data['title']) ? $data['title'] : $data['name'];
        }
        
        return $this->renderDropDown('collection_id',
                $options,
                $value,
                __('Select collection','coders_artpad'),
                'collection');
    }
}