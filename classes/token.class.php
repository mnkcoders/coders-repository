<?php namespace CODERS\ArtPad;
/**
 * 
 */
final class Token{

    const TYPE_INVITE = 'invite';
    const TYPE_SESSION = 'session';
    const TYPE_LINK = 'link';
    
    const STATUS_CREATED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_EXPIRED = 2;
    
    private $_token = array(
        'ID' =>FALSE,
        'type' =>FALSE,
        'owner' =>FALSE,
        'status' =>FALSE,
        'date_created' =>FALSE,
        'date_expired' =>FALSE,
    );
    /**
     * @param array $token
     */
    private final function __construct( array $token ) {
        foreach( $token  as $var => $val ){
            if( isset( $this->_token[ $var ] ) ){
                switch( $var ){
                    case 'owner':
                    case 'status':
                        $this->_token[ $var ] = intval($val);
                        break;
                    default:
                        $this->_token[ $var ] = $val;
                        break;
                }
            }
        }
    }
    /**
     * @param string $name
     * @return string
     */
    public final function __get($name) {
        return isset( $this->_token[$name] ) ? $this->_token[ $name ] : FALSE;
    }
    /**
     * @return string
     */
    public final function __toString() {
        $ID = $this->_token['ID'];
        return strlen($ID) ? $ID : '#INVALID';
    }
    /**
     * @param boolean $stamp
     * @return string
     */
    private static final function __ts( $stamp = FALSE ){
        return $stamp ? 
                date('YmdHis') :
                date('Y-m-d H:i:s') ;
    }
    /**
     * @param int $owner_id
     * @return string
     */
    private static final function __hash( $owner_id ){
        return md5( $owner_id . self::__ts(TRUE) );
    }
    /**
     * @param string $date
     * @return string
     */
    private static final function __offset( $date , $offset = 30 ){
        
        return date('Y-m-d H:i:s', strtotime( sprintf('%s + %s days' , $date , $offset ) ) );
    }
    /**
     * @return boolean
     */
    private final function __expired(){
        return $this->expiredTime() < time();
    }
    /**
     * @param int $sid
     * @param string $expires
     * @return boolean
     */
    private static final function __cookie( $sid , $expires = '' ){
        $key = \ArtPad::ENDPOINT;
        $offset = strlen($expires) ? strtotime($expires) : 3600;
        return setcookie( $key, $sid, time() + $offset );
    }
    /**
     * @param int $owner
     */
    private final function close( ){
        $owner = $this->owner;
        $ID = $this->ID;
        if( $ID !== FALSE && $owner !== FALSE ){
            $db = new Query();
            $updated = $db->update('token', 
                    array('status'=>self::STATUS_EXPIRED),
                    array('owner'=>$owner,'ID' => $ID ) );
            
            if( $updated ){
                setcookie( \ArtPad::ENDPOINT, '' , time() - 3600 );
            }
        }
        return FALSE;
    }
    /**
     * @param int $owner_id
     */
    private static final function reset( $owner_id ){
        $db = new Query();
        return $db->update('token',
                array('status'=>self::STATUS_EXPIRED),
                array('owner'=>$owner_id));
    }
    
    /**
     * @return int
     */
    public final function createdTime(){
        $created = $this->date_created;
        return $created !== FALSE ? strtotime($created) : 0;
    }
    /**
     * @return int
     */
    public final function expiredTime(){
        $expired = $this->date_expired;
        return $expired !== FALSE ? strtotime($expired) : 0;
    }
    
    /**
     * @return string
     */
    public final function url(){
        return sprintf('%s/%s/link-%s',
                get_site_url(),
                \ArtPad::ENDPOINT,
                $this->ID);
    }
    /**
     * @return \CODERS\ArtPad\Account|FALSE
     */
    public final function account(){
        
        $owner = $this->owner;
        
        return $owner !== FALSE ? Account::Load( $owner ) : FALSE;
    }
    /**
     * @return boolean
     */
    public final function isReady(){
        return $this->status === self::STATUS_CREATED;
    }
    /**
     * @return boolean
     */
    public final function isActive(){
        
        $status = $this->status;
        
        return $status === self::STATUS_ACTIVE  && !$this->isExpired();
    }
    /**
     * @return boolean
     */
    public final function isExpired(){
        $status = $this->status;
        if( $status < self::STATUS_EXPIRED ){
            if( !$this->__expired() ){
                return FALSE;
            }
            else{
                $this->close();
            }
        }
        return TRUE;
    }
    /**
     * @todo Define here the Session Create method
     * @return \CODERS\ArtPad\Session|boolean
     */
    public final function activate(){
        $ID = $this->ID;
        $expiration = $this->date_expired;
        if( FALSE !== $ID && $this->status < self::STATUS_ACTIVE ){
            $db = new Query();
            $updated = $db->update('token',
                    array( 'status' => self::STATUS_ACTIVE , 'type' => self::TYPE_SESSION ), 
                    array( 'ID' => $ID , 'type' => self::TYPE_LINK , 'status' => self::STATUS_CREATED ) );
            if( $updated && self::__cookie( $ID , $expiration ) ){
                $this->_token['status'] = self::STATUS_ACTIVE;
                $this->_token['type'] = self::TYPE_SESSION;
            }
        }
        return $this->isActive();
    }
    /**
     * @param string $ID
     * @param boolean $activeOnly
     * @return \CODERS\ArtPad\Invite|boolean
     */
    public static final function load( $ID , $activeOnly = FALSE ){
        
        $filters = array('ID'=>$ID);
        
        if( $activeOnly ){
            $filters['status'] = self::STATUS_ACTIVE;
        }
        
        $db = new Query();
        $token = $db->select('token','*',$filters );
        
        if( count($token ) ){
            $link = $token[0];
            return new Token(array(
                'ID' => $link['ID'],
                'type' => $link['type'],
                'owner' => $link['owner'],
                'status' => $link['status'],
                'date_created' => $link['date_expired'],
                'date_expired' => $link['date_expired'],
            ));
        }
        
        return FALSE;
    }
    /**
     * @param int $owner_id
     * @param string $type
     * @param boolean $reset
     * @return \CODERS\ArtPad\Token|boolean
     */
    public static final function create( $owner_id , $type = self::TYPE_LINK , $reset = FALSE ){
        
        if( $reset && self::reset($owner_id) ){
            ///previous tokens reseted!
        }
        
        $ID = self::__hash($owner_id);
        $ts = self::__ts();
        
        $token = array(
            'ID' => $ID,
            'type'=>$type,
            'owner' => $owner_id,
            'status' => self::STATUS_CREATED,
            'date_created' => $ts,
            'date_expired' => self::__offset($ts)
        );
        
        $db = new Query();

        $result = $db->insert('token', $token );
        
        return $result !== FALSE && $result > 0 ? new Token( $token ) : FALSE;
    }
    /**
     * @param string $sid
     * @return boolean
     */
    public static final function resume( $sid ){
        $query = new Query();
        $sessions = $query->select('token','*',array(
            'ID'=>$sid,
            'type'=>self::TYPE_SESSION,
            'status'=>self::STATUS_ACTIVE));
        $token = new Token( $sessions[0] );
        
        return $token->isActive();
    }
    /**
     * @return array
     */
    public static final function list(){
        $db = new Query();
        return $db->select('token','*');
    }
}






