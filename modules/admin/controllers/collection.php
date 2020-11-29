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
        $model = $this->importModel('admin.collection');
        $view = $this->importView('admin.collection');
        $view->setModel($model)->setLayout('collection')->display();
        return TRUE;
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


