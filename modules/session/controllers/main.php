<?php namespace CODERS\Repository\Session;
/**
 * 
 */
final class MainController extends \CODERS\Repository\Response{
    
    
    protected function default_action(\CODERS\Repository\Request $request) {
        
        $ID = $request->getInt('ID');
        
        if( $ID === 0 ){
            $ID = 1;
        }
        
        print 'Make new session for ID '. $ID;
        
        $session = \CODERS\Repository\Session::New($ID);
        
        
        var_dump($session);
        
        return true;
    }
    
    public function create_action( \CODERS\Repository\Request $request ) {
        
        $session = \CODERS\Repository\Session::New(1);
        
        var_dump($session);
        
        return true;
    }

    public function resume_action( \CODERS\Repository\Request $request ) {
        
        $ID = $request->get('ID','');
        
        if(strlen($ID)){
            
            $session = \CODERS\Repository\Session::Resume($ID);

            if( FALSE !== $session ){
                $account = $session->account();
                var_dump($account);
                var_dump($session->isActive());
            }
        
        }

        return true;
    }

}

