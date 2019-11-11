<?php defined('ABSPATH') or die;
/*******************************************************************************
 * Plugin Name: Coders Repository
 * Plugin URI: https://coderstheme.org
 * Description: Resource Access Gateway and Manager
 * Version: 1.0.0
 * Author: Coder01
 * Author URI: 
 * License: GPLv2 or later
 * Text Domain: coders_framework
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
        
        $request_id = filter_input(INPUT_GET, 'coders_repo_request');
        
        return !is_null($request_id) ? $request_id : FALSE;
    }
    
    
    public final function import( $id ){
        
        return var_dump($id);
    }
    /**
     * 
     * @return \CodersRepo
     */
    private final function init(){
        
        define('CODERS__REPOSITORY__DIR',__DIR__);
        //define('CODERS__REPOSITORY__URL',get_plu);
        require_once(sprintf('%s/classes/resource.class.php',CODERS__REPOSITORY__DIR));
        
        
        if(is_admin()){
            //INITIALIZE ADMIN MANAGEMENT
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

                    var_dump(\CodersRepo::instance());

                    //then terminate app and wordpressresponse
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

\CodersRepo::instance();
