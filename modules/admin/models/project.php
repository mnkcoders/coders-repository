<?php namespace CODERS\Repository\Admin;
/**
 * 
 */
final class ProjectModel extends \CODERS\Repository\Model{
    
    protected final function __construct(array $data = array()) {
        
        $this->define('ID', parent::TYPE_TEXT, array('size'=>12))
                ->define('title',parent::TYPE_TEXT , array('size'=>64) )
                ->define('content',parent::TYPE_TEXTAREA)
                ->define('status',parent::TYPE_NUMBER)
                ->define('image_id',parent::TYPE_NUMBER)
                ->define('date_created',parent::TYPE_DATETIME)
                ->define('date_updated',parent::TYPE_DATETIME);
        
        parent::__construct($data);
    }
    /**
     * @param string $image_id
     * @return string
     */
    protected final function getImageUrl( $image_id ){
        $img = wp_get_attachment_image_src($image_id);
        return FALSE !== $img ? $img[ 0 ] : '';
    }
    /**
     * @return string
     */
    protected final function getProjectUrl(){
        return '';
    }
    /**
     * @param int $paginagion
     * @return array
     */
    protected final function listProjects( $paginagion = 20 ){
        
        $query = $this->newQuery();
        
        $projects = $query->select('project');
        
        $output = array();
        foreach ( $projects as $meta ){
            $output[ $meta['ID'] ] = array(
                'title' => $meta['title'],
                //'img' => $this->getImageUrl($meta['image_id']),
                'image_id' => $meta['image_id'],
            );
        }
        return $output;
    }
}



