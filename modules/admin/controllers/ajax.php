<?php namespace CODERS\Repository\Admin;
/**
 * 
 */
final class AjaxController extends \CODERS\Repository\Response{
    
    protected function default_action(\CODERS\Repository\Request $request) {
        
        $repo = \CODERS\Repository\Model::create('admin.repository');
        
        return $this->ajax( $repo->list_collections );
    }
    
    protected function collection_action( \CODERS\Repository\Request $request ){
        
        $collection = \CodersRepo::collection($request->get('collection','default'));
        
        
        return $this->ajax($collection);
    }

}