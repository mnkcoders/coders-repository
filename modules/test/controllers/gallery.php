<?php namespace CODERS\ArtPad\Test;
/**
 * 
 */
final class GalleryController extends \CODERS\ArtPad\Response{
    
    /**
     * 
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected function default_action(\CODERS\ArtPad\Request $request) {
        
        var_dump($request);
        
        var_dump( \CODERS\ArtPad\Request::url('test.gallery.open'));
        var_dump( \CODERS\ArtPad\Request::url('test.gallery.create'));
        var_dump( \CODERS\ArtPad\Request::url('test.gallery.export'));
        
        
        return TRUE;
    }
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function open_action( \CODERS\ArtPad\Request $request ){
        var_dump($request);
        printf('Gallery-%s OPEN!!!',$request->ID);
        
        return TRUE;
    }
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function create_action( \CODERS\ArtPad\Request $request ){
        var_dump($request);
        printf('Gallery-%s CREATED!!!',$request->title);
        
        return TRUE;
    }
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function export_action( \CODERS\ArtPad\Request $request ){
        var_dump($request);
        printf('Gallery-%s EXPORTED!!!',$request->ID);
        
        return TRUE;
    }
}