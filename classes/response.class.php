<?php namespace CODERS\Repository;
/**
 * Description of controller
 */
class Response {
    
    private $_attributes = array(
        
    );
    
    protected function __construct( ) {

    }
    /**
     * @param string $att
     * @param mixed $value
     * @return \CODERS\Repository\Response
     */
    protected final function set( $att , $value ){
        $this->_attributes[$att]  = $value;
        return $this;
    }
    /**
     * @param string $att
     * @param mixed $default
     * @return mixex
     */
    protected final function get( $att , $default = FALSE ){
        return array_key_exists($att, $this->_attributes) ? $this->_attributes[$att] : $default;
    }
    /**
     * 
     * @param string $name
     * @return mixed
     */
    public final function __get($name) {

        $att = sprintf('get%sAttribute', preg_replace('/_/', '', $name));

        return (method_exists($this, $att)) ? $this->$att() : $this->get($name,FALSE);
    }
    /**
     * @param string $name
     * @param mixed $arguments
     * @return mixed
     */
    public final function __call($name, $arguments) {

        switch( TRUE ){
            case preg_match(  '/^display_/' , $name ):
                $method = sprintf('display%sMethod', preg_replace('/display_/', '', $name));
                //var_dump($method);
                break;
            default:
                $method = sprintf('get%sMethod',preg_replace('/_/', '', $name));
                break;
        }

        return (method_exists($this, $method)) ? $this->$method( count($arguments) ? $arguments[0]  : array( ) ) : FALSE;
    }
    /**
     * @param string $view
     * @return string
     */
    protected final function getView( $view ){
        return sprintf('%s/html/%s.php',__DIR__,$view);
    }
    /**
     * 
     * @param string $view
     * @return \CODERS\Repository\Response
     */
    protected final function display( $view ){
        
        printf('<div class="coders-repository %s-view"><!-- CODERS REPO CONTAINER -->',$view);
        
        require $this->getView($view);
        
        print('<!-- CODERS REPO CONTAINER --></div>');
        
        return $this;
    }
    protected final function request( ){
        
        $request = \CODERS\Repository\Request::import();
        
        $method = sprintf('%s_action',$request->action());
        
        return self::response( method_exists($this, $method) ? $this->$method( self::request() ) : FALSE );

    }
    /**
     * @param mixed $output
     * @return JSON
     */
    protected static final function response( $output ){
        
        switch( TRUE ){
            case is_null($output):
                //null responses are considered as empty, nothing bad happened, then TRUE
                return NULL;
            case is_array($output):
                //serialize the whole array
                return json_encode( $output );
            case is_object($output):
                //parse to string
                return json_encode( array( 'response'=> intval( $output->toString() ) ) );
            case is_bool($output):
                return json_encode( array( 'response'=> intval( $output ) ) );
            //case is_numeric($output):
            //case is_string($output):
            default:
                return json_encode( array( 'response'=> $output ) );
        }
    }
    /**
     * @param String $route
     * @return \CODERS\Repository\Response
     */
    public static final function route( $route = '' ){
        
        $root = explode('/',  preg_replace('/\\\\/', '/', __DIR__ ) );
        
        $path = sprintf('%s/admin/controllers/%s.php' , implode('/',array_slice($root, 0, count($root)-1)) , strtolower( $route ) );
        
        $class = sprintf('\CODERS\Repository\Controllers\%s',$route);
        //$root = sprintf('%s/../admin/controllers/%s.php', preg_replace('/\\\\/', '/', __DIR__ ), strtolower( $route ) );
        
        if(file_exists($path)){

            require_once $path;
            
            if(class_exists($class)){

                $C = new $class();
                
                return self::response( $C->request() );
            }
        }
        
        return new Response( $route );
        
        $method = $route . '_action';
        
        return self::response( method_exists($ctl, $method) ? $ctl->$method( self::request() ) : FALSE );
    }
}





