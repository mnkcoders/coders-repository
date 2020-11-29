<?php namespace CODERS\ArtPad\Portfolio;
/**
 * 
 */
final class ProjectModel extends \CODERS\ArtPad\Model{

    /**
     * @return array
     */
    protected final function listProjects(){
        
        $db = $this->newQuery();
        
        $status = \CODERS\ArtPad\Project::STATUS_ACTIVE;
        //type = NON NSFW PROJECT 
        
        return $db->select('project',
                array('ID','title','content','image_id','date_created'),
                array('status'=> $status ) , 'ID' );
    }
}



