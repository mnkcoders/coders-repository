<?php namespace CODERS\Repository\Admin;
/**
 * 
 */
final class AjaxController extends \CODERS\Repository\Response{
    
    protected function default_action(\CODERS\Repository\Request $request) {
        
        print $this->ajax(array(
            'ts' => $this->ts(),
            'response'=>'OK'
            ));
        
        return TRUE;
    }

}