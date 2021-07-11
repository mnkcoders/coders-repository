<?php namespace CODERS\ArtPad;
/**
 * 
 */
final class CoinBox extends \CODERS\ArtPad\Model{
    
    const TABLE = 'credits';
    const TRANSACTION_TYPE_ADD = 'add';
    const TRANSACTION_TYPE_SUB = 'sub';
    const TRANSACTION_TYPE_CURRENT = 'current';
    
    protected final function __construct(array $data = array()) {
        
        $this->define('ID',parent::TYPE_TEXT, array('size'=>32))
                ->define('owner',parent::TYPE_NUMBER, array('value'=>0))
                ->define('amount',parent::TYPE_NUMBER,array('value'=>0))
                ->define('type',parent::TYPE_TEXT)
                ->define('detail',parent::TYPE_TEXT,array('size'=>64))
                ->define('date_created',parent::TYPE_DATETIME);
    }
    /**
     * @param boolean $plural
     * @return string
     */
    public static final function getCoin( $plural = FALSE ){
        return $plural ? 
                \ArtPad::getOption('coins',__('Coins','coders_artpad')) :
                \ArtPad::getOption('coin',__('Coin','coders_artpad')) ;
    }
    /**
     * @param string $text
     * @return string
     */
    public static final function parseCoinLabel( $text ){
        
        $text = preg_replace('/{COIN}}/', self::getCoin(), $text);
        $text = preg_replace('/{COINS}}/', self::getCoin(TRUE), $text);
        
        return $text;
    }

    /**
     * @param int $owner_id
     * @return array
     */
    public static final function listMovements( $owner_id ){
        
        $db = $this->newQuery();
        
        return $db->select(self::TABLE,'*',array('owner'=>$owner_id),array('date_created'),'ID');
    }
    /**
     * @param int $owner_id
     * @return int
     */
    public static final function amount( $owner_id ){
        
        $db = $this->newQuery();
        
        $table = $db->table(self::TABLE);
        
        $add = $db->query(sprintf('SELECT SUM(`amount`) AS `amount` FROM `%s` WHERE `owner`="%s" AND `type`="%s"',
                $table,
                $owner_id,
                self::TRANSACTION_TYPE_ADD));

        $sub = $db->query(sprintf('SELECT SUM(`amount`) AS `amount` FROM `%s` WHERE `owner`="%s" AND `type`="%s"',
                $table,
                $owner_id,
                self::TRANSACTION_TYPE_SUB));
        
        $amount = intval( $add['amount'] ) - intval( $sub['amount'] );
        
        return $amount;
    }
    /**
     * @param \CODERS\ArtPad\Account $account
     * @param int $amount
     * @return \CODERS\ArtPad\CoinBox
     */
    public static final function add( Account $account , $amount ){
        return self::create($account , $amount , self::TRANSACTION_TYPE_ADD);
    }
    /**
     * @param \CODERS\ArtPad\Account $account
     * @param int $amount
     * @return \CODERS\ArtPad\CoinBox
     */
    public static final function spend( Account $account , $amount ){
        return self::create($account , $amount , self::TRANSACTION_TYPE_SUB);
    }
    
    /**
     * @param \CODERS\ArtPad\Account $account
     * @param int $amount
     * @param string $type
     * @return \CODERS\ArtPad\CoinBox
     */
    public static final function create( Account $account , $amount = 0 , $type = self::TRANSACTION_TYPE_ADD , $detail = '' ){

        $ID = self::__makeID( $account->ID . $this->__ts(TRUE) );
        
        if(strlen($detail) === 0 ){
            $detail = __('Earned','coders_artpad') . ' ' . $amount . ' {COINS}';
        }

        $movement = array(
            'ID' => $ID,
            'owner' => $account->ID,
            'amount' => $amount,
            'type' => $type,
            'detail' => $detail,
            'date_created' => $this->__ts()
        );
        
        $db = $this->newQuery();
        
        $db->insert( self::TABLE, $movement );
        
        return new CoinBox( $movement );
    }
    /**
     * @param \CODERS\ArtPad\Account $account
     * @return \CODERS\ArtPad\CoinBox
     */
    public static final function load( Account $account ){
        
        $amount = self::amount($account->ID);
        
        return new CoinBox(array(
            'ID' => self::__makeID(),
            'owner' => $account->ID,
            'amount' => $amount,
            'type' => self::TRANSACTION_TYPE_CURRENT,
            'detail' => __('Current balance','coders_artpad'),
            'date_created' => self::__ts()
        ));
    }
    /**
     * 
     * @return array
     */
    public static final function listTypes(){
        return array(
            self::TRANSACTION_TYPE_ADD => __('Add','coders_artpad'),
            self::TRANSACTION_TYPE_SUB => __('Remove','coders_artpad'),
        );
    }
}


