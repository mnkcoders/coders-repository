<?php namespace CODERS\Repository;
/**
 * 
 */
final class Resource{
    
    private $_meta = array(
        'ID'=>0,
        'public_id'=>'',
        'name'=>'',
        'type'=>'',
        'storage'=>'default',
        'date_created'=>NULL,
        'date_updated'=>NULL,
    );
    //private $_ID,$_public_id,$_name,$_type,$_storage,$_date_created,$_date_updated = NULL;
    /**
     * @param array $meta
     * @param string $buffer
     */
    private final function __construct( array $meta ) {
        
        
        $this->populate($meta);
    }
    /**
     * @param string $name
     * @return Mixed
     */
    function __get($name) {
        
        return isset($this->_meta[$name]) ? strval( $this->_meta[$name] ) : '';
    }
    /**
     * @param string $collection
     * @return string
     */
    private static final function GenerateID( $collection = '' ){
        return md5(uniqid(date('YmdHis').$collection,true));
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
     * @param string|stream $buffer
     * @return int|boolean
     */
    private final function write( $buffer ){
        
        return file_put_contents($this->path(), $buffer);
    }
    /**
     * @return string|Boolean
     */
    public final function read(){
        
        return $this->exists() ? file_get_contents($this->path()) : FALSE;
    }
    /**
     * @global \wpdb $wpdb
     * @global string $table_prefix
     * @return boolean
     */
    private static final function register( \CODERS\Repository\Resource $R ){
        
        global $wpdb,$table_prefix;
        
        $inserted = $wpdb->insert(sprintf('%scoders_repository',$table_prefix),$R->_meta);
        
        return $inserted !== FALSE && $inserted > 0;
    }
    /**
     * @param string $collection
     * @return boolean
     */
    private static final function checkCollection( $collection ){
        
        $path = \CodersRepo::base($collection);
        
        return ( filetype( $path ) === 'dir' ) ? TRUE : mkdir($path);
    }
    /**
     * @return boolean
     */
    public final function delete(){
        
        return self::remove($this->_ID);
    }
    /**
     * @global wpdb $wpdb
     * @global string $table_prefix
     * @param string $id
     * @return boolean
     */
    public static final function remove( $id ){
        
        global $wpdb,$table_prefix;
        
        $deleted = $wpdb->delete(sprintf('%scoders_repository',$table_prefix), array( 'ID' => $id ) );
        
        return $deleted !== FALSE && $deleted > 0;
    }
    /**
     * @return array
     */
    public static final function find( $search ){
        
        $filters = is_array($search) ? $search : array('ID'=>$search);
        
        return $this->query($filters);
    }
    /**
     * @param string $collection
     * @return array
     */
    public static final function collection( $collection ){
        
        return self::query( array( 'storage' => $collection ) );
    }
    /**
     * @return array
     */
    public static final function storage(){
        
        $output = array();
        $root = \CodersRepo::base();
        //var_dump(self::base());
        //var_dump(scandir(self::base()));
        foreach(scandir($root) as $item ){
            if( is_dir($root . '/' . $item ) && $item !== '.' && $item !== '..' ){
                $output[] = $item;
            }
        }
        
        return $output;
    }
    /**
     * 
     * @param array $meta
     * @param string $buffer
     * @return boolean|\CODERS\Repository\Resource
     * @throws \Exception
     */
    public static final function create( array $meta , $buffer = '' ){
        
        try{
            switch( TRUE ){
                case !array_key_exists('name', $meta):
                    throw new \Exception('EMPTY_NAME_ERROR');
                    //break;
                case !array_key_exists('type', $meta):
                    throw new \Exception('EMPTY_FILETYPE_ERROR');
                    //break;
                case !array_key_exists('storage', $meta):
                    $meta['storage'] = 'default';
                    break;
            }
            
            $meta['public_id'] = self::GenerateID( $meta['storage'] );
            
            $R = new Resource( $meta , $buffer );
            
            if(strlen($buffer) && !$R->exists( ) ){
                
                if( !self::checkCollection($meta['storage']) || !$R->write($buffer) ){
                   
                    return FALSE;
                }
            }
            
            return self::register($R) ? $R : FALSE;
        }
        catch (\Exception $ex) {
            print( $ex->getMessage() );
        }
        return FALSE;
    }
    /**
     * @param string $input
     * @param string $collection
     * @return boolean
     * @throws \Exception
     */
    public static final function upload( $input , $collection = 'default' ){
        
        try{
            $destination = \CodersRepo::base($collection);
            
            $fileMeta = array_key_exists($input, $_FILES) ? $_FILES[ $input ] : array();

            if( count($fileMeta) === 0 ){
                throw new \Exception('UPLOAD_ERROR_INVALID_FILE');
            }
            
            if( strlen($destination) === 0 ){
                throw new \Exception('UPLOAD_ERROR_INVALID_DESTINATION');
            }
            
            switch( $fileMeta['error'] ){
                case UPLOAD_ERR_CANT_WRITE:
                    throw new \Exception('UPLOAD_ERROR_READ_ONLY');
                case UPLOAD_ERR_EXTENSION:
                    throw new \Exception('UPLOAD_ERROR_INVALID_EXTENSION');
                case UPLOAD_ERR_FORM_SIZE:
                    throw new \Exception('UPLOAD_ERROR_SIZE_OVERFLOW');
                case UPLOAD_ERR_INI_SIZE:
                    throw new \Exception('UPLOAD_ERROR_CFG_OVERFLOW');
                case UPLOAD_ERR_NO_FILE:
                    throw new \Exception('UPLOAD_ERROR_NO_FILE');
                case UPLOAD_ERR_NO_TMP_DIR:
                    throw new \Exception('UPLOAD_ERROR_INVALID_TMP_DIR');
                case UPLOAD_ERR_PARTIAL:
                    throw new \Exception('UPLOAD_ERROR_INCOMPLETE');
                case UPLOAD_ERR_OK:
                    break;
            }
            
            $buffer = file_get_contents($fileMeta['tmp_name']);
            
            unlink($fileMeta['tmp_name']);
           
            if( $buffer !== FALSE ){

                return self::create($fileMeta , $buffer);
            }
        }
        catch (\Exception $ex) {
            print( $ex->getMessage() );
        }
        
        return FALSE;
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


