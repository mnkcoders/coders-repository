<?php namespace CODERS\ArtPad\Admin;
/**
 * 
 */
final class CollectionView extends \CODERS\ArtPad\View{
    
    const PAGE = 'coders-artpad';    
    const MAX_FILE_SIZE = 'coders.repository.max_file_size';
    
    private $_selected = 'default';

    public final function display() {
        
        return parent::display()->attachScript('script.js');
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
                return parent::__HTML( 'img', array(
                    'class' => 'content media',
                    'src' => $link,
                    'alt' => $resource['name'],
                    'title' => $resource['name']
                ) );
            case 'text/html':
                return self::__HTML('span', array('class'=>'content html'), $resource['name']);
            case 'text/plain':
            default:
                return self::__HTML('span', array('class'=>'content text'), $resource['name']);
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
        return \CODERS\ArtPad\Resource::link($resource_id);
    }
    /**
     * @param string $collection
     * @return array
     */
    protected final function listCollection( $collection = '' ){
        return strlen($collection) ?
            \CODERS\ArtPad\Resource::collection( $collection ) :
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


