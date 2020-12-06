<?php namespace CODERS\ArtPad\Admin;
/**
 * 
 */
final class CollectionController extends \CODERS\ArtPad\Response{
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function default_action(\CODERS\ArtPad\Request $request) {
        $collection = $this->importModel('admin.collection');
        $collection->parent_id = $request->getInt('id');
        //var_dump($collection);
        //die;
        $this->importView('admin.collection')
                ->setModel($collection)
                ->setLayout('collection')
                ->display();
        return TRUE;
    }
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function remove_action( \CODERS\ArtPad\Request $request ){
        
        $ID = $request->get('ID');
        
        $item = \CODERS\ArtPad\Resource::load($ID);
                    var_dump($item);
        if( $item !== FALSE ){
            var_dump( $item->delete() );
        }
        
        return FALSE;
    }
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function test_action( \CODERS\ArtPad\Request $request ){
        
        $this->importView('admin.collection')
                ->setLayout('collection.new')
                ->display();
        
        return TRUE;
    }
    /**
     * 
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function parent_action( \CODERS\ArtPad\Request $request ){
        
        $ID = $request->getInt('ID');
        
        $parent_id = $request->getInt('parent_id');
        
        $resource = \CODERS\ArtPad\Resource::load($ID);
        
        $response = FALSE !== $resource && $resource->setParent($parent_id);
        
        var_dump($response);
        die;
        
        $ID = $request->getInt('ID');
        $parent_id = $request->getInt('parent_id');
        var_dump($parent_id);
        var_dump($ID);
        //die;
        $resource = \CODERS\ArtPad\Resource::load($ID);
        var_dump($resource);
        if( FALSE !== $resource ){
            $resource->setParent($parent_id);
            
        }
        var_dump($resource);
        
        return FALSE;
    }
    /**
     * 
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function upload_action( \CODERS\ArtPad\Request $request ){
        
        $files = \CODERS\ArtPad\Resource::upload('upload', $request->get('collection','default'));
        $names = array();
        foreach($files as $file ){
            $names[] = $file->name;
        }
        return $this->default_action($request->redirect('admin.collection.default',array('files'=>$names)));
    }
}


