<?php namespace CODERS\ArtPad\Portfolio;

final class MainController extends \CODERS\ArtPad\Response{

    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function default_action(\CODERS\ArtPad\Request $request) {
        
        $portfolio = $this->model('portfolio.project');
        
        $this->view('portfolio')
                ->setModel($portfolio)
                ->setLayout('project.list')
                ->display();
        
        return TRUE;
    }
    
}
    

