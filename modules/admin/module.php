<?php namespace CODERS\ArtPad\Admin;

defined('ABSPATH') or die;
/**
 * 
 */
class AdminModule extends \ArtPad{
    
    private $_menu = array(
        
    );
    
    protected final function __construct() {
        //register componetns
        $this->include('Models.Account' )
                ->include('Models.Tier' )
                ->include('Services.Mailer' );
        
        //register admin menu routes
        $this->registerRoute( self::ENDPOINT , __('Artist Pad','coders_artpad'))
                ->registerRoute( 'collection' , __('Collections','coders_artpad'))
                ->registerRoute( 'accounts' , __('Accounts','coders_artpad'))
                ->registerRoute( 'settings' , __('Settings','coders_artpad'))
                ->registerRoute( 'logs' , __('Logs','coders_artpad'));

        parent::__construct();

        //register styles and scripts using the helper within the view
        \CODERS\ArtPad\View::attachStyles(array('style.css'),'admin');
        \CODERS\ArtPad\View::attachScripts(array('client.js'=>array()),'admin');
        \CODERS\ArtPad\View::attachScripts(array('collections.js'=>array()),'admin');
        
        $this->initAdminMenu();//->initAjax();
    }
    /**
     * @return string
     */
    private final function root(){
        
        $keys = array_keys($this->_menu);
        
        return count( $keys ) ? $keys[ 0 ] : FALSE;
    }
    /**
     * 
     * @param type $option
     * @return \CODERS\ArtPad\Admin\AdminModule
     */
    private final function registerRoute( $option , $title ){
                
        $root = $this->root();

        $page = strlen( $root ) ?
                sprintf('%s-%s',$root,$option):
                $option;

        $this->_menu[ $page ] = array(
            'title' => $title ,
            'route' => sprintf('admin.%s',$option !== self::ENDPOINT ? $option : 'main' ),
            'root' => $root,
        );

        return $this;
    }
    /**
     * @return \CODERS\ArtPad\Admin\AdminModule
     * @global array $submenu
     */
    private final function initAdminMenu(){
        global $submenu;
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
                                    \CODERS\ArtPad\Response::Route( $route );
                        });
                    }
                    else{
                        add_menu_page(
                                $title, $title,
                                'administrator', $page,
                                function() use( $route ) {
                                    \CODERS\ArtPad\Response::Route( $route );
                        }, 'dashicons-art', 51);
                    }
                }
                //static posts
                add_submenu_page(
                        $this->root(),
                        __('Projects (post)','coders_artpad'),
                        __('Projects (post)','coders_artpad'),
                        'administrator',
                        'edit.php?post_type=artpad_project');
                add_submenu_page(
                        $this->root(),
                        __('Collection (post)','coders_artpad'),
                        __('Collection (post)','coders_artpad'),
                        'administrator',
                        'edit.php?post_type=artpad_post');
            } , 100000 );
        }
        return $this;
    }
}
    