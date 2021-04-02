<?php defined('ABSPATH') or die;
/*******************************************************************************
 * Plugin Name: Coders ArtistPad
 * Plugin URI: https://coderstheme.org
 * Description: A community subscription content plugin ;)
 * Version: 1.0.0
 * Author: Coder01
 * Author URI: 
 * License: GPLv2 or later
 * Text Domain: coders_artpad
 * Domain Path: lang
 * Class: ArtPad
 * 
 * @author Coder01 <coder01@mnkcoder.com>
 ******************************************************************************/
class ArtPad{
    const ENDPOINT = 'artpad';
    const RESOURCE = 'resource';
    /**
     * @var \ArtPad
     */
    private static $_INSTANCE = NULL;
    /**
     * @var array
     */
    private static $_status = array(
        //add timers here for profiling
    );
    /**
     * @var array
     */
    private static $_dependencies = array(
        //'text',
        'view',
        'model',
        'request',
        'resource',
        'response',
    );
    /**
     * @var array
     */
    private $_components = array(
        //
    );
    /**
     * 
     */
    protected function __construct() {

       $this->preload(); 
        
    }
    /**
     * @return string
     */
    public final function __toString() {
        $NS = explode('\\', get_class($this ) );
        $class = $NS[count($NS) - 1 ];
        $suffix = strrpos($class, 'Module');
        return strtolower( substr($class, 0 ,$suffix) );
    }
    /**
     * @param string $component
     * @param string $type
     * @return boolean
     */
    private final function register( $component , $type ){

        $path = self::path(sprintf('components/%s/%s.php',
                strtolower( $type ),
                strtolower( $component ) ) );

        if(file_exists($path)){
            require $path;
            return TRUE;
        }
        
        return FALSE;
    }
    /**
     * @return \ArtPAd
     */
    private final function preload(){
        foreach( $this->_components as $type => $list ){
            foreach( $list as $component ){
                if( !$this->register( $component , $type ) ){
                    self::notice(sprintf('Invalid component %s.%s',$type,$component));
                }
            }
        }
        return $this;
    }
    /**
     * @param string $component
     */
    protected final function include( $component ){
        $name = explode('.', $component);
        if( count( $name ) > 1 ){
            if( !array_key_exists($name[0], $this->_components)){
                $this->_components[ $name[0]] = array();
            }
            $this->_components[ $name[0]][] = $name[1];
        }
        return $this;
    }
    /**
     * @return array
     */
    protected static final function listModules(){
        $output = array();
        $root = self::path('modules/');
        //var_dump($root);
        foreach(scandir($root) as $module ){
            $path = self::path( 'modules/' . $module );
            if( $module !== '.' && $module !== '..' && is_dir($path) ){
                $output[] = $module;
            }
        }
        return $output;
    }
    /**
     * @param string $ID
     * @return string
     */
    public static final function Storage( $ID = '' ){
        
        $base = sprintf('%s/wp-content/uploads/%s',
                preg_replace('/\\\\/', '/', ABSPATH),
                get_option( 'coders_repo_base' , self::ENDPOINT ));
        
        if( strlen( $ID ) ){
            $base .= '/' . $ID;
        }
        
        return $base;
    }
    /**
     * @param string $path
     * @return string
     */
    public static final function path( $path = '' ){
        $root = preg_replace( '/\\\\/', '/', CODERS__REPOSITORY__DIR );
        return strlen($path) ? sprintf('%s/%s',$root,$path) : $root;
    }
    /**
     * @param string $endpoint
     * @return ArtPad|Boolean
     */
    public static final function module( $endpoint ){
        
        if( self::$_INSTANCE !== NULL ){
            return FALSE;
            //throw new \Exception(sprintf('%s already running',$endpoint));
        }
        if( strlen($endpoint) === 0){
            return FALSE;
            //throw new \Exception('Invalid Endpoint');
        }
        
        $modName = strtolower( $endpoint );
        $path = self::path(sprintf('/modules/%s/module.php',$modName ));
        $class = sprintf('\CODERS\ArtPad\%s\%sModule',$endpoint,$endpoint);

        if( $modName === 'admin' && !is_admin() ){
            //do not ex`pse admin module in public
            //throw new \Exception('Invalid Endpoint');
            return FALSE;
        }

        if( file_exists( $path ) ){
            require_once $path;
            if(class_exists( $class ) && is_subclass_of( $class, self::class ) ){
                self::check(sprintf('%s_module', $modName));
                self::$_INSTANCE = new $class();
                return self::$_INSTANCE;
            }
        }
        return FALSE;
    }
    /**
     * @param string $route (empty by default)
     * @return boolean
     */
    function run( $route = '' ){
        
        if(strlen($route)){
            return \CODERS\ArtPad\Response::Route($route);
        }
        
        return FALSE;
    }
    /**
     * @return boolean
     */
    public static final function loaded(){
        return (defined('CODERS__REPOSITORY__DIR'));
    }
    /**
     * @return \ArtPad
     */
    public static final function init(){
        
        //one time loader
        if(!self::check('init')){ return; }
        
        //Activate plugin
        register_activation_hook(__FILE__, function( ){
            ArtPad::setupDataBase();
            ArtPad::setupRewrite( TRUE );
        });
        //Disable plugin
        register_deactivation_hook(__FILE__, function(){
            flush_rewrite_rules( );
        });

        //Initialize framework
        define('CODERS__REPOSITORY__DIR',__DIR__);
        define('CODERS__REPOSITORY__URL', plugin_dir_url(__FILE__));
        //register dependencies        
        foreach( self::$_dependencies as $class ){
            require_once( sprintf( '%s/classes/%s.class.php' , CODERS__REPOSITORY__DIR , $class ) );
        }
        
        //register ajax handlers
        add_action( sprintf('wp_ajax_%s_admin',ArtPad::ENDPOINT) , function(){
            ArtPad::check('ajax_admin');
            $request = \CODERS\ArtPad\Request::ajax('admin.ajax');
            \CODERS\ArtPad\Response::create($request);
            exit;
        }, 100000 );
        add_action( sprintf('wp_ajax_%s_public',ArtPad::ENDPOINT) , function(){
            ArtPad::check('ajax_public');
            $request = \CODERS\ArtPad\Request::ajax('ajax');
            \CODERS\ArtPad\Response::create($request);
        });
        add_action( sprintf('wp_ajax_nopriv_%s_admin',ArtPad::ENDPOINT) , 'exit', 100000 );
        add_action( sprintf('wp_ajax_nopriv_%s_public',ArtPad::ENDPOINT) , 'exit', 100000 );
        
        //Initialize integration
        add_action( 'init' , function(){
            //ArtPad::setupPosts();
            if(is_admin()){
                //initialize Admin
                $admin = ArtPad::module('Admin');
            }
            else{
                if( !self::check('rewrite') ){ return; }
                //now let wordpress do it's stuff with the query router
                add_rewrite_endpoint( ArtPad::ENDPOINT, EP_ROOT );
                //ArtPad::setupRewrite( );
            }
        },10);
        //Setup Route management
        //add_action( 'parse_query', function( ){
        add_action( 'template_redirect', function( ){
            $endpoint = get_query_var( ArtPad::ENDPOINT  );
            if ( strlen($endpoint)) {
                if( !ArtPad::check('redirect')) { return; }
                global $wp_query;
                $wp_query->set('is_404', FALSE);
                $path = explode('-', strtolower( $endpoint ) );
                switch( $path[0] ){
                    case 'admin':
                        //locked in public
                        break;
                    case 'rid':
                        if( count( $path ) > 1){
                            //load from public ID and validate
                            $resource = CODERS\ArtPad\Resource::load($path[1],TRUE,TRUE);
                            if (FALSE !== $resource) {
                                $resource->stream();
                            }
                            else{
                                wp_die(sprintf('INVALID_REF#%s',$path[1]));
                            }
                            ArtPad::terminate();
                            //exit;
                        }
                        break;
                    default:
                        $module = ArtPad::module($path[0]);
                        if(FALSE !== $module && $module->run( $endpoint )){
                            ArtPad::terminate();
                            //exit;
                        }
                        break;
                }
                //hooked repository app, exit WP framework into WP error display
                //wp_die('Invalid Endpoint');
            }
        },10);
    }
    /**
     * 
     */
    public static final function RequestHack(){
        $rid = \CODERS\ArtPad\Request::match();
        if( strlen($rid) ){
            \CODERS\ArtPad\Resource::load($rid);
            exit;
        }
    }
    /**
     * @global wpdb $wpdb
     * @global string $table_prefix
     * @return boolean
     */
    private static final function setupDataBase(){
        
        if( !self::check('database') ){ return; }

        global $wpdb,$table_prefix;

        $script_path = sprintf('%s/sql/setup.sql', preg_replace( '/\\\\/' , '/' , __DIR__ ) );

        if(file_exists($script_path)){
            $script_file = file_get_contents($script_path);
            if( FALSE !== $script_file && strlen($script_file)){
                $coders_table = $table_prefix . self::ENDPOINT;
                $script_sql = preg_replace('/{{TABLE_PREFIX}}/',$coders_table,$script_file);
                $tables = explode(';', $script_sql);
                $counter = 0;
                foreach( $tables as $T ){
                    if ($wpdb->query($T)) {
                        $counter++;
                    }
                    else {
                        //
                    }
                }
                return $counter === count( $tables );
            }
        }
        return FALSE;
    }
    /**
     * @global WP_Query $wp_query
     */
    public static final function terminate(){
        global $wp_query;
        $wp_query->set('is_404', FALSE);
        exit;
    }
    /**
     * @param boolean $flush FALSE default
     * @global WP $wp
     * @global WP_Rewrite $wp_rewrite
     */
    public static final function setupRewrite( $flush = FALSE ){
        if( !self::check('setup_rewrite')) { return; }
        global $wp, $wp_rewrite;
        //import the regiestered locale's endpoint from the settinsg
        $endpoint = \ArtPad::ENDPOINT;
        add_rewrite_endpoint( ArtPad::ENDPOINT, EP_ROOT );
        $wp->add_query_var( $endpoint );
        $wp_rewrite->add_rule("^/$endpoint/?$",'index.php?' . $endpoint . '=$matches[1]', 'top');
        //and rewrite
        if ($flush ){
            $wp_rewrite->flush_rules();
        }
    }
    /**
     * Register Resource Post Type
     */
    public static final function setupPosts(){
        if( !self::check('post') ){ return; }
        $post_labels = array(
            'name' => _x('Repository', 'Repo', 'coders_artpad'),
            'singular_name' => _x('Resource', 'Post type singular name', 'coders_artpad'),
            'menu_name' => _x('Repository', 'Admin Menu text', 'coders_artpad'),
            'name_admin_bar' => _x('Resource', 'Add New on Toolbar', 'coders_artpad'),
            'add_new' => __('Create', 'coders_artpad'),
            'add_new_item' => __('Add New Resource', 'coders_artpad'),
            'new_item' => __('New Resource', 'coders_artpad'),
            'edit_item' => __('Edit Resource', 'coders_artpad'),
            'view_item' => __('View Resource', 'coders_artpad'),
            'all_items' => __('Repository', 'coders_artpad'),
            'search_items' => __('Search Resource', 'coders_artpad'),
            'parent_item_colon' => __('Parent Resource:', 'coders_artpad'),
            'not_found' => __('No resources found.', 'coders_artpad'),
            'not_found_in_trash' => __('No resources found in Trash.', 'coders_artpad'),
            'featured_image' => _x('Resource Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'coders_artpad'),
            'set_featured_image' => _x('Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'coders_artpad'),
            'remove_featured_image' => _x('Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'coders_artpad'),
            'use_featured_image' => _x('Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'coders_artpad'),
            'archives' => _x('Resource archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'coders_artpad'),
            'insert_into_item' => _x('Insert into Resource', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'coders_artpad'),
            'uploaded_to_this_item' => _x('Uploaded to this Resource', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'coders_artpad'),
            'filter_items_list' => _x('Filter Resources', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'coders_artpad'),
            'items_list_navigation' => _x('Repository Navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'coders_artpad'),
            'items_list' => _x('Resource List', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'coders_artpad'),
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
               'labels'             => $post_labels,
               'public'             => FALSE,
               'publicly_queryable' => FALSE,
               'show_ui'            => FALSE,
               'show_in_menu'       => FALSE,
               'query_var'          => FALSE,
               'rewrite'            => array( 'slug' => 'coderepo-post' ),
               'capability_type'    => 'post',
               'has_archive'        => FALSE,
               'hierarchical'       => TRUE,
               'menu_position'      => null,
               'supports'           => array( 'title', 'editor', 'author', 'excerpt', 'comments' ),
        );

        $coderepo_project = array(
               'labels'             => $project_labels,
               'public'             => FALSE,
               'publicly_queryable' => FALSE,
               'show_ui'            => FALSE,
               'show_in_menu'       => FALSE,
               'query_var'          => FALSE,
               'rewrite'            => array( 'slug' => 'coderepo-project' ),
               'capability_type'    => 'post',
               'has_archive'        => FALSE,
               'hierarchical'       => FALSE,
               'menu_position'      => null,
               'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
        );

        register_post_type( 'coderepo_post', $coderepo_post );
        register_post_type( 'coderepo_project', $coderepo_project );

    }
    /**
     * @param int $product_id
     * @param string $email
     * @param float $amount
     * @param string $label
     * @return boolean
     */
    public static final function Subscribe( $product_id , $email , $amount , $label  ){
        
        $products = self::product();
        
        if(in_array($product_id, $products)){
            
            $account = \CODERS\ArtPad\Account::LoadByEmail($email);
            if( FALSE !== $account ){
                $account->subscribe();
            }
            else{
                $account = \CODERS\ArtPad\Account::New(array('email_address'=>$email));
                $account->subscribe($amount);
            }
            return TRUE;
        }
        
        return FALSE;
    }
    /**
     * Send a message through the admin notifier
     * @param string $message
     * @param string $type (success, info, warning, error)
     * @param boolean $dismissible
     */
    public static final function notice( $message , $type = 'warning' , $dismissible = FALSE ){
        if( is_admin( ) ){
            add_action( 'admin_notices' , function() use( $type , $dismissible, $message ){
                printf('<div class="notice notice-%s %s"><p>%s</p></div>',
                        $type,
                        $dismissible ? 'is-dismissible' : '',
                        $message);
            });
        }
        else{
            //do something in public?
            $ts = date('Y-m-d H:i:s');
            printf('<p>[ %s : <strong>%s</strong> ] %s</p>',$ts,$type,$message);
        }
    }
    /**
     * @param string $text
     * @return string
     */
    public static final function __( $text ){
        
        return __( $text , 'coders_artpad');

        //return \CODERS\ArtPad\Text::__($text);
    }
    /**
     * @param string $status
     */
    public static final function ts( $status ){
        self::check($status);
    }
    /**
     * @param string $status
     * @return boolean
     */
    public static final function check( $status ){
        if( strlen($status) && !array_key_exists($status, self::$_status) ){
            self::$_status[ $status ] = time();
            return TRUE;
        }
        return FALSE;
    }
    /**
     * @return array
     */
    public static final function stamp( $asCounter = FALSE ){
        if( $asCounter ){
            $first = 0;
            $output = array();
            foreach( self::$_status as $id => $ts ){
                if( $first > 0 ){
                    $output[ $id ] = ($ts - $first);
                }
                else{
                    $output[ $id ] = $first;
                    $first = $ts;
                }
                //$output[ $id ] = date('Y-m-d H:i:s',$ts);
            }
            return $output;
        }
        return self::$_status;
    }
    /**
     * @param string $option
     * @param mixed $value
     * @return boolean
     */
    public static final function setOption( $option , $value ){
        return update_option(sprintf('%s_%s',self::ENDPOINT,$option), $value);
    }
    /**
     * @param string $option
     * @param mixed $default
     * @return mixed
     */
    public static final function getOption( $option , $default = FALSE ){
        return get_option(sprintf('%s_%s',self::ENDPOINT,$option), $default );
    }
}

ArtPad::init();





