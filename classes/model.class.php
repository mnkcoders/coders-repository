<?php namespace CODERS\Repository;

abstract class Model{
    
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_TEXT = 'text';
    const TYPE_EMAIL = 'email';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_NUMBER = 'number';
    const TYPE_FLOAT = 'float';
    const TYPE_CURRENCY = 'currency';
    const TYPE_DATE = 'date';
    const TYPE_DATETIME = 'datetime';
    
    /**
     * @var array
     */
    private $_dictionary = array(
        
    );
    /**
     * @param array $data
     */
    protected function __construct( array $data = array( ) ) {
        
        if( count( $data ) ){
            $this->populate($data);
        }
    }
    /**
     * @return string
     */
    public final function __toString() {
        
        $name = explode('\\', get_class($this));
        
        return $name[count($name) - 1 ];
    }
    /**
     * @param string $name
     * @return mixed
     */
    public final function __get($name) {
        switch( TRUE ){
            case preg_match(  '/^is_/' , $name ):
                //RETURN BOOLEAN
                $is = preg_replace('/_/', '', $name);
                return method_exists($this, $is) ? $this->$is( ) : FALSE;
            case preg_match(  '/^type_/' , $name ):
                //RETURN LIST
                $type = substr($name, strlen('type_'));
                return $this->get($type, 'type', self::TYPE_TEXT);
            case preg_match(  '/^label_/' , $name ):
                //RETURN LIST
                $label = substr($name, strlen('label_'));
                return $this->get($label, 'label', $label );
            case preg_match(  '/^list_/' , $name ):
                //RETURN LIST
                $list = preg_replace('/_/', '', $name);
                return method_exists($this, $list) ? $this->$list() : array();
            default:
                //RETURN CUSTOMIZER GETTER
                $get = sprintf('get%s',preg_replace('/_/', '', $name));
                //OR DEFAULT FALLBACK IF DEFINED IN THE DICTIONARY
                return method_exists($this, $get) ? $this->$get() : $this->value($name);
        }
    }
    /**
     * @param string $name
     * @param mixed $arguments
     * @return mixed
     */
    public final function __call($name, $arguments) {
        switch( TRUE ){
            case preg_match(  '/^label_/' , $name ):
                //RETURN LIST
                $input = substr($name, strlen('label_'));
                return $this->get($input, 'label', $input);
            case preg_match(  '/^is_/' , $name ):
                //RETURN BOOLEAN
                $is = preg_replace('/_/', '', $name);
                return method_exists($this, $is) ? $this->$is( $arguments ) : FALSE;
            case preg_match(  '/^list_/' , $name ):
                //RETURN LIST
                $list = preg_replace('/_/', '', $name);
                return method_exists($this, $list) ? $this->$list( $arguments ) : array();
            default:
                //RETURN STRING
                $get = sprintf('get%s',preg_replace('/_/', '', $name));
                return method_exists($this, $get) ? $this->$get( $arguments ) : $this->value($name);
        }
    }
    
    
    /**
     * @return string
     */
    public final function module(){
        $class = explode('\\', get_class($this));
        return count($class) > 1 ?
            $class[count($class) - 2 ] :
            $class[count($class) - 1 ]  ;
    }
    /**
     * @param string $element
     * @param string $type
     * @param array $attributes
     * @return \CODERS\Repository\Model
     */
    protected final function define( $element , $type = self::TYPE_TEXT , array $attributes = array( ) ){
        
        if( !array_key_exists($element, $this->_dictionary)){
            $this->_dictionary[$element] = array(
                'type' => $type,
            );
            foreach( $attributes as $att => $val ){
                switch( $att ){
                    case 'value':
                        //force value parsing
                        $this->setValue($element,$val);
                        break;
                    default:
                        $this->set($element, $att, $val);
                        break;
                }
            }
        }

        return $this;
    }
    /**
     * Import all default values
     * @param array $input
     * @return \CODERS\Repository\Model
     */
    protected function populate( array $input ){
        foreach( $input as $element => $value ){
            $this->set($element, $value);
        }
        return $this;
    }
    /**
     * Combine elements from
     * @param \CODERS\Repository\Model $model Model to import data from
     * @return \CODERS\Repository\Model
     */
    public function import( \CODERS\Repository\Model $model ){
        
        foreach( $model->elements() as $element ){
            $this->set($element,$model->value($element));
        }
        
        return $this;
    }
    /**
     * @param string $element
     * @return boolean
     */
    public function exists( $element ){
        //return array_key_exists($element, $this->_dictionary);
        return $this->has($element);
    }
    /**
     * @param string $element
     * @param string $attribute
     * @return boolean
     */
    public final function has( $element , $attribute = '' ){
        
        if( array_key_exists($element, $this->_dictionary) ){
            if(strlen($attribute) ){
                return array_key_exists( $attribute, $this->_dictionary[$element]);
            }
            return TRUE;
        }
        
        return FALSE;
    }
    /**
     * @param string $element
     * @param string $attribute
     * @param mixed $default
     * @return mixed
     */
    protected function get( $element , $attribute , $default = FALSE ){
        if( $this->has($element, $attribute)){
            return $this->_dictionary[$element][$attribute];
        }
        return $default;
    }
    /**
     * @param string $element
     * @param mixed $default
     */
    public function value( $element , $default = FALSE ){
        return $this->get($element, 'value', $default );
    }
    /**
     * @param string $element
     * @param string $attribute
     * @param mixed $value
     * @return \CODERS\Repository\Model
     */
    protected final function set( $element , $attribute , $value ){
        if(array_key_exists($element, $this->_dictionary)){
            $this->_dictionary[$element][ $attribute ] = $value;
        }
        return $this;
    }
    /**
     * 
     * @param type $element
     * @param type $value
     * @return \CODERS\Repository\Model
     */
    protected function setValue( $element , $value = FALSE ){
        $customSetter = sprintf('set%s',$element);
        if(method_exists($this, $customSetter)){
            //define a custom setter for a more extended behavior
            $this->$customSetter( $value );
        }
        elseif( $this->exists($element)){
            switch( $this->_dictionary[$element]){
                case self::TYPE_CHECKBOX:
                    if( !is_bool($value)){ $value = FALSE; }
                    break;
                case self::TYPE_CURRENCY:
                case self::TYPE_FLOAT:
                case self::TYPE_NUMBER:
                    if( !is_numeric($value)){ $value = 0; }
                    break;
                default:
                    if( !is_string($value)){ $value = ''; }
                    break;
            }
            return $this->set($element, 'value', $value);
        }
        return $this;
    }
    /**
     * @param string $element
     * @return string|boolean
     */
    public final function type( $element ){
        return $this->has($element) ? $this->_dictionary[$element]['type'] : FALSE;
    }
    /**
     * @return array
     */
    public final function dictionary(){ return $this->_dictionary; }
    /**
     * @return array
     */
    public final function elements(){ return array_keys($this->_dictionary); }
    /**
     * @return array
     */
    public final function values(){
        $output = array();
        foreach( $this->elements() as $element ){
            $output[$element] = $this->value($element , '' );
        }
        return $output;
    }
    
    
    
    
    
    /**
     * @param string $request
     * @return \CODERS\Repository\Model | boolean
     * @throws \Exception
     */
    public static final function create( $request ){
        
        try{
            $extract = explode('.', $request);
            $module = $extract[0];
            $model = count($extract)  > 1 ? $extract[1] : 'main';

            $path = sprintf('%s/modules/%s/models/%s.php',
                    preg_replace( '/\\\\/', '/', CODERS__REPOSITORY__DIR ),
                    strtolower( $module ), strtolower( $model ) );

            $class = sprintf('\CODERS\Repository\%s\%sModel',
                    strtolower( $module ),strtolower( $model ) );

            if(file_exists($path)){
                require_once $path;
                if(class_exists($class) && is_subclass_of($class, self::class)){
                    return new $class();
                }
                else{
                    throw new \Exception(sprintf('Invalid Model %s',$class) );
                }
            }
            else{
                throw new \Exception(sprintf('Invalid path %s',$path) );
            }
            return FALSE;
        }
        catch (Exception $ex) {
            die( $ex->getMessage());
        }
        
        return FALSE;
    }
}


