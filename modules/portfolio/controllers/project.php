<?php namespace CODERS\ArtPad\Portfolio;

final class ProjectController extends \CODERS\ArtPad\Response{

    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function default_action(\CODERS\ArtPad\Request $request) {
        
        $ID = $request->getInt('ID');
        
        $project = \CODERS\ArtPad\Project::load($ID);
        
        $display = $this->importView('portfolio.project');
        
        if( FALSE !== $project ){
            
            $display->setModel($project)->setLayout('project.view')->display();
        }
        else{
            $display->setLayout('project.invalid')->display();
        }
        
        return TRUE;
    }
    
}
    

