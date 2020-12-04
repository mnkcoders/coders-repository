<?php namespace CODERS\ArtPad;
/**
 * 
 */
final class Tier extends \CODERS\ArtPad\Model{
    
    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;
    
    private $_project = FALSE;

    /**
     * @param array $data
     */
    private final function __construct(array $data = array()) {
        
        $this->define('tier_id',self::TYPE_TEXT,array('size'=>24))
                ->define('title',self::TYPE_TEXT,array('size'=>32))
                ->define('level',self::TYPE_NUMBER,array('value'=>1))
                ->define('description', self::TYPE_TEXTAREA,array('value' => '' ))
                ->define('image_id',self::TYPE_NUMBER,array('value' => 0 ))
                ->define('status',self::TYPE_NUMBER,array('value' => self::STATUS_DISABLED ))
                ->define('date_created',self::TYPE_DATETIME)
                ->define('date_updated',self::TYPE_DATETIME);
        
        parent::__construct($data);
    }
    
    /**
     * @return \CODERS\ArtPad\Project|boolean
     */
    public final function getProject(){
        
        if( $this->_project === FALSE ){
            $path = explode('.', $this->value('tier_id'));
            $this->_project = \CODERS\ArtPad\Project::load($path[0]);
        }
        
        return $this->_project;
    }
    /**
     * @return string
     */
    public final function getProjectTitle(){
        $p = $this->getProject();
        if( FALSE !== $p ){
            return $p->title;
        }
        return '';
    }
    /**
     * @return string
     */
    public final function getProjectId(){
        
        $id = explode('.',  $this->value('tier_id') );
        
        return $id[0];
    }
    /**
     * @param string $project
     * @return int
     */
    public static final function getTierCount( $project ){
        
        $db = self::newQuery();
        
        $query = sprintf("SELECT COUNT(*) AS `tiers` FROM `%s` WHERE `tier_id` LIKE ('%s.%%')",
                $db->table('tier'),
                $project);
        
        $result = $db->query($query);
        
        if( count($result)){
            return intval( $result[0]['tiers'] );
        }
        
        return 0;
    }
    /**
     * @param string $project
     * @return array
     */
    public static final function List( $project ){
        
        $db = self::newQuery();
        
        $query = sprintf("SELECT * FROM `%s` WHERE `tier_id` LIKE ('%s.%%') ORDER BY level ASC",
                $db->table('tier'),
                $project);
        
        //var_dump($query);
        
        $list = $db->query( $query , 'tier_id' );
        
        //var_dump($list);

        return $list;
    }
    /**
     * @param string $tier_id
     * @param int $level
     * @return boolean|\CODERS\ArtPad\Tier
     */
    public static final function Load( $tier_id ){
        
        if( strlen( $tier_id) ){
            
            $tier = self::newQuery()->select('tier','*',array('tier_id'=>$tier_id));
            
            if( count( $tier ) ){
                return new Tier( $tier[0] );
            }
        }
        
        return FALSE;
    }
    /**
     * @param string $tier "tier_id.tier_id"
     * @param string $title Tier Title
     * @return \CODERS\ArtPad\Tier
     */
    public static final function New( $tier, $title ){

        $level = self::getTierCount() + 1;
        
        $ts = self::__ts();
        
        $tier_data = array(
            'tier_id' => $tier,
            'level' => $level,
            'status' => self::STATUS_DISABLED,
            'title' => $title,
            'date_created' => $ts,
            'date_updated' => $ts,
        );
        
        return new Tier( $tier_data );
    }
}



