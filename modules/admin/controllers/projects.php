<?php namespace CODERS\ArtPad\Admin;
/**
 * 
 */
final class ProjectsController extends \CODERS\ArtPad\Response{

    protected function default_action(\CODERS\ArtPad\Request $request) {
        
        $project = $this->importModel('admin.project');
        
        $view = $this->importView('admin.projects');
        
        $view->setModel($project)->setLayout('project.list')->display();
        
        return TRUE;
    }

}


