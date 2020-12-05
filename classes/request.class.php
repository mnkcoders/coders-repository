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
    const _DEFAULT = 'repository.main.default';
    
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
        return $this->get($name, '');
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
     * @return \CODERS\ArtPad\Request
     */
    private final function import( array $input ){
        foreach( $input as $var => $val ){
            switch( $var ){
                case self::ACTION:
                    $this->register($val);
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
    public static final function SID(){
        
        $SID = filter_input(INPUT_COOKIE, sprintf('%s_SID', \ArtPad::ENDPOINT ) );
        
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
    /**
     * @param string $request
     * @param array $args
     * @return string
     */
    public static final function url( $request = self::_DEFAULT , $args = array( ) ){
        $serialized = array();
        $EP = \ArtPad::ENDPOINT;
        $route = explode( '.' , $request );
        $is_admin = $route[ 0 ] === 'admin';
        $url = $is_admin ? admin_url() . 'admin.php' : get_site_url();

        if( $is_admin ){

            $page = $route[ 1 ] === 'main' ?
                    $EP : 
                    $EP . '-' . $route[ 1 ];
            $serialized[ ] = 'page=' . $page;
            
            if( count( $route ) > 2  && $route[2] !== 'default' ){
                $serialized[ ] = sprintf('%s=%s',self::ACTION, $route[2]);
            }
        }
        elseif( FALSE ){
            //public modules using permalink format SEF
            $url .= sprintf( '/%s/%s' ,
                    $EP ,
                    count( $route ) > 1 ? $route[0].'.'.$route[1] : $route[0] . '.main' );

            if( count( $route ) > 2  && $route[2] !== 'default' ){
                //append action
                $url .= '.' . $route[2];
            }
            
            $url .= '/';

            foreach( $args as $var => $val ){
                $serialized[ ] = sprintf('%s=%s',$var,$val);
            }

            //return sprintf('%s?%s', $url , implode('&', $serialized ) );
        }
        else{
            $url .= '/';
            //public modules using PLAIN URL FORMAT (no sef)
            $serialized[ ] = sprintf( '%s=%s' ,
                    $EP ,
                    count( $route ) > 1 ? $route[0].'.'.$route[1] : $route[0] . '.main' );
        
            if( count( $route ) > 2  && $route[2] !== 'default' ){
                $serialized[ ] = sprintf('%s=%s',self::ACTION, $route[2]);
            }
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
    public static final function route( $route = self::_DEFAULT  ){
        
        $input = self::read();
        
        if(is_admin() ){
            unset( $input['page'] );
        }
        else{
            unset( $input[\ArtPad::ENDPOINT  ] );
        }
        
        $path = explode('.', $route);
        
        if(count($path) < 2){ $path[] = 'main'; }
        
        switch( TRUE ){
            case array_key_exists(self::ACTION, $input):
                $input[self::ACTION] = sprintf('%s.%s.%s',
                        $path[0],
                        $path[1],
                        $input[self::ACTION]);
                break;
            case count($path) > 2:
                $input[self::ACTION] = sprintf('%s.%s.%s',
                        $path[0],
                        $path[1],
                        $path[2] );
                break;
            default:
                $input[self::ACTION] = sprintf('%s.%s.default',
                        $path[0],
                        $path[1] );
                break;
        }

        return self::create($input);
    }
    /**
     * @param string $route
     * @return \CODERS\ArtPad\Request
     */
    public static final function ajax( $route ){
        
        $ajax = self::read();
        
        //action is related to wp_ajax_[action] hook: remove it
        //request is related to coders action request: parse it and remove it
        //unset( $ajax['action'] );
        
        $action = explode('.', $route);
        
        if( array_key_exists('request', $ajax) ){
            $action[] = $ajax['request'];
        }

        $input = array_key_exists('data', $ajax) ?
                json_decode($ajax['data'], TRUE) :
            array();
        
        $input[ self::ACTION ] = implode('.', $action);
        
        return new Request( $input );
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
                return array_merge( self::read(INPUT_POST) , self::read(INPUT_GET ) );
            case INPUT_COOKIE:
                $cookie = filter_input_array(INPUT_COOKIE );
                return is_array($cookie) ? $cookie : array();
        }
        return array();
    }
    /**
     * @param string $route
     * @return \CODERS\ArtPad\Request
     */
    public final function redirect( $route = self::_DEFAULT , array $input = array( ) ){
        $action = explode('.', $route);
        $this->_module = $action[0];
        $this->_controller = count( $action ) > 1 ? $action[1] : 'main';
        $this->_action = count( $action ) > 2 ? $action[2] : 'default';
        $this->_input = $input;
        return $this;
    }
}


