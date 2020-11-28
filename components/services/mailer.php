<?php namespace CODERS\ArtPad\Services;

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
     * @return \CODERS\ArtPad\Services\Mailer
     */
    private final function logError( ){
        add_action('wp_mail_failed', function ($wp_error) {
            $log = sprintf('%s/mail.log',ABSPATH );
            $fp = fopen($log, 'a');
            $ts = date('Y-m-d H:i:s');
            fputs($fp, sprintf("[%s] Error: %s\n",$ts, $wp_error->get_error_message()));
            fclose($fp);
        });
        return $this;
    }
    /**
     * @return \CODERS\ArtPad\Services\Mailer
     */
    private final function prepareSender(){
        $from = $this->_sender;
        $from_name = 'ArtistPad Test Site';
        add_filter('wp_mail_from', function() use( $from ){ return $from; });
        add_filter('wp_mail_from_name', function() use( $from_name ) { return $from_name; });
        return $this;
    }
    /**
     * @param string $header
     * @return CODERS\ArtPad\Services\Mailer
     */
    public final function header( $header ){
        $this->_headers[  ] = $header;
        return $this;
    }
    /**
     * @param string $content
     * @return \CODERS\ArtPad\Services\Mailer
     */
    public final function body( $content ){
        
        $this->_content .= $content;
        
        return $this;
    }
    /**
     * @return boolean
     */
    public final function send(){

        $this->prepareSender()->logError();
        
        return wp_mail( $this->_receiver, $this->_subject, $this->_content, $this->_headers );
    }
    /**
     * @param string $from
     * @param string $to
     * @param string  $subject
     * @param string $content
     * @return \CODERS\ArtPad\Services\Mailer
     */
    public static final function mailFrom($from , $to, $subject, $content = '' ){
        
        return new Mailer($from, $to, $subject, $content );
    }
    /**
     * @param string $from
     * @param string $to
     * @param string  $subject
     * @param string $content
     * @return \CODERS\ArtPad\Services\Mailer
     */
    public static final function systemMail($to, $subject, $content = '' ){

        $from = get_option('admin_email');
        
        $name = get_option('blogname');
        
        $mailer = new Mailer($from, $to, $subject, $content);
        
        return $mailer->header(sprintf('From: %s <%s>',$name,$from));
    }
}


