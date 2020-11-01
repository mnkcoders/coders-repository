<?php namespace CODERS\Repository\Admin;
/**
 * 
 */
abstract class FormModel extends \CODERS\Repository\Model{
    
    const TYPE_HONEYPOT = 'honeypot';
    
    protected function __construct(array $data = array()) {
        
        //honeypot
        $this->define('secret',self::TYPE_HONEYPOT );
        
        parent::__construct($data);
    }
    
}