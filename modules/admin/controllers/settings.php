<?php namespace CODERS\ArtPad\Admin;
/**
 * 
 */
final class SettingsController extends \CODERS\ArtPad\Response{

    protected function default_action(\CODERS\ArtPad\Request $request) {

        $settings = $this->importModel('admin.settings');
        
        $this->importView('admin.settings')->setModel($settings)->setLayout('settings')->display();
        
        return TRUE;
    }
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected function test_email_action( \CODERS\ArtPad\Request $request ){
        
        $to = get_option('admin_email');
        
        $message = 'Mailer Service Test';
        
        $sent = \CODERS\ArtPad\Services\Mailer::systemMail(
                $to,
                $message,
                $message)->send();
        
        $notify_message = $sent ?
                __('Email sent to','coders_artpad') . ' ' .$to :
                __('Error sending the email','coders_artpad');

        $notify_type = $sent ? parent::NOTICE_SUCCESS : parent::NOTICE_ERROR;
        
        return $this->notify($notify_message, $notify_type)->default_action($request);
    }
}