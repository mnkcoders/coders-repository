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
final class CodersRepo{
    
    const ENDPOINT = 'repository';
    const RESOURCE = 'resource';
    /**
     * @var \CodersRepo
     */
    private static $_INSTANCE = NULL;
    /**
     * @var type 
     */
    private static $_dependencies = array(
        'view',
        'model',
        'request',
        'resource',
        'response'
    );
    /**
     * 
     */
    private final function __construct() {

        $this->init();
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
     * @param String $file_id
     * @param boolean $attach
     * @return \CodersRepo
     */
    public final function download( $file_id , $attach = FALSE ){
        
        $file = self::import( $file_id );
        
        if( $file !== FALSE ){
            foreach( $file->headers( $attach ) as $header ){
                header( $header ); 
            }
            //print $file->read();
            //output strream
            $file->stream( /*default chunk size*/ );
        }
        else {
            printf('INVALID RID#%s',$file_id);
        }

        return $this;
    }
    /**
     * @param boolean $attachment
     * @return string|boolean
     */
    public final function get( $attachment = FALSE ){
        
        $rid = filter_input(INPUT_GET, self::RESOURCE );

        if( !is_null($rid) ){
            $this->download($rid , $attachment );
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
     * 
     * @return \CodersRepo
     */
    private final function init(){
        
        //one time loader
        if(defined('CODERS__REPOSITORY__DIR')){
            return $this;
        }
        define('CODERS__REPOSITORY__DIR',__DIR__);
        define('CODERS__REPOSITORY__URL', plugin_dir_url(__FILE__));

        //register dependencies        
        foreach( self::$_dependencies as $class ){
            require_once( sprintf( '%s/classes/%s.class.php' , CODERS__REPOSITORY__DIR , $class ) );
        }
        
        if(is_admin()){
            //register styles and scripts using the helper within the view
            \CODERS\Repository\View::attachScripts('admin',
                    array('style'),
                    array('script'=>array('jquery')));
            //add_filter( 'admin_body_class', 'coders-repository' );
            
            add_action('admin_menu', function() {
                add_menu_page(
                        __('Repository', 'coders_repository'),
                        __('Repository', 'coders_repository'),
                        'administrator', 'coders-repository',
                        function() {
                            $R = \CODERS\Repository\Request::import('admin');
                            \CODERS\Repository\Response::create($R);
                        }, 'dashicons-grid-view'  ,51);
                add_submenu_page(
                        'coders-repository',
                        __('Settings', 'coders_repository'),
                        __('Settings', 'coders_repository'),
                        'administrator','coders-repository-settings',
                        function(){
                            $R = \CODERS\Repository\Request::import('admin.settings');
                            \CODERS\Repository\Response::create($R);
                        });
            }, 100000);
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
                    case array_key_exists(self::ENDPOINT, $query):
                        $wp_query->set('is_404', FALSE);
                        $request = \CODERS\Repository\Request::import();
                        \CODERS\Repository\Response::create($request);
                        //hooked repository app, exit WP framework
                        exit;
                    case array_key_exists(self::RESOURCE, $query):
                        $wp_query->set('is_404', FALSE);
                        $rid = filter_input(INPUT_GET, self::RESOURCE );
                        $is_attachment = filter_input(INPUT_GET, 'attachment');
                        if( !is_null($rid) ){
                            $attach = !is_null($is_attachment) && intval( $is_attachment ) > 0;
                            CodersRepo::instance()->get( $attach );
                        }
                        else{
                            print('#');
                        }
                        //hooked repository app, exit WP framework
                        exit;
                }
            } );
        }
        
        return $this;
    }
    /**
     * Setup DB Activation Hook
     */
    private static final function setup(){
        register_activation_hook(__FILE__, function( ){
            global $wpdb,$table_prefix;
            $script_path = sprintf('%s/sql/setup.sql',__DIR__);
            if(file_exists($script_path)){
                $script_file = file_get_contents($script_path);
                if( $script_file !== FALSE && strlen($script_file)){
                    $script_sql = preg_replace('/{{TABLE_PREFIX}}/',$table_prefix,$script_file);
                    if($wpdb->query($script_sql)){
                        return TRUE;
                    }
                }
            }
            return FALSE;
        });
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
            self::$_INSTANCE = new \CodersRepo ();
            if(self::setup()){
                self::notice(__('Coders Repository Database registered!','coders_repository'));
            }
        }
        return self::$_INSTANCE;
    }
}

CodersRepo::instance();


