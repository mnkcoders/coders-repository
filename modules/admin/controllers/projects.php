<?php namespace CODERS\ArtPad\Admin;
/**
 * 
 */
final class ProjectsController extends \CODERS\ArtPad\Response{

    protected function default_action(\CODERS\ArtPad\Request $request) {
        
        $project = $this->model('admin.project');
        
        $view = $this->view('admin.projects');
        
        $view->setModel($project)->setLayout('list')->display();
        
        return TRUE;
    }

}


