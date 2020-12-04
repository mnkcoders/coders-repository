<?php namespace CODERS\ArtPad\Admin;
/**
 * 
 */
final class TierView extends \CODERS\ArtPad\View{
    
    protected final function getUrl( $tier_id ){
        return \CODERS\ArtPad\Request::url('admin.main.tier',array('ID'=>$tier_id));
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
     * 
     * @return CODERS\ArtPad\Admin\TierView
     */
    public final function display() {
        
        if( $this->hasModel()){
            $project_title = $this->model()->project_title;
            $project_id = $this->model()->project_id;
            $tier_title = $this->model()->title;
            $this->addNavPath(__('Dashboard','coders_artpad'), \CODERS\ArtPad\Request::url('admin.main'))
                    ->addNavPath( $project_title, \CODERS\ArtPad\Request::url('admin.main.project', array('ID'=>$project_id)) )
                    ->addNavPath($tier_title);
        }
        
        return parent::display();
    }
}




