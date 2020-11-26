<?php namespace CODERS\Repository\User;
/**
 * 
 */
class AccountModel extends \CODERS\Repository\Model{
    
    
    protected function __construct(array $data = array()) {
        
        $this->define('name', parent::TYPE_TEXT, array('placeholder'=>'Your avatar name'))
                ->define('email', parent::TYPE_EMAIL, array('placeholder'=>'Type in here your email','label' => 'Email'))
                ->define('active',parent::TYPE_CHECKBOX,array('label'=>'Active','value'=>TRUE));
        
        parent::__construct($data);
    }
    /**
     * @return array
     */
    public function listData(){
        return $this->dictionary();
    }
}