<?php namespace CODERS\Repository;
/**
 * Request Class
 */
final class Request{
    /**
     * @var array
     */
    private $_input = array();
    /**
     * @var string
     */
    private $_action = 'default';
    /**
     * 
     * @param array $input
     */
    private function __construct( array $input ) {
        
        $this->unpack($input);
    }
    /**
     * @param string $name
     * @return string
     */
    public final function __get($name) {
        return $this->get($name, '');
    }
    public final function action(){
        //return $this->_input['action']
    }
    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public final function get($name,$default=''){
        return array_key_exists($name, $this->_input) ? $this->_input[$name] : $default;
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
     * @param array $input
     * @return \CODERS\Repository\Request
     */
    private final function unpack( array $input ){
        
        foreach( $input as $var => $val ){
            
            if( preg_match('/^coders_repo_/', $var) ){
                $key = preg_replace('/^coders_repo_/', '', $var);
                switch( $key ){
                    case 'action':
                        $this->_action = $val;
                        break;
                    default:
                        $this->_input[ ] = $val;
                        break;
                }
            }
        }
        
        return $this;
    }
    /**
     * @param array $request
     * @return \CODERS\Repository\Request
     */
    public static final function redirect( array $request ){
        
        $input = array();
        
        foreach( $request as $var => $val ){
            if( preg_match('/^coders_repo_/', $var) ){
                $input[$var] = $val;
            }
            else{
                $input['coders_repo_' . $var ] = $val;
            }
        }
        
        return new Request($input);
    }
    /**
     * @return \CODERS\Repository\Request
     */
    public static final function import(  ){
        
        $post = filter_input_array(INPUT_POST );
        
        $get = filter_input_array(INPUT_GET );
        
        $input = array_merge(
                !is_null($get) ? $get : array(),
                !is_null($post) ? $post : array());
        
        return new Request($input);
    }
}