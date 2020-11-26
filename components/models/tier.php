<?php namespace CODERS\Repository;
/**
 * 
 */
final class Tier extends \CODERS\Repository\Model{
    
    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;

    /**
     * @param array $data
     */
    private final function __construct(array $data = array()) {
        
        $this->define('project_id',self::TYPE_TEXT,array('size'=>12))
               ->define('tier',self::TYPE_NUMBER) 
                ->define('title',self::TYPE_TEXT,array('size'=>32))
                ->define('description', self::TYPE_TEXTAREA,array('value' => '' ))
                ->define('image_id',self::TYPE_NUMBER,array('value' => 0 ))
                ->define('status',self::TYPE_NUMBER,array('value' => self::STATUS_DISABLED ))
                ->define('date_created',self::TYPE_DATETIME)
                ->define('date_updated',self::TYPE_DATETIME);
        
        parent::__construct($data);
    }
    
    /**
     * 
     * @return \CODERS\Repository\Project
     */
    public final function getProject(){
        return \CODERS\Repository\Project::load($this->project_id);
    }
    
    
    /**
     * @return int
     */
    public static final function getTierCount(){
        
        $db = self::newQuery();
        
        $query = sprintf("SELECT COUNT(*) AS `tiers` FROM `%s` WHERE `project_id`='%s'",
                $db->table('tier'),
                $this->value('project_id'));
        
        $result = $db->query($query);
        
        if( count($result)){
            return $result[0]['tiers'];
        }
        
        return 0;
    }
    /**
     * @param string $project
     * @return array
     */
    public static final function List( $project ){
        
        $db = self::newQuery();
        
        $query = sprintf("SELECT * FROM `%s` WHERE `project_id`='%s' ORDER BY tier ASC",
                $db->table('tier'),
                $project);
        
        //var_dump($query);
        
        $list = $db->query( $query );
        
        //var_dump($list);

        return $list;
    }
    /**
     * @param string $project_id
     * @param int $level
     * @return boolean|\CODERS\Repository\Tier
     */
    public static final function Load( $project_id , $level ){
        
        $tier = self::newQuery()->select('tier','*',array(
            'project_id'=>$project_id,
            'tier'=>$level));
        
        if( count( $tier ) ){
            return new Tier( $tier );
        }
        
        return FALSE;
    }
    /**
     * @param array $tier_data
     * @return \CODERS\Repository\Tier
     */
    public static final function New( $project, $title ){

        $tier = self::getTierCount() + 1;
        
        $ts = self::__ts();
        
        $tier_data = array(
            'project_id' => $project,
            'tier' => $tier,
            'title' => $title,
            'date_created' => $ts,
            'date_updated' => $ts,
        );
        
        return new Tier( $tier_data );
    }
}



