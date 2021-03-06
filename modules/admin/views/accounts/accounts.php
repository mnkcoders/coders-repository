<?php namespace CODERS\ArtPad\Admin;
/**
 * 
 */
final class AccountsView extends \CODERS\ArtPad\View{

    public final function display() {
        
        return parent::display()->attachScript('test.js');
    }
    /**
     * @return array
     */
    protected final function listStatus(){
        
        return \CODERS\ArtPad\Account::listStatus();
    }
    
    protected final function displayStatus( $status = 0 ){
        
        $list = $this->listStatus();
        
        return array_key_exists($status, $list) ? $list[ $status ] : __('Invalid','coders_artpad');
    }
    /**
     * @param int $account_id
     * @return string
     */
    protected final function getAccountUrl( $account_id ){
        return \CODERS\ArtPad\Request::url('admin.accounts.view',array('ID'=>$account_id));
    }
    /**
     * @return string
     */
    protected final function getFormUrl(){
        return \CODERS\ArtPad\Request::url('admin.accounts');
    }
    /**
     * @return string
     */
    protected final function getNewFormUrl(){
        return \CODERS\ArtPad\Request::url('admin.accounts.create');
    }
    /**
     * @return string
     */
    protected final function getSaveFormUrl(){
        return \CODERS\ArtPad\Request::url('admin.accounts.save');
    }
    
    protected final function getMainUrl(){
        return \CODERS\ArtPad\Request::url('admin.accounts');
    }
    
    protected final function getSubscriptionUrl(){
        return \CODERS\ArtPad\Request::url('admin.accounts');
    }
    /**
     * @return string
     */
    protected final function getStatusButton(){
        
        $items = \CODERS\ArtPad\Account::listStatus();
        
        $status = $this->hasModel() ? $this->model()->status : '';

        return self::renderDropDown('status', $items, $status);
    }
    /**
     * 
     * @return type
     */
    protected final function inputSubscription(){
        
        if( $this->hasModel() ){
            
            $subscriptions = $this->model()->list_subscriptions;

            $items = array( '' => __('Subscribe to','coders_artpad'));

            foreach( $subscriptions as $key => $label ){
                $items[] = self::__HTML('option', array('name'=>$key), $label);
            }

            return self::__HTML('select', array(), $items );
        }
        //append link? ;)
        return self::__HTML('span',
                array('class' =>'warning'),
                __('No tier defined','coders_artpad'));
    }
}




