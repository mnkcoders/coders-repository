<?php namespace CODERS\ArtPad\Admin;
/**
 * 
 */
final class PostController extends \CODERS\ArtPad\Response{
    /**
     * @var \CODERS\ArtPad\Admin\PostModel
     */
    private $_cache = null;
    /**
     * 
     * @param string $model
     * @param array $data
     * @return \CODERS\ArtPad\Model
     */
    protected final function model($model, array $data = array()) {
        if(is_null($this->_cache)){
            $this->_cache = parent::model($model,$data);
        }
        else{
            $this->_cache->import($data);
        }
        
        return $this->_cache;
    }
    
    protected function default_action(\CODERS\ArtPad\Request $request) {
        
        $collection = $this->model('admin.post');
        
        //var_dump( $post->listPosts() );
        
        $this->view('admin.post')
                ->setModel($collection)
                ->setLayout('list')
                ->display();
        
        return TRUE;
    }
    
    protected function save_action( \CODERS\ArtPad\Request $request ){

        $ID = $request->getInt('ID');
        
        $post = $this->model('admin.post',$request->input());
        
        if( FALSE !== $post ){
            $post->save();
        }
        
        return $this->edit_action($request->redirect('admin.edit',array('ID' => $ID ) ) );
    }
    
    protected function edit_action( \CODERS\ArtPad\Request $request ){
        
        $ID = $request->getInt('ID');
        
        $post = $this->model('admin.post',array('ID' => $ID ) );
        
        //var_dump($post);
        $this->view('admin.post')->setModel($post)->setLayout('form')->display();
        
        return TRUE;
    }
    
    protected function remove_action( \CODERS\ArtPad\Request $request ){
        
        
        
        return TRUE;
    }

}


