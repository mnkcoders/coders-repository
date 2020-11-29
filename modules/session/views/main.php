<?php namespace CODERS\ArtPad\Session;
/**
 * 
 */
final class MainView extends \CODERS\ArtPad\View{
    
    private static $_URLS = array(
        'login' => 'session.login',
        'register' => 'session.register',
        'public' => 'pad',
    );
    
    /**
     * @return \CODERS\ArtPad\Session\MainView
     */
    public final function display() {
        
        get_header();
        
        parent::display();
        
        get_footer();

        return $this;
    }
    /**
     * @return string|URL
     */
    protected final function getLoginUrl(){
        return \CODERS\ArtPad\Request::url(self::$_URLS['login']);
    }
    /**
     * @return string|URL
     */
    protected final function getRegisterUrl(){
        
        return \CODERS\ArtPad\Request::url(self::$_URLS['register']);
    }
    /**
     * @return string
     */
    protected final function getPadUrl(){
        return \CODERS\ArtPad\Request::url(self::$_URLS['public']);
    }
    /**
     * @param string $label
     * @return string|HTML
     */
    protected final function displayEmail( $label = '' ){
        
        $email = '';
        
        return self::__HTML('input', array(
            'class' => 'form-input',
            'type' => 'email',
            'name' => 'email',
            'id' => 'id_email',
            'vaule' => $email,
            'placeholder' => $label));
    }
    /**
     * @param string $label
     * @return string|HTML
     */
    protected final function displayName( $label = '' ){
        
        $name = '';
        
        return self::__HTML('input', array(
            'class' => 'form-input',
            'type' => 'text',
            'name' => 'name',
            'id' => 'id_name',
            'vaule' => $name,
            'placeholder' => $label));
    }
    
}