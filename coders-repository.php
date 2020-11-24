<?php defined('ABSPATH') or die;
/*******************************************************************************
 * Plugin Name: Coders Repository
 * Plugin URI: https://coderstheme.org
 * Description: Resource Access Gateway and Manager
 * Version: 1.0.0
 * Author: Coder01
 * Author URI: 
 * License: GPLv2 or later
 * Text Domain: coders_repository
 * Domain Path: lang
 * Class: CodersRepo
 * 
 * @author Coder01 <coder01@mnkcoder.com>
 ******************************************************************************/
//final class CodersRepo{
class CodersRepo{
    
    const ENDPOINT = 'coderepo';
    const RESOURCE = 'resource';
    /**
     * @var \CodersRepo
     */
    private static $_INSTANCE = NULL;
    /**
     * @var array
     */
    private static $_dependencies = array(
        'view',
        'model',
        'request',
        'resource',
        'response'
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
     * 
     */
    private final function preload(){
        foreach( $this->_components as $component ){
            if( !$this->component($component) ){
                self::notice(sprintf('Invalid component %s',$component));
            }
        }
    }
    /**
     * @param string $component
     * @param string $type
     * @return boolean
     */
    protected final function component( $component , $type = 'models' ){
        
        $path = self::path(sprintf('components/%s/%s.php',
                $type,
                strtolower( $component) ) );

        if(file_exists($path)){
            require $path;
            return TRUE;
        }
        
        return FALSE;
    }
    /**
     * @return array
     */
    public static final function listModules(){
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
    public static final function base( $ID = '' ){
        
        $base = sprintf('%s/wp-content/uploads/%s',
                preg_replace('/\\\\/', '/', ABSPATH),
                get_option( 'coders_repo_base' , self::ENDPOINT ));
        
        if( strlen( $ID ) ){
            $base .= '/' . $ID;
        }
        
        return $base;
    }
    /**
     * @param string $rid Resource ID
     * @return URL
     */
    public static final function url( $rid ){
        
        return sprintf('%s?%s=%s', get_site_url(),self::ENDPOINT,$rid);

        //return sprintf('%s?template=%s&rid=%s', get_site_url(),self::ENDPOINT,$rid);
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
     * @param string $rid
     * @return string
     */
    public static final function resourceLink( $rid ){

        return sprintf('%s?%s=%s', get_site_url(),self::RESOURCE,$rid);

    }
    /**
     * 
     * @param int $parent_id
     * @return array
     */
    public static final function collection( $parent_id ){
        
        $resources = \CODERS\Repository\Resource::collection($parent_id);
        
        return $resources;
        //return array( $collection => $resources );
    }
    /**
     * @return array
     */
    public static final function collections(){
        
        $collections = \CODERS\Repository\Resource::storage();
        
        return $collections;
    }
    /**
     * 
     * @global \wpdb $wpdb
     * @global string $prefix
     * @param string $public_id
     * @return \CODERS\Repository\Resource|Boolean
     */
    public static final function import( $public_id ){
        
        return \CODERS\Repository\Resource::import($public_id);
    }
    /**
     * @param string $endpoint
     * @throws \Exception
     */
    public static final function module( $endpoint ){
        
        try{
            if(strlen($endpoint) === 0){
                throw new \Exception('Invalid Endpoint');
            }
            if( self::$_INSTANCE !== NULL ){
                throw new \Exception('A module is already running');
            }

            $path = sprintf('%s/modules/%s/module.php',
                    preg_replace( '/\\\\/', '/', CODERS__REPOSITORY__DIR ),
                    strtolower( $endpoint ) );

            $class = sprintf('\CODERS\Repository\%s\%sModule',$endpoint,$endpoint);

            if( file_exists( $path ) ){
                
                require_once $path;

                if(class_exists( $class ) && is_subclass_of( $class, self::class ) ){
                    self::$_INSTANCE = new $class();
                    return self::$_INSTANCE;
                }
                else{
                    throw new \Exception(sprintf('Invalid Endpoint %s',$class));
                }
            }
            else{
                throw new \Exception(sprintf('Invalid Endpoint %s',$path));
            }
        }
        catch (Exception $ex) {
            self::notice($ex->getMessage(), 'error');
        }
        
        return FALSE;
    }
    /**
     * @param String $file_id
     * @param boolean $attach
     * @return \CodersRepo
     */
    public static final function download( $file_id , $attach = FALSE ){
        
        try{
            if(is_null($file_id) || strlen($file_id) === 0 ){
                throw new Exception('INVALID OR EMPTY RID');
            }
            
            $file = self::import( $file_id );
            if( $file !== FALSE ){
                //var_dump($file->path());
                //foreach( $file->headers( $attach ) as $header ){
                //    header( $header ); 
                //}
                //print $file->read();
                //output strream (not working with WP)
                $file->stream( /*default chunk size*/ );
            }
            else {
                throw new Exception(sprintf('INVALID RID#%s',$file_id));
            }
        }
        catch (Exception $ex) {
            printf( '<p><i>%s</i></p>',$ex->getMessage() );
        }
    }
    /**
     * @param string $file_id
     * @return string
     */
    public final function encode( $file_id ){

        $file = self::import( $file_id );
        
        return ($file !== FALSE ) ?
                base64_encode( $file->read( ) ) :
                FALSE;
    }
    /**
     * @param String $file_id
     * @return String
     */
    public static final function attach( $file_id ){
        
        $file = self::import( $file_id );
        
        if($file !== FALSE ){
        
            return base64_encode( $file->load( ) );
        }
        
        return '';
    }
    /**
     * @param string $endpoint
     * @return boolean
     */
    public final function validate( $endpoint ){
        return in_array($endpoint, array('posts','repository'));
    }
    /**
     * @param string $action
     * @param array $data
     * @return CodersRepo
     */
    protected function run( $action = '' ){

        $request = strlen($action) ?
                //define the output route
                \CODERS\Repository\Request::route( $action ) :
                //check for post/get variables
                \CODERS\Repository\Request::import( );
        
        \CODERS\Repository\Response::create($request);
        
        return $this;
    }
    /**
     * @return \CodersRepo
     */
    public static final function init(){
        
        //one time loader
        if(defined('CODERS__REPOSITORY__DIR')){
            return $this;
        }
        define('CODERS__REPOSITORY__DIR',__DIR__);
        define('CODERS__REPOSITORY__URL', plugin_dir_url(__FILE__));
        
        if(self::setup()){
            self::notice(__('Coders Repository Database registered!','coders_repository'));
        }

        //register dependencies        
        foreach( self::$_dependencies as $class ){
            require_once( sprintf( '%s/classes/%s.class.php' , CODERS__REPOSITORY__DIR , $class ) );
        }
        
        if(is_admin()){
            
            //initialize Admin
            CodersRepo::module('Admin');
            
            add_action('admin_menu', function() {
            
                $root = '';
                /*describe menu items*/
                $pages = array(
                    'main' => __( 'Artist Pad' , 'coders_repository' ),
                    'projects' => __( 'Projects' , 'coders_repository' ),
                    'accounts' => __( 'Accounts' , 'coders_repository' ),
                    'subscriptions' => __( 'Subscriptions' , 'coders_repository' ),
                    'payments' => __( 'Payments' , 'coders_repository' ),
                    'settings' => __( 'Settings' , 'coders_repository' ),
                    'logs' => __( 'Logs' , 'coders_repository' ),
                );
                
                foreach( $pages as $page => $title ){
                    if(strlen($root)){
                        add_submenu_page(
                                $root, $title, $title,
                                'administrator',
                                $root . '-' . $page ,
                                function() use( $page ) {
                                    \CODERS\Repository\Response::fromRoute('admin.' . $page );
                        });
                    }
                    else{
                        $root = 'coders-' . $page;
                        add_menu_page(
                                $title, $title,
                                'administrator', $root,
                                function() use( $page ) {
                                    \CODERS\Repository\Response::fromRoute('admin.' . $page );
                        }, 'dashicons-art', 51);
                    }
                }
            }, 100000 );
            //register all ajax handlers
            add_action( 'wp_ajax_coders_admin' , function(){
                //print json_encode(array('response'=>'OK'));
                if(is_admin() ){
                    \CODERS\Repository\Response::fromAjax('admin.ajax');
                }
                wp_die();
                //die;
            }, 100000 );
            //register all ajax handlers
            add_action( 'wp_ajax_nopriv_coders_admin' , function(){

                wp_die();

            }, 100000 );
        }
        else{
            //INITIALIZE REDIRECTION RULES
            add_action( 'init' , function(){
                global $wp, $wp_rewrite;
                //import the regiestered locale's endpoint from the settinsg
                $endpoint = \CodersRepo::ENDPOINT;
                $resource = \CodersRepo::RESOURCE;
                //now let wordpress do it's stuff with the query router
                $wp->add_query_var( $endpoint );
                $wp->add_query_var( $resource );
                //$wp->add_query_var('template');
                add_rewrite_endpoint(self::ENDPOINT, EP_ROOT);
                add_rewrite_endpoint(self::RESOURCE, EP_ROOT);
                $wp_rewrite->add_rule(
                        sprintf('^/%s/?$', $endpoint),
                        'index.php?' . $endpoint . '=default', 'bottom');
                //$wp_rewrite->add_rule(
                //        sprintf('^/%s/?$', $resource ),
                //        'index.php?' . $resource . '=empty', 'bottom');
                //and rewrite
                $wp_rewrite->flush_rules();
            } );
            //INITIALIZE TEMPLATE REDIRECTION (FOR PUBLIC APPLICATION ONLY!!!)
            add_action( 'template_redirect', function( ){
                /* Make sure to set the 404 flag to false, and redirect  to the contact page template. */
                global $wp , $wp_query;
                $query = $wp->query_vars;
                switch( TRUE ){
                    case array_key_exists(self::RESOURCE, $query):
                        $wp_query->set('is_404', FALSE);
                        $resource = filter_input(INPUT_GET, self::RESOURCE );
                        $attachment = filter_input(INPUT_GET, 'attachment');
                        CodersRepo::download(
                                $resource !== NULL ? $resource : '#INVALID',
                                $attachment !== NULL ? $attachment : FALSE );
                        //hooked repository app, exit WP framework
                        exit;
                    case array_key_exists(self::ENDPOINT, $query):
                        $wp_query->set('is_404', FALSE);
                        $EP = CodersRepo::module($query[self::ENDPOINT]);
                        if( FALSE !== $EP ){
                            $EP->run($query[self::ENDPOINT]);
                        }
                        //$request = \CODERS\Repository\Request::import();
                        //\CODERS\Repository\Response::create($request);
                        //hooked repository app, exit WP framework
                        exit;
                }
            } );
            //register ajax handlers
            add_action( 'wp_ajax_coders_module' , function(){
                
                CodersRepo::instance()->run('ajax');
                
                wp_die();
            }, 100000 );
            //register ajax handlers
            add_action( 'wp_ajax_nopriv_coders_module' , function(){
                
                CodersRepo::instance()->run('ajax');
                
                wp_die();
            }, 100000 );
        }
    }
    /**
     * Setup DB Activation Hook
     */
    private static final function setup(){
        //do only when activated
        register_activation_hook(__FILE__, function( ){
            global $wpdb,$table_prefix;
            $script_path = sprintf('%s/sql/setup.sql', preg_replace( '/\\\\/' , '/' , __DIR__ ) );
            if(file_exists($script_path)){
                $script_file = file_get_contents($script_path);
                if( FALSE !== $script_file && strlen($script_file)){
                    $script_sql = preg_replace('/{{TABLE_PREFIX}}/',$table_prefix,$script_file);
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
        });
    }
    /**
     * Register Resource Post Type
     */
    private static final function register(){
        
        add_action( 'init' , function(){
            $post_labels = array(
                'name' => _x('Repository', 'Repo', 'coders_repository'),
                'singular_name' => _x('Resource', 'Post type singular name', 'coders_repository'),
                'menu_name' => _x('Repository', 'Admin Menu text', 'coders_repository'),
                'name_admin_bar' => _x('Resource', 'Add New on Toolbar', 'coders_repository'),
                'add_new' => __('Create', 'coders_repository'),
                'add_new_item' => __('Add New Resource', 'coders_repository'),
                'new_item' => __('New Resource', 'coders_repository'),
                'edit_item' => __('Edit Resource', 'coders_repository'),
                'view_item' => __('View Resource', 'coders_repository'),
                'all_items' => __('Repository', 'coders_repository'),
                'search_items' => __('Search Resource', 'coders_repository'),
                'parent_item_colon' => __('Parent Resource:', 'coders_repository'),
                'not_found' => __('No resources found.', 'coders_repository'),
                'not_found_in_trash' => __('No resources found in Trash.', 'coders_repository'),
                'featured_image' => _x('Resource Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'coders_repository'),
                'set_featured_image' => _x('Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'coders_repository'),
                'remove_featured_image' => _x('Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'coders_repository'),
                'use_featured_image' => _x('Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'coders_repository'),
                'archives' => _x('Resource archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'coders_repository'),
                'insert_into_item' => _x('Insert into Resource', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'coders_repository'),
                'uploaded_to_this_item' => _x('Uploaded to this Resource', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'coders_repository'),
                'filter_items_list' => _x('Filter Resources', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'coders_repository'),
                'items_list_navigation' => _x('Repository Navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'coders_repository'),
                'items_list' => _x('Resource List', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'coders_repository'),
            );
            $project_labels = array(
                'name' => _x('Projects', 'Projects', 'coders_repository'),
                'singular_name' => _x('Project', 'Project', 'coders_repository'),
                'menu_name' => _x('Project', 'Project', 'coders_repository'),
                'name_admin_bar' => _x('Project', 'New Project', 'coders_repository'),
                'add_new' => __('Create', 'coders_repository'),
                'add_new_item' => __('Add New Project', 'coders_repository'),
                'new_item' => __('New Project', 'coders_repository'),
                'edit_item' => __('Edit Project', 'coders_repository'),
                'view_item' => __('View Project', 'coders_repository'),
                'all_items' => __('Projects', 'coders_repository'),
                'search_items' => __('Search Project', 'coders_repository'),
                'parent_item_colon' => __('Parent Project:', 'coders_repository'),
                'not_found' => __('No projects  found.', 'coders_repository'),
                'not_found_in_trash' => __('No projects found in Trash.', 'coders_repository'),
                'featured_image' => _x('Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'coders_repository'),
                'set_featured_image' => _x('Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'coders_repository'),
                'remove_featured_image' => _x('Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'coders_repository'),
                'use_featured_image' => _x('Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'coders_repository'),
                'archives' => _x('Archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'coders_repository'),
                'insert_into_item' => _x('Insert into Project', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'coders_repository'),
                'uploaded_to_this_item' => _x('Uploaded to this Project', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'coders_repository'),
                'filter_items_list' => _x('Filter Projects', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'coders_repository'),
                'items_list_navigation' => _x('Projects Navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'coders_repository'),
                'items_list' => _x('Project List', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'coders_repository'),
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
            
        } );
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
        }
    }
    /**
     * @return \CodersRepo
     */
    static final function instance(){
        if(is_null(self::$_INSTANCE)){
            //self::$_INSTANCE = new \CodersRepo ();
            if(self::setup()){
                self::notice(__('Coders Repository Database registered!','coders_repository'));
            }
            self::register();
            self::init();
        }
        return self::$_INSTANCE;
    }
}

CodersRepo::init();


