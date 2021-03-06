<?php namespace CODERS\ArtPad;

final class Account extends \CODERS\ArtPad\Model{
    
    const STATUS_CREATED = 0;       //created, must be activated
    const STATUS_ACTIVE = 1;        //active, can login
    const STATUS_INACTIVE = 2;      //inactive, define behavior
    
    protected final function __construct(array $data = array()) {
        $this->define('ID', parent::TYPE_NUMBER , array('value'=>0))
                ->define('token', parent::TYPE_TEXT, array('size'=>32,'required'=>TRUE))
                ->define('name', parent::TYPE_TEXT, array('size'=>16,'label' => __('Name','coders_artpad')))
                ->define('status', parent::TYPE_NUMBER, array('value'=>0,'label' => __('Status','coders_artpad')))
                ->define('email_address', parent::TYPE_EMAIL, array('size'=>128,'label' => __('Email','coders_artpad')))
                ->define('date_created', parent::TYPE_DATETIME, array('label' => __('Created','coders_artpad')))
                ->define('date_updated', parent::TYPE_DATETIME, array('label' => __('Updated','coders_artpad')));
        
        parent::__construct($data);
    }
    /**
     * @return string
     */
    public final function __toString() {
        return sprintf('[%s]%s' ,$this->value('ID') , $this->value('name'));
    }
    
    public final function getPoints(){
        
        return 0;
    }
    /**
     * @return \CODERS\ArtPad\Invite
     */
    public final function createInviteLink(){
        
        return Invite::new($this);
    }
    /**
     * @param string $element
     * @return boolean
     */
    protected final function validate($element) {
        
        if( $element === 'email_address' ){
            $email = $this->value($element);
            if(preg_match(self::EmailMatch(), $email ) < 1){
                $this->set($element, 'error', 'Invalid email format');
                return FALSE;
            }
            if( !self::checkEmail($email) ){
                $this->set($element, 'error', 'Email already exists in database');
                return FALSE;
            }
        }
        elseif( !parent::validate($element) ){
            $this->set($element, 'error', sprintf('%s wrong value',$element));
            return FALSE;
        }
        
        return TRUE;
    }
    /**
     * @return array
     */
    protected final function updated(){
        
        $updated = array();
        
        foreach( $this->elements() as $element ){
            if( $this->get($element, 'updated', FALSE)){
                $updated[ $element ] = $this->value($element);
            }
        }
        
        return $updated;
    }
    /**
     * @return string
     */
    public final function generateID(){
        return self::GenerateHash( $this->value('ID') . $this->__ts() );
    }
    /**
     * @return \CODERS\ArtPad\Model\Account
     */
    public final function save(){
        
            $ID = $this->ID;
            $query = new Query();
            $result = 0;

            if( $ID === 0 ){
                //create
                $ts = $this->__ts();
                $this->setValue('date_created',$ts)->setValue('date_updated',$ts);

                $values = $this->listValues();

                $result += $query->insert( 'account' , $values );
            }
            else{//update
                $updated = $this->updated();
                if(count($updated)){
                    $updated['date_updated'] = parent::__ts(/*timestamp*/);
                    $result += $query->update('account',
                            $updated,
                            array('ID' => $ID));
                }
            }
        
        return $this;
    }
    /**
     * @return boolean
     */
    public final function isNew(){
        return $this->value('ID',0) === 0;
    }
    /**
     * @param string $email
     * @return boolean
     */
    protected static function checkEmail( $email ){
        
        $email_address = self::normalizeEmail($email);
        
        $query = new Query();
        
        $result = $query->select('account','*',array('email_address'=>$email_address));
        
        return count( $result ) === 0;
    }
    /**
     * 
     * @param float $amount
     * @return \CODERS\ArtPad\Account
     */
    public final function subscribe( $amount ){


      return $this;
    }
    /**
     * @param string $text
     * @return string
     */
    protected static final function normalizeEmail( $text ){
        return preg_replace('/ /', '', strtolower($text) );
    }
    /**
     * @param string $input
     * @return string
     */
    public static final function GenerateHash( $input ){
        return md5( $input );
    }
    /**
     * @return array
     */
    public static final function listStatus(){
        return array(
            self::STATUS_CREATED => __('Created','coders_artpad'),
            self::STATUS_ACTIVE => __('Active','coders_artpad'),
            self::STATUS_INACTIVE => __('Inactive','coders_artpad'),
        );
    }
    /**
     * @param array $filters
     * @return array
     */
    public static final function List( array $filters = array() ){
        
        $query = new Query();
        
        $output = $query->select('account', '*', $filters , 'date_created', 'ID' );
        
        return $output;
    }
    /**
     * @param string $email
     * @param boolean $hash
     * @return boolean|\CODERS\ArtPad\Account
     */
    public static final function LoadByEmail( $email , $hash = FALSE ){
        //var_dump($email);
        if( self::ValidateEmail($email)){
            
            $email = self::normalizeEmail($email);
        
            $filters = $hash ? 
                    array( 'token' => self::GenerateHash($email) ) :
                    array( 'email_address' => $email );

            $query = new Query();

            $data = $query->select('account', '*', $filters );

            if( count( $data ) ){
                return new Account( $data[0] );
            }

        }
        
        return FALSE;
    }
    /**
     * @param int $account_id
     * @return boolean|\CODERS\ArtPad\Account
     */
    public static final function Load( $account_id ){
        
        $query = new Query();
        
        $data = $query->select('account', '*', array( 'ID' => $account_id ) );
        
        if( count( $data ) ){
            return new Account($data[0] );
        }
        
        return FALSE;
    }
    /**
     * @param string $email
     * @param float $amount
     * @return \CODERS\ArtPad\Account
     */
    public static final function Register( $email , $amount ){
        
        $account = self::LoadByEmail($email, TRUE);
        
        if( FALSE === $account ){
            $account = self::New(array('email_address' => $email));
        }
        
        return $account->subscribe($amount);
    }
    /**
     * @param array $data
     * @return boolean|\CODERS\ArtPad\Account
     */
    public static final function New( array $data ){
        
        $data['ID'] = 0; //just override if any, this is a blank new account
        $data['email_address'] = self::normalizeEmail($data['email_address']);
        $data['token'] = self::GenerateHash($data['email_address']);
        //$data['date_created'] = $data['date_updated'] = $this->__ts();

        $account = new Account($data);
        
        return $account->validateData() ? $account->save() : FALSE;
    }
    /**
     * @param string $email
     * @return boolean
     */
    public static final function ValidateEmail( $email ){

        return preg_match( self::EmailMatch() , $email ) > 0;

    }
}



