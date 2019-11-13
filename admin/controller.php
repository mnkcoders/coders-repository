<?php namespace CODERS\Repository\Admin;
/**
 * Description of controller
 */
class Controller {
    
    private $_attributes = array(
        'collection' => 'default',
    );
    
    protected function __construct( ) {

    }

    public final function __get($name) {

        $att = sprintf('get%sAttribute',$name);

        return (method_exists($this, $att)) ? $this->$att() : FALSE;
    }
    /**
     * @param string $name
     * @param mixed $arguments
     * @return mixed
     */
    public final function __call($name, $arguments) {

        $method = sprintf('get%sMethod',$name);

        return (method_exists($this, $method)) ? $this->$method( $arguments ) : FALSE;
    }
    /**
     * @param string $view
     * @return string
     */
    protected final function getView( $view ){
        return sprintf('%s/html/%s.php',__DIR__,$view);
    }
    /**
     * @return array
     */
    protected final function getStorageAttribute(){
        return \CodersRepo::storage();
    }
    /**
     * @return string|FALSE
     */
    protected final function getSelectedAttribute(){
        return array_key_exists('collection', $this->_attributes) ?
                $this->_attributes['collection'] :
                FALSE;
    }
    /**
     * @param string $attributes
     * @return array
     */
    protected final function getCollectionMethod( array $attributes ){
        return count($attributes) ?
            \CODERS\Repository\Resource::collection($attributes[0]) :
            array();
    }
    /**
     * 
     * @return array
     */
    protected static final function request(){
        
        $input = filter_input_array(INPUT_POST);
        
        
        return $input;
    }
    /**
     * @param mixed $output
     * @return JSON
     */
    protected static final function response( $output ){
        
        switch( TRUE ){
            case is_null($output):
                //null responses are considered as empty, nothing bad happened, then TRUE
                return NULL;
            case is_array($output):
                //serialize the whole array
                return json_encode( $output );
            case is_object($output):
                //parse to string
                return json_encode( array( 'response'=> intval( $output->toString() ) ) );
            case is_bool($output):
                return json_encode( array( 'response'=> intval( $output ) ) );
            //case is_numeric($output):
            //case is_string($output):
            default:
                return json_encode( array( 'response'=> $output ) );
        }
    }
    
    protected final function dashboard_action(){
        
        require $this->getView('collections');
    }
    
    protected final function upload_action( ){
        
        return TRUE;
    }
    
    protected final function remove_action( ){
        
        return TRUE;
    }
    
    protected final function settings_action(){
        
        require $this->getView('settings');
    }

    protected final function save_settings_action( ){
        
        
        return $this->settings_action();
    }
    /**
     * @param String $action
     * @return \CODERS\Repository\Admin\Controller
     */
    public static final function action( $action = 'dashboard' ){
        
        $ctl = new Controller( $action );
        
        $method = $action . '_action';
        
        return self::response( method_exists($ctl, $method) ? $ctl->$method( self::request() ) : FALSE );
    }
}





