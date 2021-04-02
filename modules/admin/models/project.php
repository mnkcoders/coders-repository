<?php namespace CODERS\ArtPad\Admin;
/**
 * 
 */
final class ProjectModel extends \CODERS\ArtPad\Model{
    
    protected final function __construct(array $data = array()) {
        
        $this->define('ID', parent::TYPE_TEXT, array('size'=>12))
                ->define('title',parent::TYPE_TEXT , array('size'=>64) )
                ->define('content',parent::TYPE_TEXTAREA)
                ->define('status',parent::TYPE_NUMBER)
                ->define('image_id',parent::TYPE_NUMBER,array('value'=>0))
                ->define('connect_patreon',parent::TYPE_CHECKBOX,array('label' => __('Connect to Patreon','coders_artpad')))
                ->define('connect_wc',parent::TYPE_CHECKBOX,array('label' => __('Connect to WooCommerce','coders_artpad')))
                //->define('collection_id',parent::TYPE_NUMBER,array('value'=>0))
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
     * @param int $parent
     * @return array
     */
    protected final function listCollections( $parent = 0 ){
        
        $collections = $this->newQuery()->select('post', '*',
            array( 'parent_id' => $parent, 'tier_id' => $this->ID . '.%' ) ,
            'ID', 'ID' );
        
        //obsolete
        /*$collections = $this->newQuery()->select('post',
                array('ID','name','title'),
                array('parent_id' => $parent ) ,
                'ID',
                'ID' );
         * 
         */
        
        //var_dump($collections);
        
        $output = array();

        foreach( $collections as $ID => $data ){
            //$output[ $ID ] = strlen($data['title']) ? $data['title'] : $data['name'];
            $output[ $ID ] = $data;
            if(strlen($data['title']) === 0 ){
                $data['title'] = $data['name'];
            }
        }
        
        return $output;
    }
    /**
     * @return int
     */
    //protected final function getProgress(){
    //    return random_int( 0 , 100 );
    //}
    //protected final function getSubscriberCount(){
    //    return 0;
    //}
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
                'status' => $meta['status'],
                'image_id' => $meta['image_id'],
                'progress' => random_int(0, 100),
                'subscribers' => random_int(0, 1000),
                'date_created' => $meta['date_created'],
                'date_updated' => $meta['date_updated'],
            );
        }
        return $output;
    }
}



