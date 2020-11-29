<?php namespace CODERS\ArtPad\Pad;
/**
 * 
 */
final class AjaxController extends \CODERS\ArtPad\Response{
    
    protected function default_action(\CODERS\ArtPad\Request $request) {
        
        return $this->ajax($request->input());
    }
}


