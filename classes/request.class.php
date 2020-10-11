<?php namespace CODERS\Repository;
/**
 * Request Class
 */
final class Request{
    
    const ACTION = 'action';
    const CONTROLLER = 'controller';
    const MODULE = 'module';
    
    const DEF_MODULE = 'Posts';
    const DEF_CONTROLLER = 'main';
    const DEF_ACTION = 'default';
    
    /**
     * @var array
     */
    private $_input = array(
        self::ACTION => 'default',
        self::CONTROLLER => 'main',
        self::MODULE => 'Posts',
    );
    /**
     * 
     * @param array $input
     */
    private function __construct( array $input ) {
        
        $this->unpack($input);
    }
    /**
     * @param array $input
     * @return \CODERS\Repository\Request
     */
    private final function unpack( array $input ){
        foreach( $input as $var => $val ){
            $this->_input[ $var ] = $val;
            /*if( preg_match('/^coders_repo_/', $var) ){
                $key = preg_replace('/^coders_repo_/', '', $var);
                switch( $key ){
                    default:
                        
                        break;
                }
            }*/
        }
        return $this;
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
     * @return array
     */
    public final function input(){
        return $this->_input;
    }
    /**
     * @return string
     */
    public final function action(){ return $this->_input[self::ACTION]; }
    /**
     * @return string
     */
    public final function controller(){ return $this->_input[self::CONTROLLER] ; }
    /**
     * @return string
     */
    public final function module(){ return $this->_input[self::MODULE]; }
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
     * @param array $request
     * @return \CODERS\Repository\Request
     */
    public static final function create( array $request ){ return new Request( $request ); }
    /**
     * @param string $context
     * @return \CODERS\Repository\Request
     */
    public static final function import( $context = self::DEF_MODULE  ){
        
        $post = filter_input_array(INPUT_POST );
        
        $get = filter_input_array(INPUT_GET );
        
        $input = array_merge(
                !is_null($get) ? $get : array(),
                !is_null($post) ? $post : array());
        
        if(strlen($context)){
            $path = explode('.', $context);
            $input[self::MODULE] = $path[0];
            if( count( $path ) > 1 ){
                $input[self::CONTROLLER] = $path[1];
            }
            if( count( $path ) > 2 ){
                $input[self::ACTION] = $path[2];
            }
        }
        
        return self::create($input);
    }
}


