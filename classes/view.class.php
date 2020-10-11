<?php namespace CODERS\Repository;
/**
 * 
 */
abstract class View{
    /**
     * @var \CODERS\Repository\Model
     */
    private $_model = null;
    /**
     * @var string
     */
    private $_layout = 'default';
    /**
     * 
     */
    protected function __construct() {
        
    }
    /**
     * @param string $tag
     * @param array $attributes
     * @param mixed $content
     * @return string
     */
    protected final function __html( $tag , $attributes , $content = null ){
        return '';
    }
    /**
     * @return string
     */
    public final function __toString() {

        $NS = explode('\\', get_class($this ) );
        
        $class = $NS[count($NS) - 1 ];
        
        $suffix = strrpos($class, 'View');
        
        return substr($class, 0 ,$suffix);
    }
    /**
     * Preload all required scripts
     * @param string $module Module name
     * @param array $style_list List of stylesets
     * @param array $script_list List of scripts containing their required dependencies
     */
    public static final function attachScripts( $module , $style_list = array() , $script_list = array() ){
        
        $action_hook = ( $module === 'admin' ) ? 'admin_enqueue_scripts' : 'enqueue_scripts';
        
        add_action( $action_hook , function() use($module,$style_list,$script_list){
            foreach( $style_list as $style ){
                wp_enqueue_style(
                        sprintf('coders-repo-%s-%s',$module,$style),
                        sprintf('%smodules/%s/assets/%s.css',
                        CODERS__REPOSITORY__URL, $module, $style));
            }
            foreach( $script_list as $script => $deps ){
                $include = is_array($deps) ? $deps : strlen( $deps ) ? array($deps) : array();
                wp_enqueue_script(
                        sprintf('coders-repo-%s-%s',$module,$script),
                        sprintf('%smodules/%s/assets/%s.js',
                        CODERS__REPOSITORY__URL, $module, $script),$include);
            }
        });
    }
    /**
     * @param boolean $lowercase 
     * @return string
     */
    public final function module( $lowercase = FALSE ){
        
        $class = explode('\\', $lowercase ?
                strtolower( get_class($this) ) :
                get_class($this));
        
        return count($class) > 1 ?
            $class[count($class) - 2 ] :
            $class[count($class) - 1 ]  ;
    }
    /**
     * @param \CODERS\Repository\Model $model
     * @return \CODERS\Repository\View
     */
    public final function setModel( $model ){
        if( $this->_model === null && is_subclass_of($model, Model::class)){
            $this->_model = $model;
        }
        return $this;
    }
    /**
     * 
     * @param string $layout
     * @return \CODERS\Repository\View
     */
    public final function setLayout( $layout ){
        $this->_layout = $layout;
        return $this;
    }
    /**
     * @param string $name
     * @return mixed
     */
    public final function __get($name) {
        switch( TRUE ){
            case preg_match(  '/^display_/' , $name ):
                $display = preg_replace('/_/', '', $name);
                return method_exists($this, $display) ?
                        $this->$display( ) :
                        sprintf('<!-- INVALID DISPLAY %s -->',$display);
            case preg_match(  '/^is_/' , $name ):
                //RETURN BOOLEAN
                $is = preg_replace('/_/', '', $name);
                return method_exists($this, $is) ?
                        $this->$is( ) :
                        $this->_model->$name();
            case preg_match(  '/^list_/' , $name ):
                //RETURN LIST
                $list = preg_replace('/_/', '', $name);
                return method_exists($this, $list) ?
                        $this->$list() :
                        $this->_model->$name();
            default:
                $get = sprintf('get%s',preg_replace('/_/', '', $name));
                return method_exists($this, $get) ?
                        $this->$get( ) :
                        $this->_model->$name();
        }
    }
    /**
     * @param string $name
     * @param mixed $arguments
     * @return mixed
     */
    public final function __call($name, $arguments) {
        $params = is_array( $arguments ) ? $arguments[0] : $arguments;
        switch( TRUE ){
            case preg_match(  '/^display_/' , $name ):
                $display = preg_replace('/_/', '', $name);
                return method_exists($this, $display) ?
                        $this->$display( $params ) :
                        sprintf('<!-- INVALID DISPLAY %s -->',$name);
            case preg_match(  '/^is_/' , $name ):
                //RETURN BOOLEAN
                $is = preg_replace('/_/', '', $name);
                return method_exists($this, $is) ?
                        $this->$is( $params ) :
                        $this->_model->$name( $params );
            case preg_match(  '/^list_/' , $name ):
                //RETURN LIST
                $list = preg_replace('/_/', '', $name);
                return method_exists($this, $list) ?
                        $this->$list( $params ) :
                        $this->_model->$name( $params );
            default:
                $get = sprintf('get%s',preg_replace('/_/', '', $name));
                return method_exists($this, $get) ?
                        $this->$get( $params ) :
                        $this->_model->$name( $params );
        }
    }
    /**
     * @return string
     */
    public final function getLayout(){
        return $this->_layout === 'default' ?
            strtolower(strval($this)) :
            $this->_layout;
    }
    /**
     * @return \CODERS\Repository\View
     */
    public function display( ){
        
        $path = $this->getView( $this->getLayout( ) );
        printf('<div class="coders-repository %s-view"><!-- CODERS REPO CONTAINER -->',$this->getLayout());
        if( file_exists($path) ){
            require $path;
        }
        else{
            printf('<p>INVALID VIEW PATH <i>%s</i></p>',$path);
        }
        print('<!-- CODERS REPO CONTAINER --></div>');
        
        return $this;
    }
    /**
     * @param string $view
     * @return string
     */
    protected final function getView( $view ){
        return sprintf('%s/modules/%s/html/%s.php',
                CODERS__REPOSITORY__DIR,
                strtolower( $this->module( true ) ),
                $view );
    }
    /**
     * @param string $request
     * @return \CODERS\Repository\Response
     */
    public static final function create( $request ){
        
        $call = explode( '.',$request);
        $module = $call[0];
        $view = count( $call ) > 1 ? $call[1] : 'main';
        
        $path = sprintf('%s/modules/%s/views/%s.php',CODERS__REPOSITORY__DIR,$module,$view);
        $class = sprintf('\CODERS\Repository\%s\%sView',$module,$view);
        
        if(file_exists($path)){
            require_once $path;
            
            if(class_exists($class) && is_subclass_of($class, self::class)){
                return new $class();
            }
            else{
                throw new \Exception(sprintf('Invalid View %s',$class) );
            }
        }
        else{
            throw new \Exception(sprintf('Invalid path %s',$path));
        }
        
        return NULL;
    }
}



