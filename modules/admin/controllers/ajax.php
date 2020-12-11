<?php namespace CODERS\ArtPad\Admin;
/**
 * 
 */
final class AjaxController extends \CODERS\ArtPad\Response{
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected function default_action(\CODERS\ArtPad\Request $request) {
        
        return $this->list_collections_action($request);
    }
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function list_collections_action( \CODERS\ArtPad\Request $request ){
        
        //$collections = \CODERS\ArtPad\Resource::storage();
        $repo = $this->importModel('admin.repository');
        
        return $this->ajax( $repo->list_collections );
    }
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected function test_action( \CODERS\ArtPad\Request $request ){
        
        return $this->ajax( $request->input() );
    }
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected function collection_action( \CODERS\ArtPad\Request $request ){
        
        $collection = \CODERS\ArtPad\Resource::collection( $request->getInt('id' ) );
        
        return $this->ajax( $collection );
    }
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected function item_action( \CODERS\ArtPad\Request $request ){
        
        $item = \CODERS\ArtPad\Resource::load($request->getInt('id'));
        
        if( FALSE !==  $item ){
            
            return $this->ajax( $item->meta() );
        }
        
        return $this->ajax(array('error' => 'Invalid Resource'));
    }
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected function attach_action( \CODERS\ArtPad\Request $request ){
        
        $ID = $request->getInt('ID');
        
        $parent_id = $request->getInt('parent_id');
        
        $resource = \CODERS\ArtPad\Resource::load($ID);
        
        $response = FALSE !== $resource && $resource->setParent($parent_id);
        
        return $this->ajax($response);
    }
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected function up_action( \CODERS\ArtPad\Request $request ){
        $id = $request->getInt('ID');
        
        $item = \CODERS\ArtPad\Resource::load($id );
        
        return $this->ajax( FALSE !== $item && $item->moveUp() );
    }
    /**
     * 
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected function path_action(\CODERS\ArtPad\Request $request){
        $item = \CODERS\ArtPad\Resource::load( $request->getInt('id' ) );
        $response = array();
        if( FALSE !== $item ){
            $response = $item->tree();
        }
        return $this->ajax($response);
    }
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function upload_action( \CODERS\ArtPad\Request $request ){
        
        $upload = $request->get('upload');
        $collection = $request->get('collection','default');
        
        $files = \CODERS\ArtPad\Resource::upload($upload,$collection);
        
        return $this->ajax( $files );
    }
    /**
     * 
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function remove_action( \CODERS\ArtPad\Request $request ){
        
        $ID = $request->getInt('ID');
        
        $item = \CODERS\ArtPad\Resource::load($ID);
        
        $removed = FALSE;
        
        if( FALSE !== $item ){
            $removed = $item->delete();
        }
        
        return $this->ajax( $removed );
    }
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function set_grid_action( \CODERS\ArtPad\Request $request ){
        
        $size = $request->getInt('size');
        //2,3,4,6,8
        $success = \ArtPad::setOption('grid', $size);
        
        return $this->ajax($success ? $size : 0);
    }
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function get_grid_action( \CODERS\ArtPad\Request $request ){
        
        //2,3,4,6,8
        $size = \ArtPad::getOption( 'grid' , 4 );

        return $this->ajax( $size );
    }
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function error(\CODERS\ArtPad\Request $request) {

        return $this->ajax(array(
            'action' => $request->action(),
            'data' => $request->input(),
        ), 400);
    }
}