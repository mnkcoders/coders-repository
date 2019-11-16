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
    /**
     *
     * @var \CodersRepo
     */
    private static $_INSTANCE = NULL;
    
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
        return sprintf('%s?template=%s&rid=%s', get_site_url(),self::ENDPOINT,$rid);
    }
    /**
     * 
     * @param string $collection
     * @return array
     */
    public static final function collection( $collection ){
        
        \CODERS\Repository\Resource::collection($collection);
        
        return array();
    }
    public static final function repositories(){
        
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
     * @global \WP $wp
     * @param string $endpoint
     * @return boolean
     */
    public static final function queryRoute() {

        global $wp;

        $query = $wp->query_vars;
        
        return array_key_exists(self::ENDPOINT, $query) || //is permalink route
                ( array_key_exists('template', $query)      //is post template
                && self::ENDPOINT === $query['template']);
    }
    /**
     * @return string|boolean
     */
    public final function request(){
        
        $rid = filter_input(INPUT_GET, 'rid');
        
        return !is_null($rid) ? $rid : FALSE;
    }
    /**
     * @param String $file_id
     * @return \CodersRepo
     */
    public final function download( $file_id ){
        
        $file = self::import( $file_id );
        
        if($file !== FALSE ){
            
            header('Content-Type:' . $file->type );
            
            if( !$file->isAttachment()){
                header( sprintf('Content-Disposition: attachment; filename="%s"',$file->name ));
            }
            else{
                header( sprintf('Content-Disposition: inline; filename="%s"',$file->name ));
            }
            
            header("Content-Length: " . $file->size() );

            print $file->read();
        }
        else {
            print $file->path();
        }

        return $this;
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
        
        define('CODERS__REPOSITORY__DIR',__DIR__);
        
        define('CODERS__REPOSITORY__URL', plugin_dir_url(__FILE__));
        
        require_once(sprintf('%s/classes/resource.class.php',CODERS__REPOSITORY__DIR));
        
        
        if(is_admin()){
            //INITIALIZE ADMIN MANAGEMENT
            
            add_action('admin_enqueue_scripts', function(){
                wp_enqueue_style('coders-repo-admin-style', sprintf('%sadmin/assets/coders-repo.css',CODERS__REPOSITORY__URL));
                wp_enqueue_script('coders-repo-admin-script', sprintf('%sadmin/assets/coders-repo.js',CODERS__REPOSITORY__URL),array('jquery'));
            });
            //add_filter( 'admin_body_class', 'coders-repository' );
            
            require_once( sprintf( '%s/admin/controller.php',__DIR__) );
            
            add_action('admin_menu', function() {
                add_menu_page(
                        __('Repository', 'coders_repository'),
                        __('Repository', 'coders_repository'),
                        'administrator', 'coders-repository',
                        function() {
                            \CODERS\Repository\Admin\Controller::action('dashboard');
                        }, 'dashicons-grid-view'  ,51);
                add_submenu_page(
                        'coders-repository',
                        __('Settings', 'coders_repository'),
                        __('Settings', 'coders_repository'),
                        'administrator','coders-repository-settings',
                        function(){
                            \CODERS\Repository\Admin\Controller::action('settings');
                        });
            }, 100000);
        }
        else{
            //INITIALIZE REDIRECTION RULES
            add_action( 'init' , function(){

                global $wp, $wp_rewrite;

                    //import the regiestered locale's endpoint from the settinsg
                $endpoint = \CodersRepo::ENDPOINT;

                //now let wordpress do it's stuff with the query router
                $wp->add_query_var('template');

                add_rewrite_endpoint($endpoint, EP_ROOT);

                $wp_rewrite->add_rule(
                        sprintf('^/%s/?$', $endpoint),
                        'index.php?template=' . $endpoint, 'bottom');

                //and rewrite
                $wp_rewrite->flush_rules();
            } );
            //INITIALIZE TEMPLATE REDIRECTION
            add_action( 'template_redirect', function( ){

                //check both permalink and page template (validate with locale)
                if (\CodersRepo::queryRoute()) {

                    /* Make sure to set the 404 flag to false, and redirect  to the contact page template. */
                    global $wp_query;
                    //blow up 404 errors here
                    $wp_query->set('is_404', FALSE);
                    //and execute the response
                    
                    $REPO = \CodersRepo::instance();
                    //var_dump($REPO->request());
                    $REPO->download($REPO->request());

                    exit;
                }
            } );
        }
        
        return $this;
    }
    /**
     * @return \CodersRepo
     */
    static final function instance(){
        
        if(is_null(self::$_INSTANCE)){
            self::$_INSTANCE = new \CodersRepo ();
        }
        
        return self::$_INSTANCE;
    }
}

CodersRepo::instance();

/**
 * Setup DB
 */
register_activation_hook(__FILE__, function( ){

    $setup_path = sprintf('%s/setup.php');
    
    if(file_exists($setup_path)){
        
        require( $setup_path );
    }
});