<?php namespace CODERS\ArtPad\Admin;
/**
 * 
 */
final class AccountsController extends \CODERS\ArtPad\Response{

    protected function default_action(\CODERS\ArtPad\Request $request) {
        //var_dump($request);
        $account = $this->model('admin.account');
        $view = $this->view('admin.accounts');
        $view->setModel($account)->setLayout('list')->display();
        
        return TRUE;
    }
    
    protected function view_action( \CODERS\ArtPad\Request $request ){
        
        $account = \CODERS\ArtPad\Account::Load($request->get('ID',0));
        
        $view = $this->view('admin.accounts');
        
        if( FALSE !== $account ){
            //mask the account model with a local model
            $form = $this->model('admin.account');
            
            $form->import($account->listValues());
        
            $view->setModel($form)->setLayout('account')->display();
            
            return TRUE;
        }
        else{
            $view->setLayout('invalid')->display();
        }
        
        return FALSE;
    }
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function create_action( \CODERS\ArtPad\Request $request ){
        
        $form = $this->model('admin.account' , $request->input( ) );
        
        if( $form->validateData() ){
            
            $account = \CODERS\ArtPad\Account::New($form->listValues());

            if( FALSE !== $account ){
                
                $this->view('admin.accounts')
                        ->setModel($account)
                        ->setLayout('account')
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
        
        $this->view('admin.accounts')
                ->setModel($form)
                ->setLayout('list')
                ->display();
        
        
        return FALSE;
    }
}




