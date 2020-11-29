<?php namespace CODERS\ArtPad\Session;
/**
 * 
 */
final class LoginController extends \CODERS\ArtPad\Response{
    
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected function default_action(\CODERS\ArtPad\Request $request) {

        return $this->invite_action($request);
    }
    /**
     * Send the invite link by email calling the access_action below
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected function invite_action(\CODERS\ArtPad\Request $request) {
        
        $email = $request->get('email','');
        
        $account = \CODERS\ArtPad\Account::LoadByEmail($email, TRUE);
        
        //validate email and check in the DB!!!
        if( FALSE !== $account ){
            
            //send invite link by email
            //account.login.open?link=ABC1234
            printf('<p>Send invite link to AccountID.%s</p>', strval($account));
            //var_dump($account);
        }
        else{
            printf('<p>Failed to login with %s</p>', $email );
        }
        
        return TRUE;
    }
    /**
     * Set the login and display redirection to dashboard
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function open_action( \CODERS\ArtPad\Request $request ) {
        $link = $request->get('link','');
        if( strlen($link) ){
            $invite = \CODERS\ArtPad\Invite::load($link );
            if( FALSE !== $invite && $invite->activate( ) ){
                $account = $invite->account();
                if( FALSE !== $account ){
                    //create new session for this account
                    $session = $account->createSession();
                    if( FALSE !== $session ){
                        $this->importView('session.main')->setModel($account)->setLayout('logged')->display();
                    }
                }
            }
        }

        $this->importView('session.main')->setLayout('invalid.link')->display();

        return TRUE;
    }
    
}



