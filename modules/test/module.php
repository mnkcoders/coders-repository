<?php namespace CODERS\ArtPad\Test;

defined('ABSPATH') or die;
/**
 * 
 */
class TestModule extends \ArtPad{
    
    protected final function __construct() {
        
        $this->include('Models.Account')
                ->include('Models.Tier')
                ->include('Models.Subscription' );
        
        parent::__construct();
    }
    
    public final function run($route = '') {
        
        $account = \CODERS\ArtPad\Account::Load(1);
        $tier = \CODERS\ArtPad\Tier::Load('artpad.copper');
        \CODERS\ArtPad\Subscription::New($account, $tier);
        die;
        
        $sub = \CODERS\ArtPad\Subscription::Load(1);
        
        var_dump($sub->isActive());
        var_dump($sub->getLevel());
        var_dump($sub);
        
        //return parent::run($route);
        die;
    }
}
    