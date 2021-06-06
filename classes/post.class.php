<?php namespace CODERS\ArtPad;
/**
 * Requires \CODERS\ArtPad\Model 
 */
final class Post extends Model{
    
    //1024 * 1024
    const DEFAULT_CHUNK_SIZE = 1048576;

    /**
     * @param array $data
     */
    private final function __construct( array $data ) {
        
        $this->define('ID',parent::TYPE_NUMBER,array('value'=>0))
                ->define('public_id',parent::TYPE_TEXT,array('size'=>32))
                ->define('name',parent::TYPE_TEXT)
                ->define('type',parent::TYPE_TEXT)
                ->define('title',parent::TYPE_TEXT)
                ->define('order',parent::TYPE_NUMBER)
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
     * @return string
     */
    public final function getLink(){
        return self::link( $this->public_id );
    }
    /**
     * @param string $rid
     * @return string
     */
    public static final function link( $rid ){
        return sprintf('%s/%s/rid-%s',
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
    public final function meta(){ return $this->listValues(); }
    /**
     * Can be embedded in the webview?
     * @return boolean
     */
    public final function embeddable(){
        switch( $this->value('type')){
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
        
        $deleted = $db->delete('post', array('ID'=>$this->value('ID')));

        if( $deleted ){
            
            $db->update('post', array('parent_id'=>0), array('parent_id'=>$this->value('ID')));
            
            return self::remove($this->value('public_id'));
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
    private static final function register( \CODERS\ArtPad\Post $item ){
        
        $db = self::newQuery();
        $inserted = $db->insert('post', $item->listValues());
        return $inserted !== FALSE && $inserted > 0;

    }
    /**
     * @param int $parent_id
     * @return int
     */
    private static final function slots( $parent_id = 0 ){
        
        $db = self::newQuery();
        
        $slots = $db->query("SELECT COUNT(*) AS slots FROM `%s` WHERE `parent_id`='%s'",Query::table('post'),$parent_id);
        
        return count( $slots ) ? intval( $slots['slots'] ) : 0;
    }
    /**
     * @param string $parent_id
     * @param boolean $public_key (FALSE)
     * @return array
     */
    public static final function collection( $parent_id = 0 , $public_key = FALSE ){
        
        $db = self::newQuery();
        
        if( $public_key && strlen($parent_id) ){
            $table = \CODERS\ArtPad\Query::table('post');
            $sql = sprintf("SELECT * FROM `%s` WHERE `parent_id` IN (SELECT `ID` FROM `%s` WHERE `public_id`='%s')",
                    $table,$table,$parent_id);
            return $db->query($sql,'public_id');
        }
        else{
            return $db->select('post','*',array('parent_id'=>$parent_id), 'date_created' , 'ID' );
        }
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
     * Use listPath instead
     * @deprecated since version 1
     * @return type
     */
    public final function listTree(){
        return $this->listRoute();
    }
    /**
     * @return array
     */
    public final function listRoute(){

        $id = $this->ID;
        $title = $this->getTitle();
        $parent_id = $this->parent_id;
        $output = array( $id => $title );

        if( $parent_id > 0 ){
            $parent = self::load($parent_id);
            if( FALSE !== $parent ){
                return array_reverse( $parent->listRoute() + $output , TRUE );
            }
        }
        return $output;
    }
    /**
     * @return array
     */
    public final function listChildren(){
        return self::collection($this->ID);
    }
    /**
     * @return array
     */
    public static final function listTypes(){
        return array();
    }
    /**
     * @return array
     */
    public static final function listTiers(){
        
        return self::newQuery()->select('tier',array('tier_id','title'),array(),'level','tier_id');
    }
    /**
     * @return boolean
     */
    public final function isImage(){
        switch( $this->type ){
            case 'image/gif':
            case 'image/png':
            case 'image/jpeg':
            case 'image/bmp':
                return true;
        }
        return false;
    }
    /**
     * @return boolean
     */
    public final function isText(){
        return !$this->isImage();
    }
    /**
     * @return boolean
     */
    public final function isValid(){
        return $this->ID > 0;
    }
    /**
     * 
     * @param array $data
     * @param string $buffer
     * @return boolean|\CODERS\ArtPad\Post
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
                case !array_key_exists('parent_id', $data):
                    $data['parent_id'] = 0;
                    break;
            }
            
            $data['public_id'] = self::GenerateID( $data['name'] );
            $data['slots'] = self::slots($data['parent_id']) + 1;
            $item = new Post( $data );
            
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
     * @param boolean $validate Require login
     * @return \CODERS\ArtPad\Post
     */
    public static final function load( $id , $public = FALSE , $validate = FALSE ){

        if( $validate && !self::checkAccess() ){
            //throw non validated output?
            return FALSE;
        }

        $filters = $public ?
                array( 'public_id' => $id ) : 
                array( 'ID' => $id );
        
        $data = self::query( $filters );

        return ( count($data)) ? new Post( $data[0] ) : FALSE;
    }
    /**
     * @return boolean
     */
    private static final function checkAccess(){

        if( Request::UID() && current_user_can('administrator') ){
            //return is administrator
            return TRUE;
        }
        
        $sid = Request::SID();
        
        if( FALSE !== $sid){
            $db = new Query();
            $session = $db->select('token', '*', array('ID'=>$sid,'type'=>'session','status'=>1));
            if( count( $session )){
                return TRUE;
            }
        }
        
        return FALSE;
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

/**
 * @todo Post Initializer
 */
add_action('init', function() {

    $post_labels = array(
        'name' => _x('Repository', 'Repo', 'coders_artpad'),
        'singular_name' => _x('Post', 'Post type singular name', 'coders_artpad'),
        'menu_name' => _x('Repository', 'Admin Menu text', 'coders_artpad'),
        'name_admin_bar' => _x('Post', 'Add New on Toolbar', 'coders_artpad'),
        'add_new' => __('Create', 'coders_artpad'),
        'add_new_item' => __('Add New Post', 'coders_artpad'),
        'new_item' => __('New Post', 'coders_artpad'),
        'edit_item' => __('Edit Post', 'coders_artpad'),
        'view_item' => __('View Post', 'coders_artpad'),
        'all_items' => __('Repository', 'coders_artpad'),
        'search_items' => __('Search Post', 'coders_artpad'),
        'parent_item_colon' => __('Parent Post:', 'coders_artpad'),
        'not_found' => __('No resources found.', 'coders_artpad'),
        'not_found_in_trash' => __('No resources found in Trash.', 'coders_artpad'),
        'featured_image' => _x('Post Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'coders_artpad'),
        'set_featured_image' => _x('Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'coders_artpad'),
        'remove_featured_image' => _x('Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'coders_artpad'),
        'use_featured_image' => _x('Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'coders_artpad'),
        'archives' => _x('Post archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'coders_artpad'),
        'insert_into_item' => _x('Insert into Post', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'coders_artpad'),
        'uploaded_to_this_item' => _x('Uploaded to this Post', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'coders_artpad'),
        'filter_items_list' => _x('Filter Posts', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'coders_artpad'),
        'items_list_navigation' => _x('Repository Navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'coders_artpad'),
        'items_list' => _x('Post List', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'coders_artpad'),
    );

    $project_labels = array(
        'name' => _x('Projects', 'Projects', 'coders_artpad'),
        'singular_name' => _x('Project', 'Project', 'coders_artpad'),
        'menu_name' => _x('Project', 'Project', 'coders_artpad'),
        'name_admin_bar' => _x('Project', 'New Project', 'coders_artpad'),
        'add_new' => __('Create', 'coders_artpad'),
        'add_new_item' => __('Add New Project', 'coders_artpad'),
        'new_item' => __('New Project', 'coders_artpad'),
        'edit_item' => __('Edit Project', 'coders_artpad'),
        'view_item' => __('View Project', 'coders_artpad'),
        'all_items' => __('Projects', 'coders_artpad'),
        'search_items' => __('Search Project', 'coders_artpad'),
        'parent_item_colon' => __('Parent Project:', 'coders_artpad'),
        'not_found' => __('No projects  found.', 'coders_artpad'),
        'not_found_in_trash' => __('No projects found in Trash.', 'coders_artpad'),
        'featured_image' => _x('Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'coders_artpad'),
        'set_featured_image' => _x('Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'coders_artpad'),
        'remove_featured_image' => _x('Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'coders_artpad'),
        'use_featured_image' => _x('Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'coders_artpad'),
        'archives' => _x('Archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'coders_artpad'),
        'insert_into_item' => _x('Insert into Project', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'coders_artpad'),
        'uploaded_to_this_item' => _x('Uploaded to this Project', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'coders_artpad'),
        'filter_items_list' => _x('Filter Projects', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'coders_artpad'),
        'items_list_navigation' => _x('Projects Navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'coders_artpad'),
        'items_list' => _x('Project List', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'coders_artpad'),
    );

    $coderepo_post = array(
        'labels' => $post_labels,
        'public' => FALSE,
        'publicly_queryable' => FALSE,
        'show_ui' => FALSE,
        'show_in_menu' => FALSE,
        'query_var' => FALSE,
        'rewrite' => array('slug' => 'coderepo-post'),
        'capability_type' => 'post',
        'has_archive' => FALSE,
        'hierarchical' => TRUE,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'author', 'excerpt', 'comments'),
    );

    $coderepo_project = array(
        'labels' => $project_labels,
        'public' => FALSE,
        'publicly_queryable' => FALSE,
        'show_ui' => FALSE,
        'show_in_menu' => FALSE,
        'query_var' => FALSE,
        'rewrite' => array('slug' => 'coderepo-project'),
        'capability_type' => 'post',
        'has_archive' => FALSE,
        'hierarchical' => FALSE,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
    );

    register_post_type('coderepo_post', $coderepo_post);
    register_post_type('coderepo_project', $coderepo_project);
});



