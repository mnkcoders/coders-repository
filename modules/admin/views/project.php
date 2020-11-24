<?php namespace CODERS\Repository\Admin;
/**
 * 
 */
final class ProjectView extends \CODERS\Repository\View{
    
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
                __('No image!','coders_repository'));
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
        
        $options = \CODERS\Repository\Project::listStatus();
        
        return self::renderDropDown('status', $options, $status);
    }
}