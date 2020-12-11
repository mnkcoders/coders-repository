<?php namespace CODERS\ArtPad\Admin;
/**
 * 
 */
final class DashboardView extends \CODERS\ArtPad\View{
    
    protected final function __construct() {
        
        parent::__construct();
        
    }
    /**
     * @param string $label
     * @return string
     */
    protected final function displayNew( $label , $value = '' ){
        return self::__HTML('input',array(
            'type' => 'text',
            'name' => 'title',
            'value' => $value,
            'class' => 'form-input',
            'placeholder' => $label
        ) );
    }
    /**
     * @param string $id
     * @return string
     */
    protected final function getDashBoardUrl( ){
        return \CODERS\ArtPad\Request::url('admin.main');
    }
    /**
     * @param string $id
     * @return string
     */
    protected final function getProjectUrl( $id ){
        return \CODERS\ArtPad\Request::url('admin.main.project',array('ID'=>$id));
    }
    /**
     * @param string $id
     * @return string
     */
    protected final function getPublicUrl( $id ){
        return \CODERS\ArtPad\Request::url('pad',array('ID'=>$id));
    }
    /**
     * @param int $status
     * @return string
     */
    protected final function getStatus( $status = 0 ){

        $status_list = \CODERS\ArtPad\Project::listStatus();
        
        return array_key_exists($status, $status_list) ?
                $status_list[ $status ] :
                __('Invalid','coders_artpad');
    }
    /**
     * @param int $media_id
     * @return string
     */
    protected final function displayImage( $media_id ){
        $img = wp_get_attachment_image_src( intval($media_id) , 'full' );
        if( FALSE !== $img ){
            $w= $img[1];
            $h = $img[2];
            /**
             * 
             */
            $class = ($h/$w < 0.6) ? 'portrait' : 'landscape';
            
            return self::__HTML('img', array(
                'src' => $img[0],
                'class' => 'project-image ' . $class,
            ) );
        }
        return self::__HTML('span', array(
            'class' => 'no-image'
        ) , 'No Image' );
    }
}