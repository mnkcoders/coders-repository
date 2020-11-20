<?php namespace CODERS\Repository\Admin;
/**
 * 
 */
final class PaymentsController extends \CODERS\Repository\Response{

    protected function default_action(\CODERS\Repository\Request $request) {
        
        $this->importView('admin.payments')->setLayout('payments')->display();
        
        var_dump($request);
        
        return TRUE;
    }

}


