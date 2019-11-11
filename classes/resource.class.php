<?php namespace CODERS\Repository;
/**
 * 
 */
final class Resource{
    
    private $_id;
    
    private final function __construct( $id ) {
        
    }
    /**
     * @return boolean
     */
    public final function delete(){
        
        return self::remove($this->_id);
    }
    
    public static final function remove( $id ){
        
        return TRUE;
    }
    
    public static final function upload( ){
        
    }
    /**
     * @param string $id
     * @return \CodersRepoSource
     */
    public static final function import( $id ){
        
        
        return new Resource( $id );
    }
}