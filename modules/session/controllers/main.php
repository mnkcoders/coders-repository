<?php namespace CODERS\ArtPad\Session;
/**
 * 
 */
final class MainController extends \CODERS\ArtPad\Response{
    
    
    protected function default_action(\CODERS\ArtPad\Request $request) {
        
        $ID = $request->getInt('ID');
        
        if( $ID === 0 ){
            $ID = 1;
        }
        
        print 'Make new session for ID '. $ID;
        
        $session = \CODERS\ArtPad\Session::New($ID);
        
        
        var_dump($session);
        
        return true;
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

