<?php namespace CODERS\ArtPad;

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
    //
    
    /**
     * @var array
     */
    private $_dictionary = array(
        //define model data
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
            case preg_match(  '/^value_/' , $name ):
                //RETURN VALUE
                $element = substr($name, strlen('value_'));
                return $this->value($element);
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
            case preg_match(  '/^error_/' , $name ):
                //RETURN LIST
                $element = substr($name, strlen('error_'));
                return $this->get($element, 'error', '');
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
            case preg_match(  '/^error_/' , $name ):
                if( count( $arguments )){
                    $element = substr($name, strlen('error_'));
                    $this->set($element, 'error', $arguments[0] );
                    return TRUE;
                }
                return FALSE;
            default:
                //RETURN STRING
                $get = sprintf('get%s',preg_replace('/_/', '', $name));
                return method_exists($this, $get) ? $this->$get( $arguments ) : $this->value($name);
        }
    }
    /**
     * @return string
     */
    protected static function __ts(){
        return date('Y-m-d H:i:s');
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
     * @return \CODERS\ArtPad\Model
     */
    protected final function define( $element , $type = self::TYPE_TEXT , array $attributes = array( ) ){
        
        if( !array_key_exists($element, $this->_dictionary)){
            $this->_dictionary[$element] = array(
                'type' => $type,
            );
            switch($type){
                case self::TYPE_CHECKBOX:
                    $this->_dictionary[$element]['value'] = FALSE;
                case self::TYPE_NUMBER:
                case self::TYPE_CURRENCY:
                case self::TYPE_FLOAT:
                    $this->_dictionary[$element]['value'] = 0;
                    break;
                case self::TYPE_TEXT:
                case self::TYPE_TEXTAREA:
                    //others
                default;
                    $this->_dictionary[$element]['value'] = '';
                    break;
            }
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
     * @param \CODERS\ArtPad\Model $source
     * @return \CODERS\ArtPad\Model
     */
    protected final function __copy(Model $source ){
        if( count( $this->_dictionary) === 0 ){
            foreach( $source->_dictionary as $element => $meta ){
                $this->_dictionary[ $element ] = $meta;
            }
        }
        return $this;
    }
    /**
     * Import all default values
     * @param array $input
     * @return \CODERS\ArtPad\Model
     */
    protected function populate( array $input ){
        foreach( $input as $element => $value ){
            $this->setValue($element, $value);
        }
        return $this;
    }
    /**
     * @return boolean
     */
    public function validateData(){
        foreach( $this->elements() as $element ){
            if( !$this->validate($element)){
                return FALSE;
            }
        }
        return TRUE;
    }
    /**
     * @return boolean
     */
    protected function validate( $element ){
        
        if( $this->get($element, 'required', FALSE)){
            $value = $this->value($element);
            switch( $this->type($element)){
                case self::TYPE_CHECKBOX:
                    return TRUE; //always true, as it holds FALSE vaule by default
                case self::TYPE_NUMBER:
                case self::TYPE_CURRENCY:
                case self::TYPE_FLOAT:
                    return FALSE !== $value; //check it's a number
                case self::TYPE_EMAIL:
                    //validate email
                    return preg_match( self::EmailMatch() , $value) > 0;
                //case self::TYPE_TEXT:
                default:
                    $size = $this->get($element, 'size' , 1 );
                    if( FALSE !== $value && strlen($value) <= $size ){
                        return TRUE;
                    }
                    break;
            }
            return FALSE;
        }
        return TRUE;
    }
    /**
     * Combine elements from
     * @param \CODERS\ArtPad\Model $model Model to import data from
     * @return \CODERS\ArtPad\Model
     */
    public function import( \CODERS\ArtPad\Model $model ){
        
        foreach( $model->elements() as $element ){
            $this->setValue($element,$model->value($element));
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
     * @return mixed
     */
    public function value( $element ){
        switch( $this->type($element)){
            case self::TYPE_CHECKBOX:
                return $this->get($element, 'value' );
            case self::TYPE_CURRENCY:
            case self::TYPE_FLOAT:
            case self::TYPE_NUMBER:
                return $this->get($element, 'value' , 0 );
            default:
                return $this->get($element, 'value', '');
        }
        //return $this->get($element, 'value', $default );
    }
    /**
     * @param string $element
     * @param string $attribute
     * @param mixed $value
     * @return \CODERS\ArtPad\Model
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
     * @return \CODERS\ArtPad\Model
     */
    protected function setValue( $element , $value = FALSE ){
        $customSetter = sprintf('set%sValue',$element);
        if(method_exists($this, $customSetter)){
            //define a custom setter for a more extended behavior
            $this->$customSetter( $value );
        }
        elseif( $this->exists($element)){
            switch( $this->type($element)){
                case self::TYPE_CHECKBOX:
                    return $this->set($element,
                            'value',
                            is_bool($value) ? $value : FALSE );
                case self::TYPE_CURRENCY:
                case self::TYPE_FLOAT:
                    return $this->set($element,'value',floatval($value));
                case self::TYPE_NUMBER:
                    return $this->set($element,'value',intval($value));
                default:
                    return $this->set($element,'value',strval($value));
            }
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
    protected final function dictionary(){ return $this->_dictionary; }
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
            $output[$element] = $this->value( $element );
        }
        return $output;
    }
    /**
     * @return \CODERS\ArtPad\Query
     */
    protected static final function newQuery(){
        return new Query();
    }
    
    /**
     * @param string $request
     * @param array $data
     * @return \CODERS\ArtPad\Model | boolean
     * @throws \Exception
     */
    public static final function create( $request , $data = array() ){
        
        try{
            $extract = explode('.', $request);
            $module = $extract[0];
            $model = count($extract)  > 1 ? $extract[1] : 'main';

            $path = sprintf('%s/modules/%s/models/%s.php',
                    preg_replace( '/\\\\/', '/', CODERS__REPOSITORY__DIR ),
                    strtolower( $module ), strtolower( $model ) );

            $class = sprintf('\CODERS\ArtPad\%s\%sModel',
                    strtolower( $module ),strtolower( $model ) );

            if(file_exists($path)){
                require_once $path;
                if(class_exists($class) && is_subclass_of($class, self::class)){
                    return new $class( $data );
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
    
    /**
     * @return string
     */
    protected static final function EmailMatch(){
    
        return "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";
    }
}
/**
 * WPDB Query Handler
 */
final class Query {
    
    private $_table = array();
    
    private $_where = array();
    
    private $_columns = array();
    
    public final function __construct() {
        
    }
    /**
     * @global \wpdb $wpdb
     * @return \wpdb
     */
    private static final function db(){
        global $wpdb;
        return $wpdb;
    }
    /**
     * @global string $table_prefix
     * @return string
     */
    public static final function prefix(){
        global $table_prefix;
        $ns = \ArtPad::ENDPOINT;
        return $table_prefix . $ns;
    }
    /**
     * @param string $table
     * @return string
     */
    public static final function table( $table ){
        return sprintf('%s_%s',self::prefix(),$table);
    }
    
    /**
     * @param array $filters
     * @return array
     */
    private final function where(array $filters) {
        
        $where = array();

        foreach ($filters as $var => $val) {
            switch (TRUE) {
                case is_string($val):
                    $where[] = sprintf("`%s`='%s'", $var, $val);
                    break;
                case is_object($val):
                    $where[] = sprintf("`%s`='%s'", $var, $val->toString());
                    break;
                case is_array($val):
                    $where[] = sprintf("`%s` IN ('%s')", $var, implode("','", $val));
                    break;
                default:
                    $where[] = sprintf('`%s`=%s', $var, $val);
                    break;
            }
        }

        return $where;
    }

    /**
     * @param string $table
     * @param array $columns
     * @param array $filters
     * @param string $index
     * @return array
     */
    public final function select( $table, $columns = '*', array $filters = array() , $index = '' ) {
        
        $select = array();
        
        switch( TRUE ){
            case is_array($columns):
                if( count( $columns ) ){
                    $select[] = sprintf("SELECT %s"  , implode(',', $columns) );
                }
                else{
                    $select[] = "SELECT *";
                }
                break;
            case is_string($columns) && strlen($columns):
                $select[] = sprintf("SELECT %s"  , $columns );
                break;
            default:
                $select[] = "SELECT *";
                break;
        }
        
        $select[] = sprintf("FROM `%s`", self::table($table) );
        
        if (count($filters)) {
            $select[] .= "WHERE " . implode(' AND ', self::where($filters ) );
        }
        
        return $this->query( implode(' ', $select) , $index );
    }
    /**
     * @param string $table
     * @param array $data
     * @return int
     */
    public final function insert( $table , array $data ){
        
        $db = self::db();
        
        $columns = array_keys($data);

        $values = array();
        
        foreach( $data as $val ){
            if(is_array($val)){
                //listas
                $values[] = sprintf("'%s'",  implode(',', $val));
            }
            elseif(is_numeric($val)){
                //numerico
                $values[] = $val;
            }
            else{
                //texto
                $values[] = sprintf("'%s'",$val);
            }
        }
        
        $sql_insert = sprintf('INSERT INTO `%s` (%s) VALUES (%s)',
                self::table($table),
                implode(',', $columns),
                implode(',', $values));
        
        $result = $db->query($sql_insert);
        
        //var_dump($db->last_error);
        
        return FALSE !== $result ? $result : 0;
    }
    /**
     * @param string $table
     * @param array $data
     * @param array $filters
     * @return int
     */
    public final function update( $table , array $data , array $filters ){

        $db = self::db();

        $values = array();
        
        foreach( $data as $field => $content ){
            
            if(is_numeric( $content)){
                $value = $content;
            }
            elseif(is_array($content)){
                $value = implode(',',$content);
            }
            else{
                $value = sprintf("'%s'",$content);
            }
            
            $values[] .= sprintf("`%s`=%s",$field,$value);
        }
        
        $sql_update = sprintf( "UPDATE %s SET %s WHERE %s",
                self::table($table),
                implode(',', $values),
                $this->set_filters($filters));
        
        $result = $db->query($sql_update);
        
        return FALSE !== $result ? $result : 0;
    }
    /**
     * @param string $table
     * @param array $filters
     * @return int
     */
    public final function delete( $table, array $filters ){
               
        $db = self::db();

        $result = $db->delete(self::table($table), $filters);

        return FALSE !== $result ? $result : 0;
    }
    /**
     * @param string $SQL_QUERY
     * @param string $index
     * @return array
     */
    public final function query( $SQL_QUERY , $index = '' ){
        
        $db = self::db();

        $result = $db->get_results($SQL_QUERY, ARRAY_A );
        //var_dump($SQL_QUERY);
        
        if( strlen($index) ){
            $output = array();
            foreach( $result as $row ){
                if( isset( $row[$index])){
                    $output[ $row[ $index ] ] = $row;
                }
            }
            return $output;
        }

        return ( count($result)) ? $result : array();
    }
}


