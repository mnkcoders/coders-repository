<?php namespace CODERS\Repository\Admin;
/**
 * 
 */
final class SubscriptionsController extends \CODERS\Repository\Response{

    protected function default_action(\CODERS\Repository\Request $request) {
        
        $this->importView('admin.subscriptions')->setLayout('subscriptions')->display();
        
        var_dump($request);
        
        return TRUE;
    }

}


