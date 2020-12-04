<?php namespace CODERS\ArtPad\Portfolio;
/**
 * Public access to the projects
 */
final class PortfolioModule extends \ArtPad{
    
    
    protected final function __construct() {
        
        $this->include('Models.Project');
        
        parent::__construct();
        
    }
    
    public final function run($route = '') {
        
        return parent::run($route);
        

    }
    
}
