<?php namespace CODERS\Repository\Controllers;
/**
 * Description of controller
 */
final class Dashboard extends \CODERS\Repository\Response {
    
    private $_attributes = array(
        'collection' => 'default',
    );
    
    protected function default_action(\CODERS\Repository\Request $request) {
        
    }

    protected function __construct( ) {

    }
    
    public final function __get($name) {

        $att = sprintf('get%sAttribute', preg_replace('/_/', '', $name));

        return (method_exists($this, $att)) ? $this->$att() : FALSE;
    }
    /**
     * @param string $name
     * @param mixed $arguments
     * @return mixed
     */
    public final function __call($name, $arguments) {

        switch( TRUE ){
            case preg_match(  '/^display_/' , $name ):
                $method = sprintf('display%sMethod', preg_replace('/display_/', '', $name));
                //var_dump($method);
                break;
            default:
                $method = sprintf('get%sMethod',preg_replace('/_/', '', $name));
                break;
        }

        return (method_exists($this, $method)) ? $this->$method( count($arguments) ? $arguments[0]  : array( ) ) : FALSE;
    }
    /**
     * @param string $view
     * @return string
     */
    protected final function getView( $view ){
        return sprintf('%s/html/%s.php',__DIR__,$view);
    }
    /**
     * 
     * @param string $view
     * @return \CODERS\Repository\Admin\Controller
     */
    protected final function display( $view ){
        
        printf('<div class="coders-repository %s-view"><!-- CODERS REPO CONTAINER -->',$view);
        
        require $this->getView($view);
        
        print('<!-- CODERS REPO CONTAINER --></div>');
        
        return $this;
    }
    /**
     * @return string|URL
     */
    protected final function getFormActionAttribute(){
        return get_admin_url( ) . '?page=coders-repository' ;
    }
    /**
     * @return array
     */
    protected final function getStorageAttribute(){
        
        return \CODERS\Repository\Resource::storage();
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
     * @return int
     */
    protected final function getMaxFileSizeAttribute(){
        return 255 * 255 * get_option('coders.repository.max_file_size',50);
    }
    /**
     * @param string $collection
     * @return array
     */
    protected final function getCollectionMethod( $collection ){
        return count($collection) ?
            \CODERS\Repository\Resource::collection($collection) :
            array();
    }
    /**
     * @param array $params
     * @return string
     */
    protected final function getFormActionMethod( array $params = array( ) ){
        
        $url = get_admin_url( ) . '?page=coders-repository' ;
        
        if( count ($params)){
            foreach( $params as $var=>$val ){
                $url .= sprintf('&%s=%s',$var,$val);
            }
        }
        
        return $url;
    }
    /**
     * @param array $resource
     * @return string
     */
    protected final function displayResourceMethod( array $resource ){
        
        //return $resource['name'];
        
        //$url = \CodersRepo::url($resource['public_id']);
        
        switch( $resource[ 'type' ] ){
            case 'image/png':
            case 'image/gif':
            case 'image/jpeg':
            case 'image/jpg':
                return sprintf('<img class="content media" src="%s" alt="%s" title="%s" />',
                    \CodersRepo::url($resource['public_id']),
                        $resource['name'],
                        $resource['name']);
            case 'text/html':
                return sprintf('<span class="content html">%s</span>',$resource['name']);
            case 'text/plain':
            default:
                return sprintf('<span class="content text">%s</span>',$resource['name']);
        }
        
        //return sprintf('<a class="link" href="%s" target="_blank">%s</a>', $url , $display );
        //return sprintf('<!-- INVALID_TYPE [%s] -->',$resource['type']);
    }
    /**
     * 
     * @return array
     */
    protected static final function request(){
        
        $post = filter_input_array(INPUT_POST);
        
        $get = filter_input_array(INPUT_GET);
        
        return array_merge(
                !is_null($get) ? $get : array()  ,
                !is_null($post) ? $post : array() );
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
    
    protected final function dashboard_action( array $request = array()){
        
        $action = array_key_exists('coders_repo_action', $request) ?
                $request['coders_repo_action'] :
                '';

        switch( $action ){
            case 'remove':
                if(array_key_exists('coders_repo_id', $request)){
                    $R = \CODERS\Repository\Resource::load($request['coders_repo_id']);
                    if( $R !== FALSE ){
                        var_dump(  $R->delete() );
                    }
                }
                break;
            case 'upload':
                $this->_attributes['collection'] = array_key_exists('coders_repo_collection', $request) ?
                        $request['coders_repo_collection'] :
                        'default';
                $R = \CODERS\Repository\Resource::upload( 'coders_repo_upload' , $this->_attributes['collection'] );
                break;
            case 'create_collection':
                if( array_key_exists('coders_repo_create', $request) ){
                    $this->_attributes['collection'] = $request['coders_repo_create'];
                    if( \CODERS\Repository\Resource::createCollection($this->_attributes['collection']) ){
                        //print 'success!!';
                    }
                }
                break;
            case 'refresh':
                $this->_attributes['collection'] = array_key_exists('coders_repo_collection', $request) ?
                        $request['coders_repo_collection'] :
                        'default';
                break;
        }
        
        //require $this->getView('collections');
        $this->display('collections');
    }
    
    protected final function upload_action( array $request ){
        
        if(array_key_exists('upload', $request)){
            
            $R = \CODERS\Repository\Resource::upload($request['upload']);
            
            var_dump($R);
            
            return $this->dashboard_action();
        }
        
        return FALSE;
    }
    
    protected final function remove_action( ){
        
        return TRUE;
    }
    
    protected final function settings_action(){
        
        //require $this->getView('settings');
        $this->display('settings');
    }

    protected final function save_settings_action( ){
        
        
        return $this->settings_action();
    }
    /**
     * @param String $action
     * @return \CODERS\Repository\Admin\Controller
     */
    public static final function action( $action = '' ){
        
        $ctl = new Controller( $action );
        
        $method = $action . '_action';
        
        return self::response( method_exists($ctl, $method) ? $ctl->$method( self::request() ) : FALSE );
    }
}





