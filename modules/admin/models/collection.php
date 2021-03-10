<?php namespace CODERS\ArtPad\Admin;
/**
 * 
 */
final class CollectionModel extends \CODERS\ArtPad\Model{
    /**
     * @param array $data
     */
    protected final function __construct(array $data = array()) {
        
        $this->define('ID',self::TYPE_NUMBER,array('value'=>0))
            ->define('parent_id',self::TYPE_NUMBER,array('value'=>0));
        
        parent::__construct($data);
    }
    /**
     * @param int $id
     * @return array
     */
    protected final function getPost( $id ){
        
        if(is_array($id)){
            $id = $id[0];
        }
        
        $post = parent::newQuery()->select('post','*',array('ID'=>$id));
        return count($post) ? $this->import($post[0]) : $this;
    }
    /**
     * @return array
     */
    protected final function getTree( ){
        $resource = \CODERS\ArtPad\Resource::load( 9 );
        return FALSE !== $resource ? $resource->tree() : array();
    }
    /**
     * @return array
     */
    //protected final function listItems(){
    //    return \CODERS\ArtPad\Resource::collection();
    //}
    /**
     * @param string $resource_id
     * @return string
     */
    //protected final function getResource( $resource_id ){
    //    return \CODERS\ArtPad\Resource::link($resource_id);
    //}
    /**
     * @return array
     */
    //protected final function getStorageAttribute(){
    //    return \CODERS\ArtPad\Resource::storage();
    //}
    /**
     * @return array
     */
    /*protected final function listCollections(){

        $query = $this->newQuery();
        
        $storage = \CODERS\ArtPad\Resource::storage();
        $projects = $query->select( 'project' , array('ID','title') , array() , 'ID' , 'ID' );
        $output = array();
        
        foreach( $storage as $collection ){
            $output[ $collection ] = array_key_exists($collection, $projects) ?
                    $storage[ $collection ] = $projects[$collection]['title'] :
                    $collection;
        }
        
        return $output;
    }*/
}




