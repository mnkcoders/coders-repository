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
     * 
     */
    public final function download(){
        
        if($this->exists()){
            header('Content-Type:' . $this->type );

            print $this->load();
        }
        else{
            print $this->path();
        }
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
     * @param string $public_id
     * @return \CodersRepoSource
     */
    public static final function import( $public_id ){
        
        global $wpdb;
        
        global $table_prefix;

        $result = $wpdb->get_results(sprintf("SELECT * FROM `%scoders_repository` WHERE `public_id`='%s'",$table_prefix,$public_id),ARRAY_A);

        return ( count($result)) ? new Resource( $result[0] ) : FALSE;
    }
}