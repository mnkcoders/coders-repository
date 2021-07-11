<?php namespace CODERS\ArtPad;
/**
 * Requires \CODERS\ArtPad\Model 
 */
final class Resource extends Model{
    
    //1024 * 1024
    const DEFAULT_CHUNK_SIZE = 1048576;
    /**
     * @var WP_Post
     */
    private $_post = null;

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
                ->define('parent',parent::TYPE_NUMBER,array('value'=>0))
                //->define('storage',parent::TYPE_TEXT)
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
        return self::Storage( $this->public_id ); 
        //return \ArtPad::Storage( $this->public_id );
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
        return sprintf('%s/%s/%s-%s',
                get_site_url(),
                \ArtPad::ENDPOINT,
                \ArtPad::RESOURCE,
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
        $path = self::Storage( $public_id );
        //$path = \ArtPad::Storage($public_id);
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
    private static final function register( \CODERS\ArtPad\Resource $item ){
        
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
     * @param string $resource
     * @return string
     */
    public static final function Storage( $resource = '' ){
        
        $coders_dir = get_option( 'coders_repo_base' , \ArtPad::ENDPOINT );
        $path = preg_replace('/\\\\/', '/', ABSPATH)
                . '/wp-content/uploads/'
                . $coders_dir;
        
        if( strlen( $resource ) ){
            $path .= '/' . $resource;
        }
        
        return $path;
    }
    /**
     * @return array
     */
    public static final function List( $drive = '' ){
        
        $output = array();
        $root = self::Storage( $drive );
        //$root = \ArtPad::Storage();
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
                case !array_key_exists('parent_id', $data):
                    $data['parent_id'] = 0;
                    break;
            }
            
            $data['public_id'] = self::GenerateID( $data['name'] );
            $data['slots'] = self::slots($data['parent_id']) + 1;
            $item = new Resource( $data );
            
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
     * @return \CODERS\ArtPad\Resource
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

        return ( count($data)) ? new Resource( $data[0] ) : FALSE;
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
     * @param array $data post metadata
     * @param string $buffer attachment content data
     * @return \CODERS\ArtPad\Resource
     */
    public static final function create($data = array() , $buffer = '' ) {
        
        $author = 1;
        $status = 'publish';
        
        $data['ID'] = wp_insert_post(array(
            'post_type' => 'artpad_post',
            'post_title'    => wp_strip_all_tags( $data['title'] ),
            'post_content'  => $data['content'],
            'post_status'   => $status,
            'post_author'   => $author,
        ), TRUE, FALSE);
        
        if( $data['ID'] ){
            
            add_post_meta($data['ID'], 'public_id', $data['public_id'] );
            add_post_meta($data['ID'], 'order', $data['order'] );
            add_post_meta($data['ID'], 'tier_id', $data['tier_id'] );
            
            return self::new( $data , $buffer ); 
        }
        
        return FALSE;
    }
}



add_action('init', function() {

    register_post_type('artpad_post', array(
        'public' => FALSE,
        'publicly_queryable' => FALSE,
        'show_ui' => TRUE,
        'show_in_menu' => FALSE,
        'query_var' => TRUE,
        'rewrite' => array('slug' => 'artpad-post'),
        'capability_type' => 'post',
        'has_archive' => FALSE,
        'hierarchical' => TRUE,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'author', 'excerpt', 'comments', 'thumbnail','parent'),
        'labels' => array(
            'name' => _x('Collection', 'Collection', 'coders_artpad'),
            'singular_name' => _x('Posts', 'Post', 'coders_artpad'),
            'menu_name' => _x('Collection', 'Collection', 'coders_artpad'),
            'name_admin_bar' => _x('Item', 'Item', 'coders_artpad'),
            'add_new' => __('Add', 'coders_artpad'),
            'add_new_item' => __('Add', 'coders_artpad'),
            'new_item' => __('New', 'coders_artpad'),
            'edit_item' => __('Edit', 'coders_artpad'),
            'view_item' => __('View', 'coders_artpad'),
            'all_items' => __('Collection', 'coders_artpad'),
            'search_items' => __('Search', 'coders_artpad'),
            'parent_item_colon' => __('Parent', 'coders_artpad'),
            'not_found' => __('Empty', 'coders_artpad'),
            'not_found_in_trash' => __('Trash is empty', 'coders_artpad'),
            'featured_image' => _x('Cover', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'coders_artpad'),
            'set_featured_image' => _x('Set cover', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'coders_artpad'),
            'remove_featured_image' => _x('Remove cover', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'coders_artpad'),
            'use_featured_image' => _x('Use as cover', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'coders_artpad'),
            'archives' => _x('Archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'coders_artpad'),
            'insert_into_item' => _x('Insert', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'coders_artpad'),
            'uploaded_to_this_item' => _x('Uploaded to this Resource', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'coders_artpad'),
            'filter_items_list' => _x('Filter Resources', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'coders_artpad'),
            'items_list_navigation' => _x('Repository Navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'coders_artpad'),
            'items_list' => _x('Resource List', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'coders_artpad'),
        ),
    ));
    
    if(is_admin()){
        
        global $wp;
        $wp->add_query_var('post_parent');
        
        add_filter('page_row_actions',function( $actions , $post ){
                //check for your post type
                if ($post->post_type === 'artpad_post' ){
                   $url = sprintf('%s/edit.php?post_type=artpad_post&post_parent=%s',
                                    get_admin_url(), $post->ID);
                   $link = sprintf('<a href="%s" target="_self" class="children">%s</a>',
                            $url,
                            __('Children','coders_artpad'));
                   
                   $actions[ 'children' ] = $link;
                }
                return $actions;
        }, 10, 2);

        add_action('edit_form_top',function( $post ){
            if( $post->post_type === 'artpad_post' && $post->ID > 0 ){
                $parent_id = intval( $post->post_parent );
                $url = $parent_id > 0 ? 
                        sprintf('%spost.php?post=%s&action=edit', get_admin_url(),$parent_id ) :
                        sprintf('%sedit.php?post_type=artpad_post', get_admin_url());
                printf('<a class="button" href="%s" target="_self">%s</a>',
                       $url,
                        __('Parent','coders_artpad'));
            }
        });
        
        add_action( 'pre_get_posts' , function( $query ){
            //$query->set('post_parent',  2635 );
            //var_dump($query);
            
            return $query;
        });
        /**
         * @param WP_Post $post
         * @return array
         */
        function list_parent_posts( $post ){
            
            $list = array(
                0 => '<b>' . __('Root','coders_artpad') . '</b>',
            );
            
            $parent_id = get_post_parent($post) | 0;
            //parent post's parent or root
            if( $parent_id ){
                $parent = get_post($parent_id);
                $list[ $parent->ID ] = '<b>'. __('Move to upper','coders_artpad') .'</b>';
            }
            else{
                //$list[ 0 ] = __('Root','coders_artpad');
            }
            //adjacent posts
            $child_args = array(
                'post_parent' => $post->post_parent,
                'post_type' => 'artpad_post',
                'numberposts' => -1 );
            $adjacent = get_children($child_args);
            foreach( $adjacent as $p ){
                if( $p->ID != $post->ID ){
                    $list[ $p->ID ] = $p->post_title;
                }
            }
            
            return $list;
        }
        
        ///metaboxes
        add_action( 'add_meta_boxes' , function(  ){
            add_meta_box(
                    'artpad_post_parent',
                    __('Attributes','coders_artpad'),
                    function($post){
                
                        printf('<p><label class="bold widefat" for="post_name">%s</label></p>',
                                __('Post name','coders_artpad'));
                        printf('<p><input type="text" class="widefat" name="post_name" id="post_name" value="%s" /></p>',
                                $post->post_name);
                
                        $post_list = list_parent_posts($post);
                        printf('<p><label class="widefat" for="_parent_id">%s</label></p>',
                                __('Parent'));
                        printf('<p><select class="widefat" name="%s" id="%s">',
                                '_parent_id',
                                '_parent_id' );
                        foreach( $post_list as $id => $title ){
                            $selected = $id > 0 && $id == $post->ID;
                            printf('<option value="%s" %s>%s</option>',
                                    $id,
                                    $selected ? 'selected="selected"' : '',
                                    $selected ? sprintf('%s (%s)',$title,__('current','coders_artpad')) : $title);
                        }
                        printf('</select></p>');
                        var_dump($post);
                    },
                    'artpad_post','side','core'
                );
        });
        function generate_post_name( $post_title ){
            return $post_title;
        }
        /*add_action( 'save_post' , function( $post_id , $post ){
            // Block on Autosave
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )  
                return;
            // Block Revisions
            if ( 'revision' == $post->post_type )
                return;
            $post_title = filter_input(INPUT_POST, 'post_title');
            $post_name = filter_input(INPUT_POST, 'post_name') | generate_post_name($post_title);
            
            $parent_id = filter_input(INPUT_POST, '_parent_id') | 0;

            if( $post->post_type === 'artpad_post' ){
                update_post_meta($post->ID, 'post_title', $post_title);
                update_post_meta($post->ID, 'post_parent', $parent_id);
            }
        }, 10 , 2 );*/
    }
    
    
});
