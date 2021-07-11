<?php namespace CODERS\ArtPad\Test;
/**
 * 
 */
final class ProjectController extends \CODERS\ArtPad\Response{
    
    /**
     * 
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected function default_action(\CODERS\ArtPad\Request $request) {
        
        var_dump( \CODERS\ArtPad\Request::url('test.project.create'));
        var_dump( \CODERS\ArtPad\Request::url('test.project.remove'));
        var_dump( \CODERS\ArtPad\Request::url('test.project.publish'));
        
        return TRUE;
    }
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function create_action( \CODERS\ArtPad\Request $request ){
        
        printf('%s CREATED!!!',$request->title );
        
        return TRUE;
    }
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function remove_action( \CODERS\ArtPad\Request $request ){
        
        printf('%s REMOVED!!!',$request->ID);
        
        return TRUE;
    }
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function publish_action( \CODERS\ArtPad\Request $request ){
        
        printf('%s PUBLISHED!!!',$request->ID);
        
        return TRUE;
    }
}