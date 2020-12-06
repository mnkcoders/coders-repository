<?php namespace CODERS\ArtPad;
/**
 * 
 */
final class Resource{
    
    //1024 * 1024
    const DEFAULT_CHUNK_SIZE = 1048576;
    
    private $_meta = array(
        'ID' => 0,
        'public_id' => '',
        'parent_id' => 0,
        'name'=>'',
        'type'=>'',
        'collection'=>'default',
        'title' => '',
        'tier_id' => '',
        'content' => '',
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
     * @return \CODERS\ArtPad\Query
     */
    private static final function query( array $filters ){
        
        $db = new Query();
        
        return $db->select('post', '*', $filters );
    }
    /**
     * @param string $collection
     * @return string
     */
    private static final function GenerateID( $collection = '' ){
        return md5( uniqid( date( 'YmdHis' ) . $collection , true ) );
    }
    /**
     * @param array $input
     * @return \CODERS\ArtPad\Resource
     */
    private final function populate( array $input ) {
        
        $ts = date('Y-m-d H:i:s');
        
        $this->_meta['date_created'] = $ts;
        $this->_meta['date_updated'] = $ts;
        
        foreach($input as $var => $val ){
            if(array_key_exists( $var, $this->_meta)){
                switch($var){
                    case 'parent_id':
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
    public final function path(){
        return \ArtPad::Storage( $this->public_id );
    }
    /**
     * @return array
     */
    public final function tree(){
        
        $id = $this->_meta['ID'];
        $name = strlen( $this->_meta['title'] ) ? $this->_meta['title'] : $this->_meta['name'];
        
        $output = array( $id => $name );
        
        if( $this->_meta['parent_id'] > 0 ){
            $parent = self::load($this->_meta['parent_id']);
            if( FALSE !== $parent ){
                return array_reverse( $parent->tree() + $output , TRUE );
            }
        }
        
        return $output;
    }
    /**
     * @param string $rid
     * @return string
     */
    public static final function link( $rid ){

        return sprintf('%s?%s=rid.%s',
                get_site_url(),
                \ArtPad::ENDPOINT,
                $rid);
        
        //return sprintf('%s?%s=%s',
        //        get_site_url(),
        //        \ArtPad::RESOURCE,
        //        $rid);
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
    public final function read( ){
        
        return $this->exists() ? file_get_contents($this->path()) : FALSE;
    }
    /**
     * @return string
     */
    public final function readEncoded(){

        return base64_encode( $this->read( ) );
    }
    /**
     * Array of headers required to stream this file
     * @param boolean $attach
     * @return array
     */
    public final function headers( $attach = FALSE ){
        
        $header = array(
            sprintf('Content-Type: %s' , $this->type ),
            sprintf( 'Content-Disposition: %s; filename="%s"',
                    //mark as attachment if cannot be embedded or not required as download
                    $attach || !$this->embeddable() ? 'attachment' : 'inline',
                    $this->name ),
            sprintf( 'Content-Length: %s', $this->size() ),
            //sprintf( 'Cache-Control : %s, max-age=%s;', 'private' , 3600 )
            //'Cache-Control : public, max-age=3600;',
        );
        
        return $header;
    }
    /**
     * Stream out the file content in a buffered loop
     * 
     * @param boolean $attach
     * @param int $chunk_size default to 1MB (1024 * 1024)
     * @return boolean
     */
    public final function stream( $attach = FALSE , $chunk_size = self::DEFAULT_CHUNK_SIZE ){
        
        if( !$this->exists()){
            return FALSE;
        }
        
        $path = $this->path();
        
        $buffer = '';
        $cnt = 0;
        $handle = fopen( $path , 'rb' );

        if( $handle === FALSE ){
            return FALSE;
        }
        
        $headers = $this->headers($attach);
        foreach ( $headers as $header) {
            header($header);
        }

        while( !feof($handle)){
            $buffer = fread($handle, $chunk_size );
            ob_flush();
            flush();
            print $buffer;
            $cnt += strlen($buffer);
        }
        
        $status = fclose($handle);
        
        return $status ? $cnt : FALSE;
    }
    /**
     * @return int
     */
    public final function size(){
        return $this->exists() ? filesize($this->path()) : 0;
    }
    /**
     * @return array
     */
    public final function meta(){ return $this->_meta; }
    /**
     * Can be embedded in the webview?
     * @return boolean
     */
    public final function embeddable(){
        switch( $this->_meta['type']){
            //images and media
            case 'image/jpg':
            case 'image/jpeg':
            case 'image/png':
            case 'image/gif':
            case 'image/bmp':
            //text files
            case 'text/plain':
            case 'text/html':
                return TRUE;
        }
        return FALSE;
    }
    /**
     * @global \wpdb $wpdb
     * @global string $table_prefix
     * @return boolean
     */
    private static final function register( \CODERS\ArtPad\Resource $R ){
        
        $db = new Query();
        
        $inserted = $db->insert('post', $R->meta());
        
        //global $wpdb,$table_prefix;
        
        //$inserted = $wpdb->insert(sprintf('%scoders_post',$table_prefix),$R->_meta);
        
        //$wpdb->show_errors();
        
        return $inserted !== FALSE && $inserted > 0;
    }
    /**
     * @param string $collection
     * @return boolean
     */
    public static final function createCollection( $collection ){
        
        $path = \ArtPad::Storage($collection);
        
        return ( file_exists( $path ) ) ? TRUE : mkdir($path);
    }
    /**
     * @return boolean
     */
    public final function delete(){
        
        $db = new Query();
        
        $deleted = $db->delete('post', array('ID'=>$this->_meta['ID']));

        if( $deleted ){
            
            $db->update('post', array('parent_id'=>0), array('parent_id'=>$this->_meta['ID']));
            
            return self::remove($this->_meta['public_id']);
        }

        return FALSE;
    }
    /**
     * @param int $parent_id
     * @return boolean
     */
    public final function setParent( $parent_id = 0 ){
        if( $parent_id !== $this->_meta['ID']){
            $db = new Query();
            $result = $db->update('post',
                    array('parent_id'=> $parent_id),
                    array('ID'=>$this->_meta['ID']));
            if( $result > 0 ){
                $this->_meta['parent_id'] = $parent_id;
                return TRUE;
            }
        }
        return FALSE;
    }
    /**
     * @global wpdb $wpdb
     * @global string $table_prefix
     * @param string $public_id
     * @return boolean
     */
    public static final function remove( $public_id ){
        $path = \ArtPad::Storage($public_id);
        if (file_exists($path)) {
            return unlink($path);
        }
        return FALSE;
    }
    /**
     * @return array
     */
    public static final function find( $search ){
        
        $filters = is_array($search) ? $search : array('ID'=>$search);
        
        return self::query($filters);
    }
    /**
     * @param string $id
     * @return array
     */
    public static final function collection( $id = 0 ){
        
        $db = new \CODERS\ArtPad\Query();
        
        return $db->select('post','*',array('parent_id'=>$id),'ID' );
    }
    /**
     * @return array
     */
    public static final function storage(){
        
        $output = array();
        $root = \ArtPad::Storage();
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
     * @return boolean|\CODERS\ArtPad\Resource
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
                case !array_key_exists('collection', $meta):
                    $meta['collection'] = 'default';
                    break;
            }
            
            $meta['public_id'] = self::GenerateID( $meta['name'] );
                        
            $R = new Resource( $meta );
            
            if(strlen($buffer) && !$R->exists( ) ){
                
            }
            //if( !self::createCollection($meta['collection'])){
            //    throw new \Exception('Cannot create a new collection. Check file permissions.' );
            //}
            if( $R->exists()){
                throw new \Exception(sprintf('File ID:%s already exists.',$meta['public_id']));
            }
            if( !$R->write($buffer)){
                throw new \Exception(sprintf('Cannot write file %s. Check file permissions.',$meta['name']));
            }
            if( self::register($R)){
                return $R;
            }
            else{
                throw new \Exception('Cannot register new resource in database');
            }
        }
        catch (\Exception $ex) {
            print( $ex->getMessage() );
        }
        return FALSE;
    }
    /**
     * @param string $input
     * @return array
     */
    private static final function parseUploadMeta( $input ){
            
            $upload = array_key_exists($input, $_FILES) ? $_FILES[ $input ] : array();

            $list = array();

            if( count($upload) ){
                
                if(is_array($upload['name'])){
                    for( $i = 0 ; $i < count($upload['name']) ; $i++ ){
                        $list[] = array(
                            'name' => $upload['name'][$i],
                            'tmp_name' => $upload['tmp_name'][$i],
                            'type' => $upload['type'][$i],
                            'error' => $upload['error'][$i],
                        );
                    }
                }
                else{
                    $list[] = $upload;
                }
            }
            
            return $list;
    }
    /**
     * @param string $input
     * @param int $parent_id
     * @return array
     */
    public static final function upload( $input , $parent_id = 0 ){
        
        $created = array();
        
        foreach( self::parseUploadMeta($input) as $upload ) {
            try{
                switch( $upload['error'] ){
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

                $buffer = file_get_contents($upload['tmp_name']);

                unlink($upload['tmp_name']);

                if( $buffer !== FALSE ){
                    $upload['parent_id'] = $parent_id;
                    $resource = self::create($upload , $buffer );
                    if( $resource !== FALSE ){
                        $created[ $resource->ID ] = $resource;
                    }
                }
            }
            catch (\Exception $ex) {
                //send notification
                print( $ex->getMessage() );
            }
        }
        
        return $created;
    }
    /**
     * @param int $ID
     * @return \CODERS\ArtPad\Resource
     */
    public static final function load( $ID ){

        $result = self::query( array('ID'=>$ID) );

        return ( count($result)) ? new Resource( $result[0] ) : FALSE;
    }
    /**
     * @param string $public_id
     * @return \CODERS\ArtPad\Resource
     */
    public static final function import( $public_id ){
        
        $result = self::query( array('public_id' => $public_id ) );

        return ( count($result)) ? new Resource( $result[0] ) : FALSE;
    }
}


