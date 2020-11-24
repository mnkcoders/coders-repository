<?php namespace CODERS\Repository\Admin;
/**
 * 
 */
final class CollectionModel extends \CODERS\Repository\Model{
    
    /**
     * @param string $resource_id
     * @return string
     */
    protected final function getResource( $resource_id ){
        return \CodersRepo::resourceLink( $resource_id );
    }
    /**
     * @return array
     */
    protected final function getStorageAttribute(){
        
        return \CODERS\Repository\Resource::storage();
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
            \CODERS\Repository\Resource::collection($collection) :
            array();
    }
    /**
     * @return array
     */
    protected final function listCollections(){

        $query = $this->newQuery();
        
        $storage = \CODERS\Repository\Resource::storage();
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




