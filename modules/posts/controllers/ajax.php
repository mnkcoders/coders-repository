<?php namespace CODERS\Repository\Posts;
/**
 * 
 */
final class AjaxController extends \CODERS\Repository\Response{
    
    protected function default_action(\CODERS\Repository\Request $request) {
        
        return $this->ajax(array(
            'ts'=>$this->ts(),
            'response' => 'OK',
        ));
    }

}