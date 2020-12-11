<?php namespace CODERS\ArtPad;

final class Project extends \CODERS\ArtPad\Model{
    
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_COMPLETED = 2;
    
    protected final function __construct(array $data = array()) {
        
        $this->define('ID', parent::TYPE_TEXT, array('size'=>12))
                ->define('title',parent::TYPE_TEXT , array('size'=>64,'label'=>'Title') )
                ->define('content',parent::TYPE_TEXTAREA , array('label'=>'Content') )
                ->define('access_level',parent::TYPE_TEXT, array('label'=>'Access Level','value'=>'private'))
                ->define('status',parent::TYPE_NUMBER , array('label'=>'Status','value'=>self::STATUS_INACTIVE))
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
     * @return array
     */
    public static final function listAccessLevel(){
        return array(
            'public' => __('Public','coders_artpad'),
            'subscription' => __('Subscription','coders_artpad'),
            'nsfw' => __('NSFW','coders_artpad'),
            'private' => __('Private','coders_artpad'),
        );
    }
    /**
     * @param string $element
     * @return boolean
     */
    public final function isUpdated( $element ){
        return $this->get($element, 'updated', FALSE);
    }
    /**
     * @return array
     */
    public final function listUpdated(){
        $output = array();
        foreach( $this->elements() as $element ){
            if( $this->isUpdated($element)){
                $output[ $element ] = $this->value($element);
            }
        }
        return $output;
    }
    /**
     * @return array
     */
    public final function listTiers(){
        //var_dump($this->ID);
        return Tier::List($this->ID);
    }
    /**
     * @return boolean
     */
    public final function save(){
        
        $values = $this->listUpdated();
        
        $ID = $this->ID;
        
        $db = self::newQuery();
        
        $result = $db->update('project', $values, array('ID'=>$ID));
        
        return $result > 0;
    }
    /**
     * 
     * @param array $data
     * @return \CODERS\ArtPad\Project
     */
    public final function import(array $data) {
        //parent::import($data);
        foreach( $data as $element => $value ){
            if( $this->has($element)){
                //set value and mark as updated
                $this->setValue($element,$value)->set($element, 'updated', TRUE);
            }
        }
        
        return $this;
    }
    /**
     * @param string $ID
     * @return boolean|\CODERS\ArtPad\Project
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
    /**
     * @param string $title
     * @return \CODERS\ArtPad\Project|Boolean
     */
    public static final function New( $title ){
        
        $ts = self::__ts();
        
        $name = strtolower( preg_replace('/\s+/', '_', $title));
        
        $project = array(
            'ID' => $name,
            'title' => $title,
            'content' => '',
            'date_created' => $ts,
            'date_updated' => $ts,
        );
        
        $db = self::newQuery();
        
        $result = $db->insert('project', $project);
        
        if( $result > 0 ){
            
            return new Project($project);
        }
        
        return FALSE;
    }
    /**
     * Disabled
     */
    public static function create( $request , $data = array() ){
        return FALSE;
    }
}





