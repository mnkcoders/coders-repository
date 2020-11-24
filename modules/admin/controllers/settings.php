<?php namespace CODERS\Repository\Admin;
/**
 * 
 */
final class SettingsController extends \CODERS\Repository\Response{

    protected function default_action(\CODERS\Repository\Request $request) {

        //$settings = $this->importModel('admin.settings');
        
        $this->importView('admin.settings')->display();
        
        return TRUE;
    }
    /**
     * @param \CODERS\Repository\Request $request
     * @return boolean
     */
    protected function test_email_action( \CODERS\Repository\Request $request ){
        
        $to = get_option('admin_email');
        
        $message = 'Mailer Service Test';
        
        $sent = \CODERS\Repository\Services\Mailer::systemMail(
                $to,
                $message,
                $message)->send();
        
        $notify_message = $sent ?
                __('Email sent to','coders_repository') . ' ' .$to :
                __('Error sending the email','coders_repository');

        $notify_type = $sent ? parent::NOTICE_SUCCESS : parent::NOTICE_ERROR;
        
        return $this->notify($notify_message, $notify_type)->default_action($request);
    }
}