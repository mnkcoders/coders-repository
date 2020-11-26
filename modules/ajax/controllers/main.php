<?php namespace CODERS\Repository\Ajax;
/**
 * 
 */
final class MainController extends \CODERS\Repository\Response{

    protected function default_action(\CODERS\Repository\Request $request) {
        
        return $this->ajax($request->input());
    }
}