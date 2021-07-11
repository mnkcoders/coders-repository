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
        return $this->display_action($request);
    }
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function display_action( \CODERS\ArtPad\Request $request ){
        
        $ID = $request->getInt('ID');
        
        $collection = $ID > 0 ?
                \CODERS\ArtPad\Resource::load($ID) :
                $this->model('admin.collection');

        $this->view('admin.collection')
                ->setModel($collection)
                //->setLayout('collection')
                ->setLayout('new')
                ->display();
        return TRUE;
    }
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function display2_action( \CODERS\ArtPad\Request $request ){
        //$collection = $this->model('admin.collection');
        $collection = \CODERS\ArtPad\Resource::load($request->getInt('ID'));
        //$collection->post( $request->getInt('ID') );
        $this->view('admin.collection')
                ->setModel($collection)
                ->setLayout('new')
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
        
        $this->view('admin.collection')
                ->setLayout('new')
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


