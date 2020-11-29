<?php namespace CODERS\ArtPad\Session;
/**
 * 
 */
final class RegisterController extends \CODERS\ArtPad\Response{
    
    /**
     * Send the invite link by email calling the access_action below
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected function default_action(\CODERS\ArtPad\Request $request) {
        
        $email = $request->get('email','');
        $name = $request->get('name','');
        
        if(strlen($email)){
            //send invite link by email
        }
        
        return TRUE;
    }
    /**
     * Set the login and display redirection to dashboard
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function access_action( \CODERS\ArtPad\Request $request ) {
        
        
        var_dump($request);
        return FALSE;
    }
    
}

