<?php namespace CODERS\Repository;
/**
 * Request Class
 */
final class Request{
    
    const FORMAT_TIMESTAMP = 'YmdHis';
    const FORMAT_DATETIME = 'Y-m-d H:i:s';
    const FORMAT_DATE = 'Y-m-d';
    
    const ACTION = '_action';
    const CONTROLLER = '_controller';
    const MODULE = '_module';
    const _DEFAULT = 'repository.main.default';
    
    //const DEF_MODULE = 'repository';
    //const DEF_CONTROLLER = 'main';
    //const DEF_ACTION = 'default';
    
    /**
     * @var array
     */
    private $_input = array( );
    
    private $_action = 'default';
    private $_controller = 'main';
    private $_module = '';
    
    private $_fingerprint;
    private $_ts = 0;
    
    /**
     * 
     * @param array $input
     */
    private function __construct( array $input ) {
        
        $this->_ts = time();
        //$this->_ts = date('YmdHis');
        
        $this->unpack($input)->readFP();
    }
    /**
     * @return string
     */
    public final function __toString() {
        return sprintf('%s.%s.%s',
                $this->module(),
                $this->controller(),
                $this->action());
    }
    /**
     * @param string $name
     * @return string
     */
    public final function __get($name) {
        return $this->get($name, '');
    }
    /**
     * Import fingerprint
     * @return \CODERS\Repository\Request
     */
    private final function readFP(){

        $client = array(
            filter_input(INPUT_SERVER, 'REMOTE_ADDR'),
            filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
        );
        
        $this->_fingerprint = md5( base64_encode( implode('|', $client ) ) );
        
        return $this;
    }
    /**
     * @return \CODERS\Repository\Request
     */
    private final function register( $request = '' ){
        if(strlen($request)){
            $path = explode('.', $request);
            $this->_module = $path[0];
            if( count( $path ) > 1 ){
                $this->_controller = $path[1];
            }
            if( count( $path ) > 2 ){
                $this->_action = $path[2];
            }
        }
        if(strlen($this->_module) === 0 ){
            $this->_module = is_admin() ? 'admin' : 'repository';
        }
        return $this;
    }
    /**
     * @param array $input
     * @return \CODERS\Repository\Request
     */
    private final function unpack( array $input ){
        foreach( $input as $var => $val ){
            switch( $var ){
                case self::ACTION:
                    $this->register($val);
                    break;
                default:
                    $this->_input[ $var ] = $val;
                    break;
            }
            //$this->_input[ $var ] = $val;
            /*if( preg_match('/^coders_repo_/', $var) ){
                $key = preg_replace('/^coders_repo_/', '', $var);
                switch( $key ){
                    default:
                        
                        break;
                }
            }*/
        }
        //if(strlen($this->module()) === 0 ){
        //    $this->_input[ self::MODULE ] = is_admin() ? 'admin' : 'repository';
        //}
        return $this;
    }
    /**
     * @return array
     */
    public final function export(){
        return array(
            self::ACTION => strval($this),
            'ts' => $this->_ts,
            'fp' => $this->_fingerprint,
            'input' => $this->_input,
        );
    }
    /**
     * @return string
     */
    public final function ts(){
        return $this->_ts;
    }
    /**
     * @param string $format
     * @return string
     */
    public final function time( $format = self::FORMAT_DATETIME ){
        return date( $format ,$this->_ts );
    }
    /**
     * @return string
     */
    public final function ID(){
        return sprintf('[%s::%s]',$this->_fingerprint,$this->time(self::FORMAT_TIMESTAMP));
    }
    /**
     * @return array
     */
    public final function input(){
        return $this->_input;
    }
    /**
     * @return string
     */
    //public final function action(){ return $this->_input[self::ACTION]; }
    public final function action(){ return $this->_action; }
    /**
     * @return string
     */
    //public final function controller(){ return $this->_input[self::CONTROLLER] ; }
    public final function controller(){ return $this->_controller; }
    /**
     * @return string
     */
    //public final function module(){ return $this->_input[self::MODULE]; }
    public final function module(){ return $this->_module; }
    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public final function get($name,$default=''){
        return array_key_exists($name, $this->_input) ?
                $this->_input[$name] :
                $default;
    }
    /**
     * @param string $name
     * @return int
     */
    public final function getInt( $name ){
        return array_key_exists($name, $this->_input) ? intval( $this->_input[$name] ) : 0;
    }
    /**
     * @param string $name
     * @return array
     */
    public final function parseArray( $name , $sep = ',' ){
        return array_key_exists($name, $this->_input) ?
                explode( $sep, $this->_input[$name] ) :
                array();
    }
    /**
     * HASH CODE from the current client's request fingerprint
     * @return string
     */
    public final function fingerPrint(){
        
        return md5( $this->_fingerprint );
    }
    /**
     * @return string
     */
    public final function SID(){
        
        $SID = filter_input(INPUT_COOKIE, 'CODERS_SID');
        
        return $SID !== NULL ? $SID : '';
    }
    /**
     * @return int
     */
    public final function userID(){
        
        return 0;
    }
    /**
     * @return WP_User
     */
    public final function userName( $niceName = FALSE ){
        
        $ID = $this->userID();
        
        if( $ID ){
            
            $user = get_user_by('ID', $ID);
            
            if( $user !== FALSE ){
                
                return $niceName ? $user->user_nicename : $user->user_login;
            }
        }
        
        return '';
    }
    public static final function url( $request = self::_DEFAULT , $args = array( ) ){

        $var = explode('.',$request);
        
        if( $var[ 0 ] === 'admin' ){
            
            $params = array(
                'page' => 'coders-repository',
                self::ACTION => $request
                );
            
            foreach( $args as $var => $val ){
                if( $var !== self::ACTION ){
                    $params[] = sprintf('%s=%s',$var,$val);
                }
            }

            return sprintf('%s?%s', admin_url() , implode('&', $params ) );
        }
        else{
            
            return sprintf('%s?%s=%s' , get_site_url( ) , \CodersRepo::ENDPOINT );
        }
    }
    /**
     * @param array $request
     * @return \CODERS\Repository\Request
     */
    public static final function create( array $request ){ return new Request( $request ); }
    /**
     * @param string $route
     * @return \CODERS\Repository\Request
     */
    public static final function route( $route = self::_DEFAULT  ){
        
        $input = self::read();
        
        $input[ self::ACTION ] = $route;
        
        return self::create($input);
    }
    /**
     * @param string $route
     * @return \CODERS\Repository\Request
     */
    public static final function ajax( $route = 'admin.ajax' ){

        $input = self::read(INPUT_POST);
        $data = isset($input['data']) ? json_decode($input['data'], TRUE) : array();
        $data[ self::ACTION ] = isset($input[self::ACTION]) ?
                sprintf('%s.%s',$route,$input[self::ACTION]) :
                $route;
        return self::create( $data );
    }
    /**
     * @param string $request
     * @return \CODERS\Repository\Request
     */
    public static final function import(  ){
        
        //$input = self::extract();
        
        return self::create( self::read( ) );
    }
    /**
     * @return array
     */
    public static final function read( $input = INPUT_REQUEST ){
        
        switch( $input ){
            case INPUT_POST:
                $post = filter_input_array( INPUT_POST );
                return is_array($post) ? $post : array();
            case INPUT_GET:
                $get = filter_input_array( INPUT_GET );
                return is_array($get) ? $get : array();
            case INPUT_REQUEST:
                $post = self::read(INPUT_POST);
                $get = self::read(INPUT_GET);
                return array_merge( $get , $post );
            case INPUT_COOKIE:
                $cookie = filter_input_array(INPUT_COOKIE );
                return is_array($cookie) ? $cookie : array();
        }
        
        return array();
    }
}


