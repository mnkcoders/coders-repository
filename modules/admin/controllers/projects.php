<?php namespace CODERS\Repository\Admin;
/**
 * 
 */
final class ProjectsController extends \CODERS\Repository\Response{

    protected function default_action(\CODERS\Repository\Request $request) {
        
        $project = $this->importModel('admin.project');
        
        $view = $this->importView('admin.projects');
        
        $view->setModel($project)->setLayout('project.list')->display();
        
        return TRUE;
    }

}


