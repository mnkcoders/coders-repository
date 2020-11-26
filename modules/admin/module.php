<?php namespace CODERS\Repository\Admin;

defined('ABSPATH') or die;
/**
 * 
 */
class AdminModule extends \CodersRepo{
    
    private $_menu = array(
        
    );
    
    protected final function __construct() {
        //register componetns
        $this->component('Account', 'models' );
        $this->component('Project', 'models' );
        $this->component('Mailer', 'services' );
        
        //register admin menu routes
        $this->registerRoute( 'main' , __('Artist Pad','coders_repository'))
                ->registerRoute( 'collection' , __('Collections','coders_repository'))
                ->registerRoute( 'accounts' , __('Accounts','coders_repository'))
                ->registerRoute( 'settings' , __('Settings','coders_repository'))
                ->registerRoute( 'logs' , __('Logs','coders_repository'));

        parent::__construct();

        //register styles and scripts using the helper within the view
        \CODERS\Repository\View::attachStyles(array('style.css'),'admin');
        
        $this->initAdminMenu()->initAjax();
    }
    /**
     * @return string
     */
    private final function rootMenu(){
        
        $keys = array_keys($this->_menu);
        
        return count( $keys ) ? $keys[ 0 ] : FALSE;
    }
    /**
     * 
     * @param type $option
     * @return \CODERS\Repository\Admin\AdminModule
     */
    private final function registerRoute( $option , $title ){
                
        $root = $this->rootMenu();

        $page = strlen( $root ) ?
                sprintf('%s-%s',$root,$option):
                sprintf('%s-%s','coders',$option);

        $this->_menu[ $page ] = array(
            'title' => $title ,
            'route' => sprintf('admin.%s',$option),
            'root' => $root,
        );

        return $this;
    }
    /**
     * @return \CODERS\Repository\Admin\AdminModule
     */
    private final function initAdminMenu(){

        if( count( $this->_menu ) ){
            
            $menu = $this->_menu;

            add_action('admin_menu', function() use( $menu ){
                foreach( $menu as $page => $content ){
                    $route = $content['route'];
                    $title = $content['title'];
                    $root = $content['root'];
                    if( FALSE !== $root ){
                        add_submenu_page(
                                $root, $title, $title,
                                'administrator', $page ,
                                function() use( $route ) {
                                    \CODERS\Repository\Response::fromRoute( $route );
                        });
                    }
                    else{
                        add_menu_page(
                                $title, $title,
                                'administrator', $page,
                                function() use( $route ) {
                                    \CODERS\Repository\Response::fromRoute( $route );
                        }, 'dashicons-art', 51);
                    }
                }
            } , 100000 );
        }
        return $this;
    }
    /**
     * @return \CODERS\Repository\Admin\AdminModule
     */
    private final function initAjax(){
        
        //register private ajax handlers
        add_action( sprintf('wp_ajax_%s_admin', self::ENDPOINT ) , function(){
            //print json_encode(array('response'=>'OK'));
            \CODERS\Repository\Response::fromAjax('admin.ajax');
            wp_die();
        } , 100000 );

        //disable pubilc ajax handlers
        add_action( sprintf('wp_ajax_nopriv_%s_admin', self::ENDPOINT) , 'wp_die', 100000 );   
        
        return $this;
    }
}
    