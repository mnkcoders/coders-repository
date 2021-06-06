<?php namespace CODERS\ArtPad\Admin;
/**
 * 
 */
final class PaymentsController extends \CODERS\ArtPad\Response{

    protected function default_action(\CODERS\ArtPad\Request $request) {
        
        $this->view('admin.payments')->setLayout('payments')->display();
        
        var_dump($request);
        
        return TRUE;
    }

}


