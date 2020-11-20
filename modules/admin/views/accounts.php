<?php namespace CODERS\Repository\Admin;
/**
 * 
 */
final class AccountsView extends \CODERS\Repository\View{

    public final function display() {
        
        return parent::display()->attachScript('test.js');
    }
    /**
     * @return array
     */
    protected final function listStatus(){
        
        return \CODERS\Repository\Account::listStatus();
    }
    
    protected final function displayStatus( $status = 0 ){
        
        $list = $this->listStatus();
        
        return array_key_exists($status, $list) ? $list[ $status ] : __('Invalid','coders_repository');
    }
    /**
     * @param int $account_id
     * @return string
     */
    protected final function getAccountUrl( $account_id ){
        return sprintf('#%s',$account_id);
    }
}