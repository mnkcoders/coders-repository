<?php namespace CODERS\ArtPad;
/**
 * 
 * \CODERS\ArtPad\Model Abstract Class
 * 
 */
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
            $this->__populate($data);
        }
    }
    /**
     * @return string
     */
    public function __toString() {
        return $this->__class();
    }
    /**
     * Model Class Name
     * @return string
     */
    protected final function __class(){
        
        $ns = explode('\\', get_class($this));
        
        return $ns[ count($ns) - 1 ];
    }
    /**
     * @param string $name
     * @return mixed
     */
    public final function __get($name) {
        
        switch( TRUE ){
            case preg_match(  '/^updated_/' , $name ):
                //RETURN ATTRIBUTE IS UPDATED (BOOLEAN)
                $updated = substr($name, strlen('updated_'));
                return $this->get($updated, 'updated',FALSE);
            case preg_match(  '/^type_/' , $name ):
                //RETURN ATTRIBUTE TYPE
                $type = substr($name, strlen('type_'));
                return $this->type($type);
            case preg_match(  '/^label_/' , $name ):
                //RETURN ATTRIBUTE LABEL
                $label = substr($name, strlen('label_'));
                return $this->get($label, 'label', $label );
            case preg_match(  '/^error_/' , $name ):
                //RETURN ATTRIBUTE ERROR
                $element = substr($name, strlen('error_'));
                return $this->get($element, 'error', '');
            case preg_match(  '/^count_/' , $name ):
            case preg_match(  '/^is_/' , $name ):
            case preg_match(  '/^list_/' , $name ):
                //RETURN CALLABLE FALLBACK WITH NO PARAMETERS
                return $this->$name();
            default:
                //RETURN RAW ATTRIBUTE VALUE
                $get = sprintf('get%s',preg_replace('/_/', '', $name));
                return method_exists($this, $get) ?
                    $this->$get( /*empty argument call*/) :
                    $this->get($name,'value','');
        }
    }
    /**
     * @param string $name
     * @param mixed $arguments
     * @return mixed
     */
    public final function __call($name, $arguments) {
        switch( TRUE ){
            case preg_match(  '/^count_/' , $name ):
                $count = preg_replace('/_/', '', $name);
                return method_exists($this, $count) ? $this->$count( $arguments ) : array();
            case preg_match(  '/^is_/' , $name ):
                //RETURN BOOLEAN
                $is = preg_replace('/_/', '', $name);
                return method_exists($this, $is) ? $this->$is( $arguments ) : FALSE;
            case preg_match(  '/^list_/' , $name ):
                //RETURN LIST
                $list = preg_replace('/_/', '', $name);
                return method_exists($this, $list) ? $this->$list( $arguments ) : array();
            case preg_match(  '/^label_/' , $name ):
                //RETURN LIST
                $label = preg_replace('/_/', '', $name);
                return method_exists($this, $label) ? $this->$label( $arguments ) : $label;
            default:
                //RETURN ATTRIBUTE VALUE
                $get = sprintf('get%s',preg_replace('/_/', '', $name));
                return method_exists($this, $get) ? $this->$get( $arguments ) : $this->get($name,'value','');
        }
    }
    /**
     * @param string $name
     * @param mixed $value
     */
    public final function __set($name, $value) {
        if( $this->has($name)){
            $this->setValue($name, $value);
        }
    }
    /**
     * @param boolean $noformat
     * @return string
     */
    protected static function __ts( $noformat = FALSE ){
        return $noformat ? date('YmdHis')  : date('Y-m-d H:i:s');
    }
    /**
     * @param string $tag
     * @return String
     */
    protected static function __makeID( $tag = '' ){
        return md5( uniqid( strlen($tag) ? $tag : self::__ts(true), TRUE) );
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
    protected function __populate( array $input ){
        foreach( $input as $element => $value ){
            $this->setValue($element, $value);
        }
        return $this;
    }
    /**
     * Combine elements from
     * @param array $data Import data from
     * @return \CODERS\ArtPad\Model
     */
    public function import( array $data ){
        
        foreach( $data as $element => $value ){
            if( $this->has($element)){
                $this->setValue($element,$value);
            }
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
        elseif( $this->has($element)){
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
    public final function elements(){ return $this->listElements(); }
    /**
     * @return array
     */
    public final function listElements(){ return array_keys($this->_dictionary); }
    /**
     * @return array
     */
    public final function listValues(){
        $output = array();
        foreach( $this->elements() as $element ){
            $output[$element] = $this->value( $element );
        }
        return $output;
    }
    /**
     * @return array
     */
    public static function listTypes(){
        return array(
            self::TYPE_CHECKBOX => __('Checkbox','coders_artpad'),
            self::TYPE_CURRENCY => __('Currency','coders_artpad'),
            self::TYPE_DATE => __('Date','coders_artpad'),
            self::TYPE_DATETIME => __('DateTime','coders_artpad'),
            self::TYPE_EMAIL => __('Email','coders_artpad'),
            self::TYPE_FLOAT => __('Float','coders_artpad'),
            self::TYPE_NUMBER => __('Number','coders_artpad'),
            self::TYPE_TEXT => __('Text','coders_artpad'),
            self::TYPE_TEXTAREA => __('Text Area','coders_artpad'),
        );
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
    public static final function Instance( $request , $data = array() ){
        
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
 * \CODERS\ArtPad\Query Class
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
        foreach ($filters as $var => $val ) {
            switch (TRUE) {
                //LIST
                case is_array($val):
                    $where[] = sprintf("`%s` IN ('%s')", $var, implode("','", $val));
                    break;
                // LIKE CLAUSE
                case preg_match('/%/', $val):
                    $where[] = sprintf("`%s` LIKE '%s'",$var,$val);
                    break;
                //STRING
                case is_string($val):
                    $where[] = sprintf("`%s`='%s'", $var, $val);
                    break;
                case is_object($val):
                    $where[] = sprintf("`%s`='%s'", $var, $val->toString());
                    break;
                default:
                    //SIMPLE TYPE
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
     * @param array|string $order_by
     * @param string $index
     * @return array
     */
    public final function select( $table, $columns = '*', array $filters = array() , $order_by = array() , $index = '' ) {
        
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
            $select[] = "WHERE " . implode(' AND ', self::where($filters ) );
        }
        if( !is_array( $order_by )){
            $order_by = array( $order_by );
        }
        if( count( $order_by ) ){
            $select[] = sprintf('ORDER BY (%s)', implode(',', $order_by) );
        }

        return $this->query( implode(' ', $select) , $index );
    }
    /**
     * @param string $table
     * @param array $data
     * @param boolean $return_id
     * @return int
     */
    public final function insert( $table , array $data , $return_id = FALSE ){
        
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
        
        if(  $db->rows_affected ){
            $id = $return_id ? $db->insert_id : 1;
            return $id;
        }
        
        return 0;
        
        //var_dump($db->last_error);
        
        //return FALSE !== $result ? $result : 0;
        return FALSE !== $result ? $id : 0;
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
        
        $sql_update = sprintf( "UPDATE `%s` SET %s WHERE %s",
                self::table($table),
                implode(',', $values),
                implode(' AND ', self::where( $filters )));

        $db->query($sql_update);
        
        return $db->rows_affected;
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


