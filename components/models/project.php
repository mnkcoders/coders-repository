<?php namespace CODERS\Repository;

final class Project extends \CODERS\Repository\Model{
    
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_COMPLETED = 2;
    
    protected final function __construct(array $data = array()) {
        
        $this->define('ID', parent::TYPE_TEXT, array('size'=>12))
                ->define('title',parent::TYPE_TEXT , array('size'=>64,'label'=>'Title') )
                ->define('content',parent::TYPE_TEXTAREA , array('label'=>'Content') )
                ->define('status',parent::TYPE_NUMBER , array('label'=>'Status'))
                ->define('image_id',parent::TYPE_NUMBER,array('value'=>0))
                ->define('collection_id',parent::TYPE_NUMBER,array('value'=>0))
                ->define('date_created',parent::TYPE_DATETIME , array('label'=>'Created'))
                ->define('date_updated',parent::TYPE_DATETIME , array('label'=>'Updated'));
        
        parent::__construct($data);
    }
    
    /**
     * @return array
     */
    public static final function listStatus(){
        return array(
            self::STATUS_INACTIVE => __('Inactive','codrers_repository'),
            self::STATUS_ACTIVE => __('Active','codrers_repository'),
            self::STATUS_COMPLETED => __('Completed','codrers_repository'),
            
        );
    }
    /**
     * @param string $ID
     * @return boolean|\CODERS\Repository\Project
     */
    public static final function load( $ID ){
        
        if(strlen($ID)){
            $query = self::newQuery();

            $data = $query->select('project','*',array('ID' => $ID ));

            if( count( $data )){
                return new Project($data[0]);
            }
        }
        
        return FALSE;
    }
}





