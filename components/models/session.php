<?php namespace CODERS\Repository;
/**
 * 
 */
final class Session extends \CODERS\Repository\Model{
    
    const STATUS_CREATED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_EXPIRED = 2;

    private final function __construct(array $data = array()) {
        
        $this->define('ID', self::TYPE_TEXT, array('size'=>32,'value'=>''))
                ->define('account_id',self::TYPE_NUMBER)
                ->define('status',self::TYPE_NUMBER,array('value'=>self::STATUS_CREATED))
                ->define('date_created',self::TYPE_DATETIME)
                ->define('date_expired',self::TYPE_DATETIME);
        
        parent::__construct($data);
    }
    /**
     * @param string $date
     * @return string
     */
    private static final function expiration( $date ){
        
        $offset = 30;

        return date('Y-m-d H:i:s', strtotime( sprintf('%s + %s days' , $date , $offset ) ) );
    }
    /**
     * @return \CODERS\Repository\Account
     */
    public final function account(){
        
        return Account::Load($this->value('account_id'));
    }
    /**
     * @return boolean
     */
    public final function close(){
        
        if( $this->value('status') < self::STATUS_EXPIRED ){
            $result = $this->newQuery()->update('token',
                    array('status'=>self::STATUS_EXPIRED),
                    array(
                        'ID' => $this->ID,
                        'target' => $this->account_id,
                    ));

            if( $result ){
                $this->setValue('status', self::STATUS_EXPIRED);
            }
        }
        return $this->value('status') === self::STATUS_EXPIRED;
    }
    /**
     * @return boolean
     */
    public final function isActive(){
        
        $active = strtotime($this->value('date_created')) < strtotime($this->value('date_expired'));
        
        if( !$active ){
            $this->close();
        }
        
        return $active;
    }
    /**
     * @param int $ID
     * @return boolean|\CODERS\Repository\Session
     */
    public static final function Resume( $ID ){
        
        $db = self::newQuery();
        
        $data = $db->select('token', '*', array('ID' => $ID,'type'=>'session'));
        
        if( count($data)){
            
            $session = $data[0];
            
            return new Session( array(
                'ID' => $session['ID'],
                'account_id' => $session['target'],
                'status' => $session['status'],
                'date_created' => $session['date_created'],
                'date_expired' => $session['date_expired'],
            ));
            
        }
        
        return FALSE;
    }
    /**
     * @param int $account_id
     * @return \CODERS\Repository\Session
     */
    public static final function New( $account_id ){
        
        $ts = self::__ts();
        
        $data = array(
            //create session
            'ID' => md5(sprintf('%s_%s',$ts,$account_id) ),
            'account_id' => $account_id,
            'status' => self::STATUS_ACTIVE,
            'date_created' => $ts,
            'date_expired' => self::expiration( $ts )

        );
        
        $session = new Session( $data );
        
        $db = self::newQuery();
        
        $result = $db->insert('token', array(
            'ID' => $session->ID,
            'target' => $session->account_id,
            'type' => 'session',
            'status' => $session->status,
            'date_created' => $session->date_created,
            'date_expired' => $session->date_expired,
        ));
        
        return $result > 0 ? $session : FALSE;
    }
}


/**
 * TOKEN TABLE
 *  `ID` varchar(64) NOT NULL,
 `type` varchar(16) NOT NULL,
 `target` int(11) NOT NULL,
 `status` tinyint(1) NOT NULL,
 `date_created` datetime NOT NULL,
 `date_expired` datetime NOT NULL,

 */