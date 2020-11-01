<?php namespace CODERS\Repository\Admin;
/**
 * 
 */
final class AjaxController extends \CODERS\Repository\Response{
    
    protected function default_action(\CODERS\Repository\Request $request) {
        
        return $this->list_collections_action($request);
    }
    
    protected final function list_collections_action( \CODERS\Repository\Request $request ){
        
        $collections = \CODERS\Repository\Resource::storage();
        
        return $this->ajax( $collections );
    }
    
    protected function test_action( \CODERS\Repository\Request $request ){
        
        return $this->ajax( $request->input() );
    }
    
    protected function collection_action( \CODERS\Repository\Request $request ){
        
        $collection = \CodersRepo::collection($request->get('collection','default'));
        
        return $this->ajax($collection);
    }
    
    protected final function create_collection_action( \CODERS\Repository\Request $request ){
                        
        $collection = preg_replace( '/ / ','' , strtolower($request->get('collection','')) );
        
        if( strlen($collection) ){
            
            return $this->ajax(array('collection' => $collection));
        }
        
        return $this->ajax( array( 'message' => 'Invalid collection name' ) );
    }

    protected final function error(\CODERS\Repository\Request $request) {

        return $this->ajax(array(
            'action' => $request->action(),
            'data' => $request->input(),
        ), 400);
    }
}