<?php namespace CODERS\Repository\Services;

class Mailer{
    
    private $_sender,$_receiver,$_subject,$_content;
    
    private $_headers = array(
        'Content-Type: text/html; charset=UTF-8',
    );
    
    private final function __construct( $from , $to , $subject , $content = '' ) {
        
        $this->_content = $content;
        $this->_sender = $from;
        $this->_receiver = $to;
        $this->_subject = $subject;

        //sprintf('From: %s <%s>',$site,$admin_email);
        //sprintf('Cc: %s <%s>',$name, $email);
        //sprintf('Cc: %s <%s>',$name, $email);
        //$this->header($header);
    }
    /**
     * @param string $header
     * @return CODERS\Repository\Services\Mailer
     */
    public final function header( $header ){
        $this->_headers[  ] = $header;
        return $this;
    }
    /**
     * @param string $content
     * @return \CODERS\Repository\Services\Mailer
     */
    public final function body( $content ){
        
        $this->_content .= $content;
        
        return $this;
    }
    /**
     * @return boolean
     */
    public final function send(){
        
        //add_action('wp_mail_failed', function ($wp_error) {
        //    $fn = ABSPATH . '/mail.log'; // say you've got a mail.log file in your server root
        //    $fp = fopen($fn, 'a');
        //    fputs($fp, "Mailer Error: " . $wp_error->get_error_message() . "\n");
        //    fclose($fp);
        //});

        return wp_mail( $this->_receiver, $this->_subject, $this->_content, $this->_headers );
        
    }
    /**
     * @param string $from
     * @param string $to
     * @param string  $subject
     * @param string $content
     * @return \CODERS\Repository\Services\Mailer
     */
    public static final function mailFrom($from , $to, $subject, $content = '' ){
        
        return new Mailer($from, $to, $subject, $content );
    }
    /**
     * @param string $from
     * @param string $to
     * @param string  $subject
     * @param string $content
     * @return \CODERS\Repository\Services\Mailer
     */
    public static final function systemMail($to, $subject, $content = '' ){

        $from = get_option('admin_email');
        
        $name = get_option('blogname');
        
        $mailer = new Mailer($from, $to, $subject, $content);
        
        return $mailer->header(sprintf('From: %s <%s>',$name,$from));
    }
}


