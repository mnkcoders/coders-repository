<?php namespace CODERS\ArtPad;
/**
 * 
 */
final class Invite extends \CODERS\ArtPad\Model{
    
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
     * @return string
     */
    public final function __toString() {
        return $this->ID;
    }
    /**
     * @return string
     */
    public final function link(){
        $value = $this->value('ID');
        return strlen($value) ? Request::url('invite', array( 'link' => $value ) ) : '#invalid';
    }
    /**
     * @return boolean
     */
    public final function isActive(){
        return $this->value('status') === self::STATUS_ACTIVE;
    }
    /**
     * @return boolean
     */
    public final function activate(){
        
        if( !$this->isActive() ){
            
            $account_id = $this->account_id;

            $session = Session::New($account_id);

            if( FALSE !== $session ){

                $updated = $this->newQuery()->update('token',
                        array('status'=>self::STATUS_ACTIVE), 
                        array('ID'=>$this->value('ID'),'type'=>'invite'));

                if( $updated ){
                    $this->setValue('status',self::STATUS_EXPIRED);
                }
            }

            return $session;
        }
        
        return $this->isActive();
    }
    /**
     * @param int $account_id
     */
    private static final function resetTokens( $account_id ){
        
        $db = self::newQuery();
        
        return $db->update('token',
                array('status'=>self::STATUS_EXPIRED),
                array('target'=>$account_id,'type'=>'invite'));
    }
    /**
     * @param string $ID
     * @param boolean $activeOnly
     * @return \CODERS\ArtPad\Invite|boolean
     */
    public static final function load( $ID , $activeOnly = FALSE ){
        
        $filters = array('ID'=>$ID,'type'=>'invite');
        
        if( $activeOnly ){
            $filters['status'] = self::STATUS_ACTIVE;
        }
        
        $token = self::newQuery()->select('token','*',$filters );
        
        $invite = count($token) ? $token[0] : array();
        
        return count($invite) ? new Invite(array(
            'ID' => $invite['ID'],
            'account_id' => $invite['target'],
            'status' => $invite['status'],
            'date_created' => $invite['date_expired'],
            'date_expired' => $invite['date_expired'],
        )) : FALSE;
    }
    /**
     * @param \CODERS\ArtPad\Account $account
     * @param boolean $resetOlder
     * @return \CODERS\ArtPad\Invite|boolean
     */
    public static final function new( Account $account , $resetOlder = FALSE ){
        
        $account_id = $account->ID;

        if( $resetOlder && self::resetTokens($account_id) ){
            ///previous tokens reseted!
        }
        
        $ID = md5($account_id . date('YmdHis') );
        $ts = $this->__ts();
        
        $token = array(
            'ID' => $ID,
            'type'=>'invite',
            'target' => $account_id,
            'status' => self::STATUS_CREATED,
            'date_created' => $ts,
            'date_expired' => $ts
        );

        $result = $this->newQuery()->insert('token', $token);
        
        return $result !== FALSE && $result > 0 ? new Invite( $token ) : FALSE;
    }
}


