<?php namespace CODERS\Repository\Admin;

defined('ABSPATH') or die;
/**
 * 
 */
class AdminModule extends \CodersRepo{
    
    
    protected final function __construct() {
        
        $this->component('Account', 'models' );
        $this->component('Project', 'models' );
        $this->component('Mailer', 'services' );
        
        parent::__construct();

        //register styles and scripts using the helper within the view
        \CODERS\Repository\View::attachStyles(array('style.css'),'admin');
        //\CODERS\Repository\View::attachScripts(array('script.js'=>array()),'admin');
        //add_filter( 'admin_body_class', 'coders-repository' );
        
        //$this->adminMenu();
    }
    /**
     * Describe admin pages
     * @return array
     */
    private final function adminOptions(){

        return array(
                    'main' => __( 'Artist Pad' , 'coders_repository' ),
                    'collection' => __( 'Collections' , 'coders_repository' ),
                    //'projects' => __( 'Projects' , 'coders_repository' ),
                    'accounts' => __( 'Accounts' , 'coders_repository' ),
                    //'subscriptions' => __( 'Subscriptions' , 'coders_repository' ),
                    //'payments' => __( 'Payments' , 'coders_repository' ),
                    'settings' => __( 'Settings' , 'coders_repository' ),
                    'logs' => __( 'Logs' , 'coders_repository' ),
                );
    }
    /**
     * @return boolean
     */
    public final function registerAdminMenu(){
        
        $menu = $this->adminOptions();

        $root = '';
        foreach( $menu as $page => $title ){
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


        return TRUE;
    }
    /**
     * @return boolean
     */
    public final function registerAjax(){
        
        //print json_encode(array('response'=>'OK'));
        \CODERS\Repository\Response::fromAjax('admin.ajax');
        wp_die();
        return TRUE;
    }
    /**
     * 
     */
    private final function adminMenu(){
        
        $menu = $this->adminOptions();
        
        add_action('admin_menu', function() use( $menu ) {
                $root = '';
                foreach( $menu as $page => $title ){
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
     
    }
}
    