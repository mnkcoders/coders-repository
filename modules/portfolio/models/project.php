<?php namespace CODERS\ArtPad\Portfolio;
/**
 * 
 */
final class ProjectModel extends \CODERS\ArtPad\Model{
    
    protected final function __construct(array $data = array()) {
        
        if(array_key_exists('ID', $data)){
            $project = self::loadProject($data['ID']);
            if( $project !== FALSE ){
                $this->__copy($project);
                //override the base collection
                //$this->setValue('collection_id', $collection);
            }
        }
        
        $this->define('item',parent::TYPE_TEXT,array('value'=>''));

        parent::__construct($data);
    }
    /**
     * 
     * @param string $ID
     * @return array
     */
    protected static final function loadProject( $ID ){
        
        return \CODERS\ArtPad\Project::load($ID);
    }

    /**
     * @return array
     */
    protected final function listProjects(){
        
        $db = $this->newQuery();
        
        $filters = array(
            'status' => \CODERS\ArtPad\Project::STATUS_ACTIVE,
            'access_level' => array('public','subscription','nsfw')
        );
        
        //type = NON NSFW PROJECT 
        
        return $db->select('project',
                array('ID','title','content','image_id','date_created'),
                $filters , 'ID', 'ID' );
    }
    /**
     * @return array
     */
    protected final function listItems( ){
        
        $selected = $this->value('item');
        
        if( $selected !== FALSE && strlen($selected)){

            return \CODERS\ArtPad\Resource::collection($selected, TRUE );
        }
        
        $id = $this->collection_id;

        return \CODERS\ArtPad\Resource::collection( $id );
    }
}



