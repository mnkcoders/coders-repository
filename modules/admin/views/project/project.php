<?php namespace CODERS\ArtPad\Admin;
/**
 * 
 */
final class ProjectView extends \CODERS\ArtPad\View{
    
    protected final function __construct() {
        
        parent::__construct();
        
    }
    
    protected final function getTierUrl( $ID ){
        return \CODERS\ArtPad\Request::url('admin.main.tier',array('ID'=>$ID));
    }
    
    protected final function getProjectUrl( $ID ){
        return \CODERS\ArtPad\Request::url('admin.main.project',array('ID'=>$ID));
    }
    
    protected final function getSaveProjectUrl( $ID ){
        return \CODERS\ArtPad\Request::url('admin.main.save_project',array('ID'=>$ID));
    }
    /**
     * @param string $ID
     * @return string
     */
    protected final function getPublicUrl( $ID = '' ){
        
        if( strlen($ID) === 0 && $this->hasModel() ){
            $ID = $this->model()->ID;
        }
        
        return \CODERS\ArtPad\Request::url('pad',array('ID'=>$ID));
    }

    
    protected final function getTierTitle(){
        
        if( $this->hasModel() ){
            $tier = $this->model()->has('tier_id') ? $this->model()->tier_id : '';
            
            if(strlen($tier)){
                
                return sprintf('%s : %s', $this->project_title,$this->title);
            }
        }
        return '';
    }
    /**
     * @return string
     */
    protected final function displayAccessLevel(){
        $options = $this->hasModel() ? $this->model()->list_access_level : array();
        $value = $this->hasModel() ? $this->model()->access_level : 'private';
        $label = $this->hasModel() ? $this->model()->label_access_level : __('Access Level','coders_artpad');
        
        return self::renderDropDown('access_level', $options, $value, $label );
    }
    /**
     * @return string|html
     */
    protected final function displayPatreonIntegration(){
        
        $value = 0;
        
        return self::renderDropDown('connect_patreon',
                array(0 => __('No'),1=>__('Yes')),
                $value );
        
    }
    /**
     * @return string|html
     */
    protected final function displayWoocommerceIntegration(){
        
        $value = 0;
        
        return self::renderDropDown('connect_wc',
                array(0 => __('No'),1=>__('Yes')),
                $value );
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
    /**
     * @return \CODERS\ArtPad\Admin\ProjectView
     */
    public final function display() {

        
        if( $this->hasModel()){
            $title = $this->model()->title;
            $url = \CODERS\ArtPad\Request::url('admin.main');
            $this->addNavPath(__('Dashboard','coders_artpad') , $url )
                    ->addNavPath($title);
        }
        
        return parent::display();
    }
}


