<?php namespace CODERS\ArtPad\Admin;
/**
 * 
 */
final class CollectionModel extends \CODERS\ArtPad\Model{
    
    /**
     * @param string $resource_id
     * @return string
     */
    protected final function getResource( $resource_id ){
        return \CODERS\ArtPad\Resource::link($resource_id);
    }
    /**
     * @return array
     */
    protected final function getStorageAttribute(){
        
        return \CODERS\ArtPad\Resource::storage();
    }
    /**
     * @return string|FALSE
     */
    protected final function getSelectedAttribute(){
        return array_key_exists('collection', $this->_attributes) ?
                $this->_attributes['collection'] :
                FALSE;
    }
    /**
     * @param string $collection
     * @return array
     */
    protected final function getCollection( $collection ){
        return strlen($collection) ?
            \CODERS\ArtPad\Resource::collection($collection) :
            array();
    }
    /**
     * @return array
     */
    protected final function listCollections(){

        $query = $this->newQuery();
        
        $storage = \CODERS\ArtPad\Resource::storage();
        $projects = $query->select( 'project' , array('ID','title') , array() ,  'ID' );
        $output = array();
        
        foreach( $storage as $collection ){
            $output[ $collection ] = array_key_exists($collection, $projects) ?
                    $storage[ $collection ] = $projects[$collection]['title'] :
                    $collection;
        }
        
        return $output;
    }
}




