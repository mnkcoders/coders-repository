<?php namespace CODERS\Repository;
/**
 * 
 */
final class Resource{
    
    private $_meta = array(
        'ID'=>NULL,
        'public_id'=>NULL,
        'name'=>NULL,
        'type'=>NULL,
        'storage'=>NULL,
        'date_created'=>NULL,
        'date_updated'=>NULL,
    );
    //private $_ID,$_public_id,$_name,$_type,$_storage,$_date_created,$_date_updated = NULL;
    /**
     * @param array $meta
     */
    private final function __construct( array $meta ) {
        
        
        $this->populate($meta);
    }
    /**
     * 
     * @global wpdb $wpdb
     * @global string $table_prefix
     * @param array $filters
     * @return array
     */
    private static final function query( array $filters  = array() ){

        global $wpdb;
        
        global $table_prefix;
        
        $where = array();
        
        foreach( $filters as $var => $val ){
            switch( TRUE ){
                case is_string($val):
                    $where[] = sprintf("`%s`='%s'",$var,$val);
                    break;
                case is_object($val):
                    $where[] = sprintf("`%s`='%s'",$var,$val->toString());
                    break;
                case is_array($val):
                    $where[] = sprintf("`%s` IN ('%s')",$var, implode("','", $val));
                    break;
                default:
                    $where[] = sprintf('`%s`=%s',$var,$val);
                    break;
            }
        }
        
        $query = sprintf("SELECT * FROM `%scoders_repository`",$table_prefix);
        
        if( count($where)){
            $query .= " WHERE " . implode(' AND ', $where);
        }
        
        $result = $wpdb->get_results($query,ARRAY_A);

        return ( count($result)) ? $result : array();
     }
    /**
     * @param array $input
     * @return \CODERS\Repository\Resource
     */
    private final function populate( array $input ) {
        
        $ts = date('Y-m-d H:i:s');
        
        $this->_meta['date_created'] = $ts;
        $this->_meta['date_updated'] = $ts;
        
        //var_dump($this->_meta);
        
        foreach($input as $var => $val ){
            if(array_key_exists( $var, $this->_meta)){
                switch($var){
                    case 'ID':
                        $this->_meta[$var] = intval($val);
                        break;
                    default:
                        $this->_meta[$var] = $val;
                        break;
                }
                
            }
        }

        return $this;
    }
    /**
     * @return string
     */
    private final function path(){
        return sprintf('%s/%s/%s', \CodersRepo::base(),$this->storage,$this->public_id);
    }
    /**
     * 
     * @return boolean
     */
    public final function exists(){
        return file_exists($this->path());
    }
    /**
     * @return string|Boolean
     */
    public final function load(){
        
        return $this->exists() ? file_get_contents($this->path()) : FALSE;
    }
    /**
     * @param string $name
     * @return Mixed
     */
    function __get($name) {
        
        return isset($this->_meta[$name]) ? strval( $this->_meta[$name] ) : '';
    }
    /**
     * @return boolean
     */
    public final function delete(){
        
        return self::remove($this->_ID);
    }
    
    public static final function remove( $id ){
        
        return TRUE;
    }
    
    public static final function upload( ){
        
    }
    /**
     * @return array
     */
    public static final function find( ){
        
        return array();
    }
    /**
     * @param string $collection
     * @return array
     */
    public static final function collection( $collection ){
        
        return self::query( array( 'storage' => $collection ) );
    }
    /**
     * @param string $public_id
     * @return \CodersRepoSource
     */
    public static final function import( $public_id ){
        
        $result = self::query(array('public_id'=>$public_id));

        return ( count($result)) ? new Resource( $result[0] ) : FALSE;
    }
}