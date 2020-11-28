<?php namespace CODERS\ArtPad\Admin;
/**
 * 
 */
final class SubscriptionsController extends \CODERS\ArtPad\Response{

    protected function default_action(\CODERS\ArtPad\Request $request) {
        
        $this->importView('admin.subscriptions')->setLayout('subscriptions')->display();
        
        var_dump($request);
        
        return TRUE;
    }

}


