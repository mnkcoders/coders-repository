<?php namespace CODERS\ArtPad\Pad;
/**
 * 
 */
final class MainController extends \CODERS\ArtPad\Response{
    /**
     * 
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected function default_action(\CODERS\ArtPad\Request $request) {
        
        $dashboard = $this->model('pad.dashboard');
        
        $this->view('pad.dashboard')->setModel($dashboard)->setLayout('dashboard')->display();
        
        return TRUE;
    }
}




