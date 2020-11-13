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
    
    const ENDPOINT = 'repository';
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
     */
    protected final function component( $component , $type = 'models' ){
        
        $path = sprintf('%s/components/%s/%s.php',CODERS__REPOSITORY__DIR,$component,$component);
        
        if(file_exists($path)){
            require $path;
            return TRUE;
        }
        
        return FALSE;
    }
    /**
     * @param string $collection
     * @return string
     */
    public static final function base( $collection = '' ){
        
        $base = sprintf('%s%s',
                preg_replace('/\\\\/', '/', ABSPATH),
                get_option( 'coders_repo_base' , self::ENDPOINT ));
        
        if( strlen( $collection ) ){
            $base .= '/' . $collection;
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
     * @param string $rid
     * @return string
     */
    public static final function resourceLink( $rid ){

        return sprintf('%s?%s=%s', get_site_url(),self::RESOURCE,$rid);

    }
    /**
     * 
     * @param string $collection
     * @return array
     */
    public static final function collection( $collection ){
        
        $resources = \CODERS\Repository\Resource::collection($collection);
        
        return $resources;
        //return array( $collection => $resources );
    }
    /**
     * @return array
     */
    public static final function collections(){
        return array();
    }
    /**
     * @return boolean
     */
    /*public static final function route(){
        
        $request = \CODERS\Repository\Request::import();

        return \CODERS\Repository\Response::create($request);
    }*/
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
                foreach( $file->headers( $attach ) as $header ){
                    header( $header ); 
                }
                print $file->read();
                //output strream (not working with WP)
                //$file->stream( /*default chunk size*/ );
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
    public final function attach( $file_id ){
        
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
                                    //CodersRepo::instance()->run('admin.' . $page);
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
                                    //CodersRepo::instance()->run('admin.' . $page);
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
            
            $labels = array(
                'name' => _x('Repository', 'Repo', 'textdomain'),
                'singular_name' => _x('Resource', 'Post type singular name', 'textdomain'),
                'menu_name' => _x('Repository', 'Admin Menu text', 'textdomain'),
                'name_admin_bar' => _x('Resource', 'Add New on Toolbar', 'textdomain'),
                'add_new' => __('Create', 'textdomain'),
                'add_new_item' => __('Add New Resource', 'textdomain'),
                'new_item' => __('New Resource', 'textdomain'),
                'edit_item' => __('Edit Resource', 'textdomain'),
                'view_item' => __('View Resource', 'textdomain'),
                'all_items' => __('Repository', 'textdomain'),
                'search_items' => __('Search Resource', 'textdomain'),
                'parent_item_colon' => __('Parent Resource:', 'textdomain'),
                'not_found' => __('No resources found.', 'textdomain'),
                'not_found_in_trash' => __('No resources found in Trash.', 'textdomain'),
                'featured_image' => _x('Resource Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain'),
                'set_featured_image' => _x('Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain'),
                'remove_featured_image' => _x('Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain'),
                'use_featured_image' => _x('Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain'),
                'archives' => _x('Resource archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain'),
                'insert_into_item' => _x('Insert into Resource', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain'),
                'uploaded_to_this_item' => _x('Uploaded to this Resource', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain'),
                'filter_items_list' => _x('Filter Resources', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain'),
                'items_list_navigation' => _x('Repository Navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'textdomain'),
                'items_list' => _x('Resource List', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'textdomain'),
            );
            
            $supports = array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' );
            
            $resource = array(
                   'labels'             => $labels,
                   'public'             => FALSE,
                   'publicly_queryable' => FALSE,
                   'show_ui'            => FALSE,
                   'show_in_menu'       => FALSE,
                   'query_var'          => FALSE,
                   'rewrite'            => array( 'slug' => 'resource' ),
                   'capability_type'    => 'post',
                   'has_archive'        => FALSE,
                   'hierarchical'       => TRUE,
                   'menu_position'      => null,
                   'supports'           => $supports,
            );
            
            register_post_type( 'resource', $resource );
            
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


