<?php namespace CODERS\Repository\Admin;
/**
 * 
 */
final class AjaxController extends \CODERS\Repository\Response{
    /**
     * @param \CODERS\Repository\Request $request
     * @return boolean
     */
    protected function default_action(\CODERS\Repository\Request $request) {
        
        return $this->list_collections_action($request);
    }
    /**
     * @param \CODERS\Repository\Request $request
     * @return boolean
     */
    protected final function list_collections_action( \CODERS\Repository\Request $request ){
        
        //$collections = \CODERS\Repository\Resource::storage();
        $repo = $this->importModel('admin.repository');
        
        return $this->ajax( $repo->list_collections );
    }
    /**
     * @param \CODERS\Repository\Request $request
     * @return boolean
     */
    protected function test_action( \CODERS\Repository\Request $request ){
        
        return $this->ajax( $request->input() );
    }
    /**
     * @param \CODERS\Repository\Request $request
     * @return boolean
     */
    protected function collection_action( \CODERS\Repository\Request $request ){
        
        $collection = \CodersRepo::collection($request->get('collection','default'));
        
        return $this->ajax( $collection );
    }
    /**
     * @param \CODERS\Repository\Request $request
     * @return boolean
     */
    protected final function create_collection_action( \CODERS\Repository\Request $request ){
                        
        $collection = preg_replace( '/ / ','' , strtolower($request->get('collection','')) );
        
        if( strlen($collection) ){
            
            return $this->ajax(array('collection' => $collection));
        }
        
        return $this->ajax( array( 'message' => 'Invalid collection name' ) );
    }
    /**
     * @param \CODERS\Repository\Request $request
     * @return boolean
     */
    protected final function upload_action( \CODERS\Repository\Request $request ){
        
        $upload = $request->get('upload');
        $collection = $request->get('collection','default');
        
        $files = \CODERS\Repository\Resource::upload($upload,$collection);
        
        return $this->ajax( $files );
    }
    /**
     * @param \CODERS\Repository\Request $request
     * @return boolean
     */
    protected final function error(\CODERS\Repository\Request $request) {

        return $this->ajax(array(
            'action' => $request->action(),
            'data' => $request->input(),
        ), 400);
    }
}