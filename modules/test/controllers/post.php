<?php namespace CODERS\ArtPad\Test;
/**
 * 
 */
final class PostController extends \CODERS\ArtPad\Response{
    
    /**
     * 
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected function default_action(\CODERS\ArtPad\Request $request) {
        var_dump($request);
        var_dump( \CODERS\ArtPad\Request::url('test.post.open'));
        var_dump( \CODERS\ArtPad\Request::url('test.post.create'));
        var_dump( \CODERS\ArtPad\Request::url('test.post.export'));
        
        
        return TRUE;
    }
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function open_action( \CODERS\ArtPad\Request $request ){
        var_dump($request);
        printf('Post-%s OPEN!!!',$request->ID);
        return TRUE;
    }
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function create_action( \CODERS\ArtPad\Request $request ){
        var_dump($request);
        printf('Post %s CREATED!!!',$request->title);
        return TRUE;
    }
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function export( \CODERS\ArtPad\Request $request ){
        var_dump($request);
        printf('Post-%s EXPORT!!!',$request->ID);
        return TRUE;
    }
}