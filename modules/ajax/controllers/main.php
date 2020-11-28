<?php namespace CODERS\ArtPad\Ajax;
/**
 * 
 */
final class MainController extends \CODERS\ArtPad\Response{

    protected function default_action(\CODERS\ArtPad\Request $request) {
        
        return $this->ajax($request->input());
    }
}