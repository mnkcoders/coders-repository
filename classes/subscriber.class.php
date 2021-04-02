<?php namespace CODERS\ArtPad;
/**
 * Request Class
 */
final class Subscriber{
    
    private final function __construct() {
        
    }
    
    private static final function product( ){
        return array( 50 );
    }
    /**
     * @param int $product_id
     * @param string $email
     * @param float $amount
     * @param string $label
     * @return boolean
     */
    public static final function Subscribe( $product_id , $email , $amount , $label  ){
        
        $products = self::product();
        
        if(in_array($product_id, $products)){
            $data = array(
                'email' => $email,
                'amount' => $amount,
                'subscription' => $product_id,
                'label' => $label
            );
            
            if( file_put_contents('accounts.txt', json_encode($data)) ){
                //ok!
            }
            return TRUE;
        }
        
        return FALSE;
    }
    
}