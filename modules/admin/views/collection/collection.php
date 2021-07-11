<?php namespace CODERS\ArtPad\Admin;
/**
 * 
 */
final class CollectionView extends \CODERS\ArtPad\View{
    
    const PAGE = 'coders-artpad';    
    const MAX_FILE_SIZE = 'coders.repository.max_file_size';
    /**
     * 
     * @return \CODERS\ArtPad\Admin\CollectionView
     */
    public final function display() {
        
        $pathNodes = $this->isPost() ? $this->model()->list_route : array();
        
        foreach( $pathNodes as $id => $title ){
            $this->addNavPath(
                    $title,
                    \CODERS\ArtPad\Request::url('admin.collection',array('ID'=>$id) ) );
        }
        
        return parent::display();
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
     * @param int $id
     * @return string
     */
    protected final function getPostUrl( $id){
        return \CODERS\ArtPad\Request::url('admin.collection.display2',array('ID'=>$id));
    }
    /**
     * @return array
     */
    protected final function listGrid(){
        return array( 2,4,6,8);
    }
    /**
     * @return array
     */
    protected final function listItems(){
        return $this->isPost() ?
                $this->model()->list_children :
                \CODERS\ArtPad\Resource::collection();
    }
    /**
     * @return boolean
     */
    public final function isPost(){
        return $this->getPostId() > 0;
    }
    /**
     * @return string
     */
    protected final function getAttachmentContent(){
        return $this->hasModel() ? $this->model()->attachment : '';
    }
    /**
     * @return int
     */
    protected final function getPostId(){
        return $this->hasModel() ? $this->model()->value('ID') : 0;
    }
    /**
     * @return string
     */
    protected final function getAttachmentUrl(){
        return $this->hasModel() ? $this->model()->attachment_url : '';
    }
    /**
     * @return string
     */
    protected final function getUploadUrl(){
        
        $post_id = $this->getPostId();
        
        return $post_id > 0 ?
                \CODERS\ArtPad\Request::url('admin.collection' , array('ID'=>$post_id)) :
                \CODERS\ArtPad\Request::url('admin.collection') ;
    }
    /**
     * @return int
     */
    protected final function getGridOption(){
        return \ArtPad::getOption('grid');
    }
    /**
     * @return string
     */
    protected final function inputTier( ){
        
        $options = $this->hasModel() ? $this->model()->list_tiers : array();
        
        $value = 0;
        
        return count($options) ?
                self::renderDropDown('tier', $options, $value) :
                self::__HTML('a', array(
                    'href'=> \CODERS\ArtPad\Request::url('admin.main'),
                    'target' => '_self',
                    'class' => 'link'
                ),__('Create a tier','coders_artpad')) ;
    }
}


