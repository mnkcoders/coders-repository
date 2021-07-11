<?php namespace CODERS\ArtPad;
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
    //const _DEFAULT = 'repository.main.default';

    /**
     * @var string
     */
    private $_fingerprint;
    /**
     * @var int
     */
    private $_ts = 0;
    /**
     * @var string
     */
    private $_module = 'public';
    /**
     * @var string
     */
    private $_controller = 'main';
    /**
     * @var string
     */
    private $_action = 'default';
    /**
     * @var array
     */
    private $_input = array( );
    /**
     * @param array $input
     */
    private function __construct( array $input ) {
        $this->readFP()->import($input);
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
        return $this->request($name,'');
    }
    /**
     * Import fingerprint
     * @return \CODERS\ArtPad\Request
     */
    private final function readFP(){

        $client = array(
            filter_input(INPUT_SERVER, 'REMOTE_ADDR'),
            filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
        );
        
        $this->_fingerprint = md5( base64_encode( implode('|', $client ) ) );
        
        $this->_ts = time();       

        return $this;
    }
    /**
     * @return \CODERS\ArtPad\Request
     */
    /*private final function register( $request = '' ){
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
    }*/
    /**
     * @param array $input
     * @return \CODERS\ArtPad\Request
     */
    private final function import( array $input ){
        foreach( $input as $var => $val ){
            switch( $var ){
                case self::ACTION:
                    $route = self::PARSE($val);
                    $this->_module = $route['module'];
                    $this->_controller = $route['controller'];
                    $this->_action = $route['action'];
                    break;
                default:
                    $this->_input[ $var ] = $val;
                    break;
            }
        }
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
            'input' => $this->input(),
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
     * @return string
     */
    //public final function action(){ return $this->_input[self::ACTION]; }
    public final function action(){
        return $this->_action;
    }
    /**
     * @return string
     */
    //public final function controller(){ return $this->_input[self::CONTROLLER] ; }
    public final function controller(){ return $this->_controller; }
    /**
     * @param boolean $capitalize
     * @return string
     */
    //public final function module(){ return $this->_input[self::MODULE]; }
    public final function module( $capitalize = FALSE ){
        return $capitalize ? ucfirst($this->_module) : $this->_module;
    }
    /**
     * @return array
     */
    public static final function input(){ return $this->_input; }
    /**
     * @param string $name
     * @param string $default
     * @return string
     */
    public final function request( $name , $default = '' ){
        return array_key_exists( $name, $this->_input) ? $this->_input[$name] : $default;
    }
    /**
     * @param string $var
     * @param string $default
     * @return string
     */
    public static final function post( $var , $default = '' ){
        $input = filter_input(INPUT_POST, $var);
        return !is_null($input) ? $input : $default;
    }
    /**
     * @param string $var
     * @param string $default
     * @return string
     */
    public static final function get( $var , $default = '' ){
        $input = filter_input(INPUT_GET, $var);
        return !is_null($input) ? $input : $default;
    }
    /**
     * @param string $name
     * @return int
     */
    public final function getInt( $name ){
        return intval( $this->request($name,0) );
    }
    /**
     * @param string $name
     * @return array
     */
    public final function getArray( $name , $sep = ',' ){
        $input = $this->request($name,'');
        return strlen($input) ?  explode( $sep, $input ) : array();
    }
    /**
     * HASH CODE from the current client's request fingerprint
     * @return string
     */
    public final function fingerPrint(){
        return md5( $this->_fingerprint );
    }
    /**
     * @return string|boolean
     */
    public static final function SID(){
        $SID = filter_input(INPUT_COOKIE, sprintf('%s_sid', \ArtPad::ENDPOINT ) );
        return $SID !== NULL && strlen($SID) ? $SID : FALSE;
    }
    /**
     * Get WordPress current|Active User ID
     * @return int
     */
    public static final function UID(){
        return get_current_user_id();
    }
    /**
     * @return WP_User
     */
    public static final function userName( $niceName = FALSE ){
        $ID = self::UID();
        if( $ID ){
            $user = get_user_by('ID', $ID);
            
            if( $user !== FALSE ){
                
                return $niceName ? $user->user_nicename : $user->user_login;
            }
        }
        return '';
    }
    /**
     * @param string $route
     * @return \CODERS\ArtPad\Request
     */
    public final function redirect( $route , array $input = array( ) ){
        $path = self::PARSE($route);
        $this->_module = $path['module'];
        $this->_controller = $path['controller'];
        $this->_action = $path['action'];
        $this->_input = $input;
        return $this;
    }
    /**
     * @param string $request
     * @param array $args
     * @return string
     */
    public static final function url( $request  , $args = array( ) ){
        $serialized = array();
        $EP = \ArtPad::ENDPOINT;
        $route = self::PARSE($request);
        $is_admin = $route[ 'module' ] === 'admin';
        $url = $is_admin ? admin_url() . 'admin.php' : get_site_url();

        if( $is_admin ){
            $page = $route[ 'controller' ] === 'main' ?
                    $EP : 
                    $EP . '-' . $route[ 'controller' ];
            $serialized[ ] = 'page=' . $page;
            $serialized[ ] = sprintf('%s=%s',self::ACTION, $route['action']);
        }
        elseif( TRUE ){
            //public modules using permalink format SEF
            $url .= sprintf( '/%s/%s-%s-%s/' ,
                    $EP ,
                    $route['module'],
                    $route['controller'],
                    $route['action'] );

            foreach( $args as $var => $val ){
                $serialized[ ] = sprintf('%s=%s',$var,$val);
            }
           //return sprintf('%s?%s', $url , implode('&', $serialized ) );
        }
        else{
            $url .= '/';
            //public modules using PLAIN URL FORMAT (no sef)
            $serialized[ ] = sprintf( '%s=%s-%s' , $EP , $route['module'], $route['controller'] );
            $serialized[ ] = sprintf('%s=%s',self::ACTION, $route['action']);
            //if( count( $route ) > 2  && $route[2] !== 'default' ){}
        }

        foreach( $args as $var => $val ){
            $serialized[ ] = sprintf('%s=%s',$var,$val);
        }
        
        return count( $serialized ) ?
                sprintf('%s?%s', $url , implode('&', $serialized ) ) :
                $url;
    }
    /**
     * @param array $request
     * @return \CODERS\ArtPad\Request
     */
    public static final function create( array $request ){ return new Request( $request ); }
    /**
     * @param string $route
     * @return \CODERS\ArtPad\Request
     */
    public static final function route( $route  ){
        
        $input = self::READ();
        $path = self::PARSE( $route );
        
        if(is_admin() ){
            unset( $input['page'] );
        }
        else{
            unset( $input[\ArtPad::ENDPOINT  ] );
        }
        
        if(array_key_exists(self::ACTION, $input) ){
            $path['action'] = $input[self::ACTION];
        }
        $input[ self::ACTION ] = implode('.', $path);
        
        return new Request( $input );
        //return self::create($input);
    }
    /**
     * @param string $route
     * @return \CODERS\ArtPad\Request
     */
    public static final function ajax( $route ){
        
        $ajax = self::READ();
        
        //action is related to wp_ajax_[action] hook: remove it
        //request is related to coders action request: parse it and remove it
        //unset( $ajax['action'] );
        
        $input = array_key_exists('data', $ajax) ?
                json_decode($ajax['data'], TRUE) :
            array();
        
        $input[ self::ACTION ] = $route;
        
        return new Request($input);
        //return new Request( $input );
    }
    /**
     * @return array
     */
    private static final function READ( $input = INPUT_REQUEST ){
        switch( $input ){
            case INPUT_POST:
                $post = filter_input_array( INPUT_POST );
                return is_array($post) ? $post : array();
            case INPUT_GET:
                $get = filter_input_array( INPUT_GET );
                return is_array($get) ? $get : array();
            case INPUT_REQUEST:
                return $_REQUEST;
                //return array_merge( self::READ(INPUT_POST) , self::READ(INPUT_GET ) );
            case INPUT_COOKIE:
                $cookie = filter_input_array(INPUT_COOKIE );
                return is_array($cookie) ? $cookie : array();
        }
        return array();
    }
    /**
     * @return string|URL
     */
    public static final function fullPath(){
        $host = filter_input(INPUT_SERVER, 'HTTP_HOST');
        $uri = filter_input(INPUT_SERVER, 'REQUEST_URI');
        
        $https = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' );
        return sprintf(sprintf('%s://%s%s',
                $https ? 'https' : 'http',
                $host, $uri));
    }
    /**
     * @return string
     */
    public static final function match( ){

        $url = self::fullPath();
        
        $pattern = sprintf('/\/artpad\/%s.[a-z\-0-9]/i',\ArtPad::RESOURCE);
        
        if( preg_match($pattern, $url) ){
            $match = sprintf('/%s/%s.', \ArtPad::ENDPOINT,\ArtPad::RESOURCE);
            return preg_replace(
                    '/\//', '', 
                    substr($url, strpos($url, $match ) + strlen($match)));
        }
        
        return '';
    }
    /**
     * @return array
     */
    public static final function headers(){
        if(function_exists('getallheaders')){
            return getallheaders();
        }
        elseif( function_exists('apache_request_headers')){
            return apache_request_headers();
        }
        return array();
    }
    /**
     * @param array $request
     */
    private static final function PARSE( $request ){
        $path = explode('.', $request);
        return array(
            'module' => $path[0],
            'controller' => count( $path ) > 1 ? $path[ 1 ] : 'main',
            'action' => count( $path ) > 2 ? $path[ 2 ] : 'default',
        ); 
    }
}


