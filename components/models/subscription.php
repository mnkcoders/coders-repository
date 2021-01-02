<?php namespace CODERS\ArtPad;
/**
 * 
 */
final class Subscription extends \CODERS\ArtPad\Model{

    const STATUS_NEW = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_EXPIRED = 2;
    const STATUS_CANCELLED = 3;
    
    /**
     * @param array $data
     */
    private final function __construct(array $data = array()) {
        
        $this->define('ID',self::TYPE_NUMBER)
                ->define('account_id',self::TYPE_NUMBER)
                ->define('tier_id',self::TYPE_TEXT,array('size'=>24))
                //
                ->define('status',self::TYPE_NUMBER,array('value'=>self::STATUS_NEW))
                ->define('date_created',self::TYPE_DATETIME)
                ->define('date_updated',self::TYPE_DATETIME)
                ->define('date_terminated',self::TYPE_DATETIME);
        
        parent::__construct($data);
    }
    /**
     * 
     * @param string $days
     * @return string
     */
    protected static final function __ts( $days = 0 ) {
        
        $ts = parent::__ts();
        
        if( $days > 0 ){
            $offset = strtotime(sprintf('+%s days',$days));
            return date('Y-m-d H:i:s', $offset );
        }
        
        return $ts;
    }
    /**
     * @return \CODERS\ArtPad\Account|boolean
     */
    public final function account(){
        return \CODERS\ArtPad\Account::Load($this->value('account_id'));
    }
    /**
     * @return \CODERS\ArtPad\Tier|boolean
     */
    public final function tier(){
        return \CODERS\ArtPad\Tier::Load($this->value('tier_id'));
    }
    /**
     * @return \CODERS\ArtPad\Project|Boolean
     */
    public final function project(){
        return Project::load($this->getProject());
    }
    /**
     * @return string
     */
    public final function getAccount(){
        return $this->value('account_id');
    }
    /**
     * @return int
     */
    public final function getLevel(){
        $tier = $this->tier();
        return $tier !== FALSE ? $tier->level : 0;
    }
    /**
     * @return string
     */
    public final function getProject(){
        $data = explode('.',  $this->value('tier_id') );
        return $data[0];
    }
    /**
     * @return string
     */
    public final function getTier(){
        $data = explode('.',  $this->value('tier_id') );
        return count($data) > 1 ? $data[1] : $data[0];
    }
    /**
     * @return boolean
     */
    public final function isExpired(){
        return !$this->isActive();
    }
    /**
     * @return boolean
     */
    public final function isActive(){
        
        if( $this->value('status') === self::STATUS_ACTIVE ){
            $terminated = $this->value('date_terminated');
            $target = new \DateTime($terminated);
            $now = new \DateTime();
            return $target >= $now;
        }
        return FALSE;
    }
    /**
     * @return array
     */
    public static final function ListStatus(){
        return array(
            STATUS_NEW => __('New','coders_artpad'),
            STATUS_ACTIVE => __('Active','coders_artpad'),
            STATUS_EXPIRED => __('Expired','coders_artpad'),
            STATUS_CANCELLED => __('Cancelled','coders_artpad'),
        );
    }
    /**
     * @param array $filters
     * @return array
     */
    public static final function List( array $filters = array() ){
        
        return self::newQuery()->select('subscription','*',$filters);
    }
    /**
     * @param \CODERS\ArtPad\Account $account
     * @return array
     */
    public static final function ListByAccount(Account $account ){
        
        return self::List(array('account_id' => $account->value('ID')));
    }
    /**
     * @param \CODERS\ArtPad\Account $account
     * @return array
     */
    public static final function ListActive( Account $account = NULL ){
        
        $filters = array(
                'status' => self::STATUS_ACTIVE
            );
        
        if( !is_null($account)){
            $filters['account_id'] = $account->value('ID');
        }
        
        return self::List($filters);
    }
    /**
     * @param int $ID
     * @return boolean
     */
    public static final function Load( $ID ){
        
        $subscription = self::List(array('ID'=>$ID));
        
        if( count( $subscription )){
            return new Subscription($subscription[0]);
        }
        
        return FALSE;
    }
    /**
     * 
     * @param \CODERS\ArtPad\Account $account
     * @param \CODERS\ArtPad\Tier $tier
     * @param int $days
     * @return boolean
     */
    public static final function New(Account $account , Tier $tier, $days = 30 ){
        
        $ts = self::__ts();
        
        $meta = array(
            //'ID' => 0,
            'account_id' => $account->value('ID'),
            'tier_id' => $tier->tier_id,
            'status' => self::STATUS_ACTIVE,
            'date_created' => $ts,
            'date_updated' => $ts,
            'date_terminated' => self::__ts($days)
        );

        $suid = self::newQuery()->insert('subscription', $meta );

        if( $suid > 0){
            $meta['ID'] = $suid;
            return new Subscription($meta);
        }
        
        return FALSE;
    }
}

