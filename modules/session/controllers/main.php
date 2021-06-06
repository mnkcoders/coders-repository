<?php namespace CODERS\ArtPad\Session;
/**
 * 
 */
final class MainController extends \CODERS\ArtPad\Response{
    
    
    protected function default_action(\CODERS\ArtPad\Request $request) {
        
        $this->view('session.main')->setLayout('login')->display();
        
        return TRUE;
    }
    
    public function create_action( \CODERS\ArtPad\Request $request ) {
        
        $session = \CODERS\ArtPad\Session::New(1);
        
        var_dump($session);
        
        return true;
    }

    public function resume_action( \CODERS\ArtPad\Request $request ) {
        
        $ID = $request->get('ID','');
        
        if(strlen($ID)){
            
            $session = \CODERS\ArtPad\Session::Resume($ID);

            if( FALSE !== $session ){
                $account = $session->account();
                var_dump($account);
                var_dump($session->isActive());
            }
        
        }

        return true;
    }

}

