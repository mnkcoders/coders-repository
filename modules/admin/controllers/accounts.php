<?php namespace CODERS\ArtPad\Admin;
/**
 * 
 */
final class AccountsController extends \CODERS\ArtPad\Response{

    protected function default_action(\CODERS\ArtPad\Request $request) {
        //var_dump($request);
        $account = $this->importModel('admin.account');
        
        $view = $this->importView('admin.accounts');
        
        $view->setModel($account)->setLayout('account.list')->display();
        
        
        return TRUE;
    }
    
    protected function view_action( \CODERS\ArtPad\Request $request ){
        
        $account = \CODERS\ArtPad\Account::Load($request->get('ID',0));
        
        $view = $this->importView('admin.accounts');
        
        if( FALSE !== $account ){
            //mask the account model with a local model
            $form = $this->importModel('admin.account');
            
            $form->import($account);
        
            $view->setModel($form)->setLayout('account.view')->display();
            
            return TRUE;
        }
        else{
            $view->setLayout('account.invalid')->display();
        }
        
        return FALSE;
    }
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function create_action( \CODERS\ArtPad\Request $request ){
        
        $form = $this->importModel('admin.account' , $request->input( ) );
        
        if( $form->validateData() ){
            
            $account = \CODERS\ArtPad\Account::New($form->values());

            if( FALSE !== $account ){
                
                $this->importView('admin.accounts')
                        ->setModel($account)
                        ->setLayout('account.view')
                        ->display();
                
                return TRUE;
            }
            else{
                //$form->error_
            }
        }
        else{
                //var_dump($form);
        }
        
        $this->importView('admin.accounts')
                ->setModel($form)
                ->setLayout('account.list')
                ->display();
        
        
        return FALSE;
    }
}




