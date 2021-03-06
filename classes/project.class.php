<?php namespace CODERS\ArtPad;
/**
 * 
 */
final class Project extends \CODERS\ArtPad\Model{
    
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_COMPLETED = 2;
    
    protected final function __construct(array $data = array()) {
        
        $this->define('ID', parent::TYPE_TEXT, array('size'=>12))
                ->define('title',parent::TYPE_TEXT , array('size'=>64,'label'=>'Title') )
                ->define('content',parent::TYPE_TEXTAREA , array('label'=>'Content') )
                ->define('access_level',parent::TYPE_TEXT, array('label'=>'Access Level','value'=>'private'))
                ->define('status',parent::TYPE_NUMBER , array('label'=>'Status','value'=>self::STATUS_INACTIVE))
                ->define('connect_patreon',parent::TYPE_CHECKBOX,array('label' => __('Connect to Patreon','coders_artpad')))
                ->define('connect_wc',parent::TYPE_CHECKBOX,array('label' => __('Connect to WooCommerce','coders_artpad')))

                ->define('image_id',parent::TYPE_NUMBER,array('value'=>0))
                //->define('collection_id',parent::TYPE_NUMBER,array('value'=>0))
                ->define('date_created',parent::TYPE_DATETIME , array('label'=>'Created'))
                ->define('date_updated',parent::TYPE_DATETIME , array('label'=>'Updated'));
        
        parent::__construct($data);
    }
    
    /**
     * @return array
     */
    public static final function listStatus(){
        return array(
            self::STATUS_INACTIVE => __('Inactive','codrers_repository'),
            self::STATUS_ACTIVE => __('Active','codrers_repository'),
            self::STATUS_COMPLETED => __('Completed','codrers_repository'),
            
        );
    }
    /**
     * @return array
     */
    public static final function listAccessLevel(){
        return array(
            'public' => __('Public','coders_artpad'),
            'subscription' => __('Subscription','coders_artpad'),
            'nsfw' => __('NSFW','coders_artpad'),
            'private' => __('Private','coders_artpad'),
        );
    }
    /**
     * @param string $element
     * @return boolean
     */
    public final function isUpdated( $element ){
        return $this->get($element, 'updated', FALSE);
    }
    /**
     * @return array
     */
    public final function listUpdated(){
        $output = array();
        foreach( $this->elements() as $element ){
            if( $this->isUpdated($element)){
                $output[ $element ] = $this->value($element);
            }
        }
        return $output;
    }
    /**
     * @return array
     */
    public final function listTiers(){
        //var_dump($this->ID);
        return Tier::List($this->ID);
    }
    /**
     * @return int
     */
    public final function countTiers(){
        return count( $this->listTiers());
    }
    /**
     * @param int $parent
     * @return array
     */
    protected final function listCollections( $parent = 0 ){
        
        $output = array();

        $collections = $this->newQuery()->select('post', '*',
            array( 'parent_id' => $parent, 'tier_id' => $this->ID . '.%' ) ,
            'ID', 'ID' );

        foreach( $collections as $ID => $data ){
            $output[ $ID ] = $data;
            if(strlen($data['title']) === 0 ){
                $data['title'] = $data['name'];
            }
        }
        
        return $output;
    }
    /**
     * @return int
     */
    public final function hasCollections(){
        return count( $this->listCollections()) > 0;
    }
    /**
     * @return boolean
     */
    public final function save(){
        
        $values = $this->listUpdated();
        
        $ID = $this->ID;
        
        $db = self::newQuery();
        
        $result = $db->update('project', $values, array('ID'=>$ID));
        
        return $result > 0;
    }
    /**
     * 
     * @param array $data
     * @return \CODERS\ArtPad\Project
     */
    public final function import(array $data) {
        //parent::import($data);
        foreach( $data as $element => $value ){
            if( $this->has($element)){
                //set value and mark as updated
                $this->setValue($element,$value)->set($element, 'updated', TRUE);
            }
        }
        
        return $this;
    }
    /**
     * @param string $ID
     * @return boolean|\CODERS\ArtPad\Project
     */
    public static final function load( $ID ){
        
        if(strlen($ID)){
            $query = self::newQuery();

            $data = $query->select('project','*',array('ID' => $ID ));

            if( count( $data )){
                return new Project($data[0]);
            }
        }
        
        return FALSE;
    }
    /**
     * @param string $title
     * @return \CODERS\ArtPad\Project|Boolean
     */
    public static final function New( $title ){
        
        $ts = self::__ts();
        
        $name = strtolower( preg_replace('/\s+/', '_', $title));
        
        $project = array(
            'ID' => $name,
            'title' => $title,
            'content' => '',
            'date_created' => $ts,
            'date_updated' => $ts,
        );
        
        $db = self::newQuery();
        
        $result = $db->insert('project', $project);
        
        if( $result > 0 ){
            
            return new Project($project);
        }
        
        return FALSE;
    }
    /**
     * Disabled
     */
    public static function create( $request , $data = array() ){
        return FALSE;
    }
}




add_action('init', function() {

    register_post_type('artpad_project', array(
        'public' => FALSE,
        'publicly_queryable' => FALSE,
        'show_ui' => TRUE,
        'show_in_menu' => FALSE,
        'query_var' => FALSE,
        'rewrite' => array('slug' => 'artpad-project'),
        'capability_type' => 'post',
        'has_archive' => FALSE,
        'hierarchical' => FALSE,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'author', 'thumbnail'),
        'labels' => array(
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
        ),
    ));
});
