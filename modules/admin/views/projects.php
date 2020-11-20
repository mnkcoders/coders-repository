<?php namespace CODERS\Repository\Admin;
/**
 * 
 */
final class ProjectsView extends \CODERS\Repository\View{
    
    protected final function __construct() {
        
        parent::__construct();
        
    }
    /**
     * @param string $id
     * @return string
     */
    protected final function getUrl( $id ){

        return '#' . $id;

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