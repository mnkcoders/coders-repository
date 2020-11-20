<?php namespace CODERS\Repository\Admin;
/**
 * 
 */
final class AccountsController extends \CODERS\Repository\Response{

    protected function default_action(\CODERS\Repository\Request $request) {
        
        //var_dump($request);
        
        $account = $this->importModel('admin.account');
        
        $view = $this->importView('admin.accounts');
        
        $view->setModel($account)->setLayout('accounts')->display();
        
        var_dump($request);
        
        return TRUE;
    }

}


