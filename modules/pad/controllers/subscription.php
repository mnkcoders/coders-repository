<?php namespace CODERS\ArtPad\Pad;
/**
 * 
 */
final class SubscriptionController extends \CODERS\ArtPad\Response{
    
    protected function default_action(\CODERS\ArtPad\Request $request) {
        
        var_dump($request);
        
        return FALSE;
    }
}


