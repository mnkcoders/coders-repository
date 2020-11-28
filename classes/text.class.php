<?php namespace CODERS\ArtPad;
/**
 * Centralize all strings here for WP string parser and locale translations
 */
final class Text {
    /**
     * @var \CODERS\ArtPad\Strings
     */
    private static $_instance = null;
    /**
     * @var array
     */
    private $_strings;
    
    private final function __construct() {
        
        $this->_strings = $this->setup();
    }
    
    /**
     * @return array
     */
    private final function setup( ){
        
        return array(
            'Collections' => __('Collections','artist_pad'),
        );
    }
    /**
     * @return String
     */
    public static final function __( $string ){
    
        if(is_null(self::$_instance)){
            self::$_instance = new Text();
        }
        
        return array_key_exists($string, self::$_instance->_strings) ? 
                self::$_instance->_strings[ $string ] :
                $string;
    }
}

