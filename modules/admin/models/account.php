<?php namespace CODERS\Repository\Admin;
/**
 * 
 */
final class AccountModel extends \CODERS\Repository\Model{
    
    protected final function __construct(array $data = array()) {
        
        $this->define('ID',parent::TYPE_NUMBER,array('label'=>'ID'))
            ->define('name', parent::TYPE_TEXT,array('label' => __('Name', 'coders_repository')))
            ->define('email_address', parent::TYPE_EMAIL,array('label' => __('Email', 'coders_repository')))
            ->define('token',parent::TYPE_TEXT,array('label'=>__('Public Key','coders_repository')))
            ->define('status',parent::TYPE_NUMBER,array('label'=>__('Status','coders_repository')))
            ->define('date_created',parent::TYPE_DATETIME,array('label'=>__('Created','coders_repository')))
            ->define('date_updated',parent::TYPE_DATETIME,array('label'=>__('Last Updated','coders_repository')));
        
        parent::__construct($data);
    }
    /**
     * @return array
     */
    protected final function listSubscriptions(){
        return array(
            'copper'=>'Copper',
            'silver'=>'Silver',
            'gold'=>'Gold',
            'diamond'=>'Diamond',
        );
    }
    /**
     * @param string $element
     * @return boolean
     */
    public final function validate($element) {
        if( !parent::validate($element) ){
            $this->set($element, 'error', 'wrong value');
            return FALSE;
        }
        return TRUE;
    }
    
    public static final function listAccounts(){

        return \CODERS\Repository\Account::List();
        
        $output = array();
        
        $date = date('Y-m-d H:i:s');
        
        for( $i = 0 ; $i < 10 ; $i++ ){
            $output[] = array(
                'ID' => $i + 1,
                'token' => 'abc' . ( $i + 1 ),
                'name' => 'Account Name ' . ( $i + 1 ),
                'status' => 1,
                'email_address' => sprintf('account%s@hotmail.com', $i + 1 ),
                'address_city' => 'city ' . ( $i + 1 ),
                'address_zip' => '12345-' . ( $i + 1 ),
                'address_state' => 'state ' . ( $i + 1 ),
                'address_country' => 'country ' . ( $i + 1 ),
                'date_created' => $date,
                'date_updated' => $date,
            );
        }
        
        return $output;
    }
}