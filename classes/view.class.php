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
     * @return string
     */
    public final function __toString() {

        $NS = explode('\\', get_class($this ) );
        
        $class = $NS[count($NS) - 1 ];
        
        $suffix = strrpos($class, 'View');
        
        return substr($class, 0 ,$suffix);
    }
    /**
     * @param string $name
     * @return mixed
     */
    public final function __get($name) {
        switch( TRUE ){
            case preg_match(  '/^action_/' , $name ):
                $action = substr($name, strlen('action_'));
                return self::__HTML('button', array(
                    'type' => 'button',
                    'name' => 'action',
                    'value' => $action,
                    'id' => 'id_'.$action,
                    'class' => 'button button-primary ' . $action,
                ), $this->__label($action));
            case preg_match(  '/^input_/' , $name ):
                $method = preg_replace('/_/', '', $name);
                return method_exists($this, $method) ?
                        $this->$method( ) :
                        $this->__input(substr($name, strlen('input_')));
            case preg_match(  '/^label_/' , $name ):
                $element = substr($name, strlen('label_'));
                $label = $this->__label($element);
                return self::__HTML('label', array(
                        'class' => 'label',
                        'for' => 'id_' . $element
                    ), $label);
            case preg_match(  '/^fieldset_/' , $name ):
                $element = substr($name, strlen('fieldset_'));
                $label = sprintf('label_%s',$element);
                $input = sprintf('input_%s',$element);
                return self::__HTML('fieldset',
                        array('class'=>'form-input'),
                        array($this->$label,$this->$input));
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
            case preg_match(  '/^action_/' , $name ):
                $action = substr($name, strlen('action_'));
                $label = is_array($arguments) && count( $arguments ) ?
                        $arguments[0] :
                        $this->__label($action);
                return self::__HTML('button', array(
                    'type' => 'button',
                    'name' => 'action',
                    'value' => $action,
                    'id' => 'id_'.$action,
                    'class' => 'button button-primary ' . $action,
                ), $label );
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
     * @param string $element
     * @return string
     */
    protected function __input( $element ){
        
        if( $this->_model->has( $element ) ){

            $id = 'id_' . $element;
            $value = $this->_model->getValue($element,FALSE);
            
            switch( $this->_model->type($element) ){
                case Model::TYPE_CHECKBOX:
                    $checkbox = array(
                        'type' => 'checkbox',
                        'name' => $element,
                        'value' => '1',
                        'id' => $id,
                        'class' => 'form-input'
                    );
                    if( $value ){
                        $checkbox['checked'] = 'ckecked';
                    }
                    return self::__HTML( 'label',
                            array( 'class' => 'form-input', 'for' => $id ),
                            array(
                                self::__HTML('input', $checkbox),
                                $this->__label( $element )
                            ));
                    //$this->__label( $input ) . self::__HTML('input', $checkbox );
                case Model::TYPE_NUMBER:
                    return self::__HTML('input', array(
                        'type' => 'number',
                        'name' => $element,
                        'value' => $this->_model->getValue($element,0),
                        'id' => $id,
                        'class' => 'form-input'
                    ));
                case Model::TYPE_CURRENCY:
                    return self::__HTML('input', array(
                        'type' => 'number',
                        'name' => $element,
                        'value' => $this->_model->getValue($element,0),
                        'id' => $id,
                        'class' => 'form-input'
                    ));
                case Model::TYPE_FLOAT:
                    return self::__HTML('input', array(
                        'type' => 'number',
                        'name' => $element,
                        'value' => $this->_model->getValue($element,0),
                        'id' => $id,
                        'class' => 'form-input'
                    ));
                case Model::TYPE_DATE:
                    return self::__HTML('input', array(
                        'type' => 'date',
                        'name' => $element,
                        'value' => $this->_model->getValue($element,''),
                        'id' => $id,
                        'class' => 'form-input'
                    ));
                case Model::TYPE_DATETIME:
                    return self::__HTML('input', array(
                        'type' => 'date',
                        'name' => $element,
                        'value' => $this->_model->getValue($element,''),
                        'id' => $id,
                        'class' => 'form-input'
                    ));
                case Model::TYPE_TEXTAREA:
                    return self::__HTML('textarea', array(
                        'name' => $element,
                        'value' => $this->_model->getValue($element,''),
                        'id' => $id,
                        'class' => 'form-input'
                    ));
                case Model::TYPE_EMAIL:
                    return self::__HTML('input', array(
                        'type' => 'email',
                        'name' => $element,
                        'value' => $this->_model->getValue($element,''),
                        'id' => $id,
                        'class' => 'form-input'
                    ));
                case Model::TYPE_TEXT:
                default:
                    return self::__HTML('input', array(
                        'type' => 'text',
                        'name' => $element,
                        'value' => $this->_model->getValue($element,''),
                        'id' => 'id_' . $element,
                        'class' => 'form-input'
                    ) );
            }
        }
        
        return sprintf('<!-- INVALID INPUT [%s] -->',$element);
    }
    /**
     * @param string $input
     * @return string
     */
    protected function __label( $input ){
        
        $method = sprintf('label%s',$input);
        
        if(method_exists($this, $method) ){
            return $this->$method();
        }
        elseif($this->hasModel()){
            $label = 'label_' . $input;
            return $this->model()->$label;
        }
        return $input;
    }
    /**
     * <element />
     * @param string $TAG
     * @param array $attributes
     * @param mixed $content
     * @return HTML
     */
    protected static final function __HTML( $TAG , array $attributes , $content = null ){

        if( isset( $attributes['class'])){
            if(is_array($attributes['class'])){
                $attributes['class'] = implode(' ', $attributes['class']);
            }
        }
        
        $serialized = array();
        
        foreach( $attributes as $att => $val ){
            $serialized[] = sprintf('%s="%s"',$att,$val);
        }
        
        if( !is_null($content) ){

            if(is_object($content)){
                $content = strval($content);
            }
            elseif(is_array($content)){
                $content = implode(' ', $content);
            }
            
            return sprintf('<%s %s>%s</%s>' , $TAG ,
                    implode(' ', $serialized) , strval( $content ) ,
                    $TAG);
        }
        
        return sprintf('<%s %s />' , $TAG , implode(' ', $serialized ) );
    }
    /**
     * @param string $url
     * @param string $module
     * @return string
     */
    public static final function assetUrl( $url , $module = '' ){
        
        if(strlen($module)){
            return sprintf('%smodules/%s/client/%s',
                    CODERS__REPOSITORY__URL, $module, $url );
        }
        
        //if( preg_match('/http(s?)\:\/\//i', $url) ) {
        //    return $url;
        //}
        
        return $url;
    }
    /**
     * @param string $url
     * @param string $module Empty by default
     * @return string
     */
    public static final function assetName( $url , $module = '' ){
        $path = explode('/', $url);
        $file_name = explode('.',$path[ count($path) - 1 ]);
        return strlen($module) ?
            sprintf('%s-%s-%s', \CodersRepo::ENDPOINT , $module, $file_name[0]) :
            implode('-', $file_name);
    }
    /**
     * Preload all required scripts
     * @param array $script_list List of scripts containing their required dependencies
     * @param string $module Module name
     */
    public static final function attachScripts( $script_list , $module = '' ){
        $action_hook = ( $module === 'admin' ) ? 'admin_enqueue_scripts' : 'enqueue_scripts';
        add_action( $action_hook , function() use( $script_list , $module ){
            foreach( $script_list as $script => $deps ){
                $script_url = \CODERS\Repository\View::assetUrl( $script , $module );
                $script_name = \CODERS\Repository\View::assetName($script , $module );
                wp_enqueue_script($script_name,
                        $script_url,
                        is_array($deps) ? $deps : strlen( $deps ) ? array($deps) : array());
            }
        });
    }
    /**
     * Preload all required scripts
     * @param array $style_list List of stylesets
     * @param string $module Module name
     */
    public static final function attachStyles( $style_list = array() , $module = '' ){
        $action_hook = ( $module === 'admin' ) ? 'admin_enqueue_scripts' : 'enqueue_scripts';
        add_action( $action_hook , function() use( $style_list , $module){
            foreach( $style_list as $style ){
                $style_url = \CODERS\Repository\View::assetUrl( $style , $module);
                $style_name = \CODERS\Repository\View::assetName($style,$module);
                wp_enqueue_style( $style_name , $style_url );
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
     * @return \CODERS\Repository\Model
     */
    protected final function model(){
        return $this->_model;
    }
    /**
     * @return boolean
     */
    protected final function hasModel(){
        return $this->_model !== null;
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
     * @param String $url
     * @return \CODERS\Repository\View
     */
    protected function attachScript( $url ){
        
        print self::__HTML('script', array(
            'type' => 'text/javascript',
            'src' => self::assetUrl($url, $this->module(TRUE)),
        ));
        
        return $this;
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



