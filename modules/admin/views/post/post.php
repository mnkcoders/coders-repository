<?php namespace CODERS\ArtPad\Admin;
/**
 * 
 */
final class PostView extends \CODERS\ArtPad\View{
    
    
    protected final function displayEdit( $id ){
        print \CODERS\ArtPad\Request::url('admin.post.edit',array('ID'=>$id));
    }
    
    protected final function inputAdjacent(){
        return $this->post_parent;
    }
    /**
     * @return string
     */
    protected final function getFormUrl(){
        return \CODERS\ArtPad\Request::url('admin.post.save',array('ID'=>$this->model()->ID));
    }
}

