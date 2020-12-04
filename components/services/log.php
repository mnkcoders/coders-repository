<?php namespace CODERS\ArtPad\Services;
/**
 * 
 */
class Log{
    
    const TYPE_SYSTEM = 'system';
    const TYPE_SUCCESS = 'success';
    const TYPE_INFO = 'info';
    const TYPE_ALERT = 'alert';
    const TYPE_ERROR = 'error';
    const TYPE_EXCEPTION = 'exception';
    
    private $_message = '';
    private $_type = self::TYPE_INFO;
    private $_context;
    private $_ts;
    
    private final function __construct( $message , $type = self::TYPE_INFO , $context = '', $ts = '') {
        
        $this->_message = $message;
        $this->_type = $type;
        $this->_context = $context;
        
        $this->_ts = strlen($ts) ? $ts : self::ts();
    }
    /**
     * @return string
     */
    public final function __toString() {
        return sprintf('[ %s : %s ] - %s',$this->_ts,$this->_type,$this->_message);
    }
    /**
     * @return string
     */
    private static final function ts (){
        
        return date('Y-m-d H:i:s');
    }
    /**
     * @return \CODERS\ArtPad\Log
     */
    public final function save( ){
        
        //INSERT INTO columns values ...
        $db = new \CODERS\ArtPad\Query();
        $result = $db->insert('logs', $this->data());
        
        if( $result === 0 ){
            //
        }
        
        return $this;
    }
    /**
     * @return array
     */
    public final function data(){
        return array(
            'message' => $this->_message,
            'type' => $this->_type,
            'context' => $this->_context,
            'date_created' => $this->_ts
        );
    }
    
    /**
     * @param string $message
     * @return \CODERS\ArtPad\Log
     */
    public static final function info( $message ){
        
        return new Log($message, self::TYPE_INFO );
    }
    /**
     * @param string $message
     * @return \CODERS\ArtPad\Log
     */
    public static final function system( $message ){
        
        return new Log($message, self::TYPE_SYSTEM);
    }
    /**
     * @param string $message
     * @return \CODERS\ArtPad\Log
     */
    public static final function success( $message ){
        
        return new Log($message, self::TYPE_SUCCESS );
    }
    /**
     * @param string $message
     * @return \CODERS\ArtPad\Log
     */
    public static final function alert( $message ){
        
        return new Log($message, self::TYPE_ALERT );
    }
    /**
     * @param string $message
     * @return \CODERS\ArtPad\Log
     */
    public static final function error( $message ){
        
        return new Log($message, self::TYPE_ERROR );
    }
    /**
     * @param string $message
     * @return \Exception
     */
    public static final function exception( $message ){
        
        $log = new Log($message, self::TYPE_EXCEPTION );
        
        return new \Exception( $log->_message );
    }
    /**
     * @param array $filters
     * @return array
     */
    public static final function list( array $filters = array( ) ){
        
        $db = new \CODERS\ArtPad\Query();
        
        return $db->select('logs','*',$filters);
    }
}
