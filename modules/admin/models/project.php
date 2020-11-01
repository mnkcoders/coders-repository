<?php namespace CODERS\Repository\Admin;
/**
 * 
 */
final class ProjectModel extends \CODERS\Repository\Model{
    
    protected final function __construct(array $data = array()) {
        
        $this->define('ID', parent::TYPE_TEXT, array('length'=>12))
                ->define('title',parent::TYPE_TEXT , array('length'=>64) )
                ->define('content',parent::TYPE_TEXTAREA)
                ->define('status',parent::TYPE_NUMBER)
                ->define('image_id',parent::TYPE_NUMBER)
                ->define('date_created',parent::TYPE_DATETIME)
                ->define('date_updated',parent::TYPE_DATETIME);
        
        parent::__construct($data);
    }

}