<?php namespace CODERS\ArtPad;
/**
 * Requires \CODERS\ArtPad\Model 
 */
final class Item extends Model{
    
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
    /**
     * @param array $data
     */
    private final function __construct( array $data ) {
        
        $this->define('ID',parent::TYPE_NUMBER,array('value'=>0))
                ->define('public_id',parent::TYPE_TEXT,array('size'=>32))
                ->define('name',parent::TYPE_TEXT)
                ->define('type',parent::TYPE_TEXT)
                ->define('title',parent::TYPE_TEXT)
                ->define('tier_id',parent::TYPE_TEXT)
                ->define('content',parent::TYPE_TEXTAREA)
                ->define('date_created',parent::TYPE_DATETIME)
                ->define('date_updated',parent::TYPE_DATETIME);
        
        //$this->populate($data);
        parent::__construct($data);
    }
    /**
     * @return \CODERS\ArtPad\Query
     */
    private static final function query( array $filters ){
        
        $db = self::newQuery();
        
        return $db->select('post', '*', $filters );
    }
    /**
     * @param string $id
     * @return string
     */
    private static final function GenerateID( $id = 0 ){
        return md5( uniqid( date( 'YmdHis' ) . $id , true ) );
    }
    /**
     * @return array
     */
    public final function listTree(){

        $id = $this->ID;
        $title = $this->getTitle();
        $parent_id = $this->parent_id;
        $output = array( $id => $title );

        if( $parent_id > 0 ){
            $parent = self::load($parent_id);
            if( FALSE !== $parent ){
                return array_reverse( $parent->listTree() + $output , TRUE );
            }
        }
        return $output;
    }
    /**
     * @return string
     */
    public final function getPath(){
        return \ArtPad::Storage( $this->public_id );
    }
    /**
     * @return string
     */
    public final function getTitle(){
        
        $title = $this->title;
        
        return strlen($title) ? $title : $this->name;
    }
    /**
     * @return string|URL
     */
    public final function getLink(){
        return self::link( $this->public_id );
    }
    /**
     * @param string $rid
     * @return string
     */
    public static final function link( $rid ){
        return sprintf('%s/%s/rid.%s',
                get_site_url(),
                \ArtPad::ENDPOINT,
                $rid);
    }
    /**
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
            sprintf( 'Content-Length: %s', $this->getSize() ),
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
    public final function getSize(){
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
     * @return boolean
     */
    public final function delete(){
        
        $db = self::newQuery();
        
        $deleted = $db->delete('post', array('ID'=>$this->_meta['ID']));

        if( $deleted ){
            
            $db->update('post', array('parent_id'=>0), array('parent_id'=>$this->_meta['ID']));
            
            return self::remove($this->_meta['public_id']);
        }

        return FALSE;
    }
    /**
     * @return boolean
     */
    public final function moveUp(){
        $parent_id = $this->value('parent_id');
        if( $parent_id > 0 ){
            $parent = self::load($parent_id);
            if( FALSE !== $parent ){
                return $this->setParent( $parent->parent_id );
            }
        }
        return FALSE;
    }
    /**
     * @param int $parent_id
     * @return boolean
     */
    public final function setParent( $parent_id = 0 ){
        if( $parent_id !== $this->ID ){
            $db = self::newQuery();
            $result = $db->update('post',
                    array('parent_id'=> $parent_id),
                    array('ID'=>$this->ID));
            if( $result > 0 ){
                $this->setValue('parent_id', $parent_id );
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
    private static final function remove( $public_id ){
        $path = \ArtPad::Storage($public_id);
        if (file_exists($path)) {
            return unlink($path);
        }
        return FALSE;
    }
    /**
     * @global \wpdb $wpdb
     * @global string $table_prefix
     * @return boolean
     */
    private static final function register( \CODERS\ArtPad\Item $item ){
        
        $db = self::newQuery();
        $inserted = $db->insert('post', $item->listValues());
        return $inserted !== FALSE && $inserted > 0;

    }
    /**
     * @param string $parent_id
     * @return array
     */
    public static final function collection( $parent_id = 0 ){
        
        $db = self::newQuery();
        
        return $db->select('post','*',array('parent_id'=>$parent_id),'ID' );
    }
    /**
     * @return array
     */
    public static final function listStorage(){
        
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
     * @param array $data
     * @param string $buffer
     * @return boolean|\CODERS\ArtPad\Resource
     * @throws \Exception
     */
    public static final function new( array $data , $buffer = '' ){
        
        try{
            switch( TRUE ){
                case !array_key_exists('name', $data):
                    throw new \Exception('EMPTY_NAME_ERROR');
                    //break;
                case !array_key_exists('type', $data):
                    throw new \Exception('EMPTY_FILETYPE_ERROR');
            }
            
            $data['public_id'] = self::GenerateID( $data['name'] );
                        
            $item = new Item( $data );
            
            if(strlen($buffer) && !$item->exists( ) ){
                //
            }
            if( $item->exists()){
                throw new \Exception(sprintf('File ID:%s already exists.',$data['public_id']));
            }
            if( !$item->write($buffer)){
                throw new \Exception(sprintf('Cannot write file %s. Check file permissions.',$data['name']));
            }
            if( self::register($item)){
                return $item;
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
    private static final function uploadMeta( $input ){
            
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
        
        $collection = array();
        
        foreach( self::uploadMeta($input) as $upload ) {
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
                    $item = self::new($upload , $buffer );
                    if( $item !== FALSE ){
                        $collection[ $item->ID ] = $item;
                    }
                }
            }
            catch (\Exception $ex) {
                //send notification
                print( $ex->getMessage() );
            }
        }
        
        return $collection;
    }
    /**
     * @param int|String $id
     * @param boolean $public use ID (default) or public_ID
     * @return \CODERS\ArtPad\Resource
     */
    public static final function load( $id , $public = FALSE ){

        $filters = $public ?
                array( 'public_id' => $id ) : 
                array( 'ID' => $id );
        
        $data = self::query( $filters );

        return ( count($data)) ? new Item( $data[0] ) : FALSE;
    }
    /**
     * Disabled
     * @param type $request
     * @param type $data
     * @return boolean
     */
    public static final function create($request, $data = array()) {
        return FALSE;
    }
}


