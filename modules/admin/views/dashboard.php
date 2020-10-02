<?php namespace CODERS\Repository\Admin;
/**
 * 
 */
final class DashboardView extends \CODERS\Repository\View{
    
    const PAGE = 'coders-repository';    
    const MAX_FILE_SIZE = 'coders.repository.max_file_size';
    
    private $_selected = 'default';
    
    protected final function __construct() {
        
        parent::__construct();
        
    }
    /**
     * @param array $resource
     * @return string
     */
    protected final function displayResource( array $resource ){
        //var_dump($resource);
        switch( $resource[ 'type' ] ){
            case 'image/png':
            case 'image/gif':
            case 'image/jpeg':
            case 'image/jpg':
                $link = $this->getResourceLink($resource['public_id']);
                return sprintf('<img class="content media" src="%s" alt="%s" title="%s" />',
                        $link,
                        $resource['name'],
                        $resource['name']);
            case 'text/html':
                return sprintf('<span class="content html">%s</span>',$resource['name']);
            case 'text/plain':
            default:
                return sprintf('<span class="content text">%s</span>',$resource['name']);
        }
    }
    
    /**
     * @return int
     */
    protected final function getMaxFileSize(){
        
        return 255 * 255 * get_option( self::MAX_FILE_SIZE , 50 );
    }
    /**
     * @param array $params
     * @return string
     */
    protected final function getFormAction( array $params = array( ) ){
        
        $url = sprintf('%s?page=%s', get_admin_url( ) , self::PAGE );
        
        if( count ($params)){
            foreach( $params as $var=>$val ){
                $url .= sprintf('&%s=%s',$var,$val);
            }
        }
        
        return $url;
    }
    /**
     * @param string $resource_id
     * @return string
     */
    protected final function getResourceLink( $resource_id ){
        
        return \CodersRepo::resourceLink( $resource_id );
    }
    /**
     * @param string $collection
     * @return array
     */
    protected final function listCollection( $collection = '' ){
        return strlen($collection) ?
            \CODERS\Repository\Resource::collection( $collection ) :
            array();
    }
    /**
     * @return string|FALSE
     */
    protected final function listSelected(){
        
        $collection = $this->listCollection( $this->_selected );

        return $collection;
    }
}

