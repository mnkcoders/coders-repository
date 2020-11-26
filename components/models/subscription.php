<?php namespace CODERS\Repository;
/**
 * 
 */
final class Subscription extends \CODERS\Repository\Model{

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
                ->define('project_id',self::TYPE_TEXT,array('size'=>12))
                ->define('tier_id',self::TYPE_NUMBER)
                //
                ->define('status',self::TYPE_NUMBER,array('value'=>self::STATUS_NEW))
                ->define('date_created',self::TYPE_DATETIME)
                ->define('date_updated',self::TYPE_DATETIME);
        
        parent::__construct($data);
    }
    
    /**
     * @return \CODERS\Repository\Account|boolean
     */
    public final function getAccount(){
        return \CODERS\Repository\Account::Load($this->value('account_id'));
    }
    /**
     * @return \CODERS\Repository\Tier|boolean
     */
    public final function getTier(){
        return \CODERS\Repository\Tier::Load($this->value('project_id'),$this->value('tier_id'));
    }

    /**
     * @param array $filters
     * @return array
     */
    public static final function List( array $filters = array() ){
        
        return self::newQuery()->select('subscription','*',$filters);
    }
    /**
     * @param \CODERS\Repository\Account $account
     * @return array
     */
    public static final function ListByAccount(Account $account ){
        
        return self::List(array('account_id' => $account->value('ID')));
    }
    /**
     * @param \CODERS\Repository\Account $account
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
     * @param \CODERS\Repository\Account $account
     * @param \CODERS\Repository\Tier $tier
     * @return boolean
     */
    public static final function New(Account $account , Tier $tier ){
        
        $ts = self::__ts();
        
        $meta = array(
            'account_id' => $account->value('ID'),
            'project_id' => $tier->project_id,
            'tier_id' => $tier->tier,
            'status' => self::STATUS_ACTIVE,
            'date_created' => $ts,
            'date_updated' => $ts,
        );
        
        $result = self::newQuery()->insert('subscription', $meta );
        
        if( $result > 0){
            return new Subscription($meta);
        }
        
        return FALSE;
    }
}

