<?php namespace CODERS\ArtPad\Portfolio;

final class ProjectController extends \CODERS\ArtPad\Response{

    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function default_action(\CODERS\ArtPad\Request $request) {
        
        $ID = $request->get('ID');
        
        $item = $request->get('item','');
        
        //$project = \CODERS\ArtPad\Project::load($ID);
        $project = $this->model('portfolio.project',array('ID'=>$ID,'item' => $item));
        
        $display = $this->view('portfolio.project');
        
        if( FALSE !== $project ){
            
            $display->setModel($project)->setLayout('project.view')->display();
        }
        else{
            $display->setLayout('project.invalid')->display();
        }
        
        return TRUE;
    }
}
    

