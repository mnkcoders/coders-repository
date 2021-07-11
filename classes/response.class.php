<?php namespace CODERS\ArtPad;
/**
 * Description of controller
 */
abstract class Response {
    
    const NOTICE_ERROR = 'error';
    const NOTICE_INFO = 'info';
    const NOTICE_SUCCESS = 'success';
    const NOTICE_WARNING = 'warning';
    
    protected function __construct( ) {

    }
    /**
     * @param string $message
     * @param string $type
     * @param boolean $dismiss
     * @return \CODERS\ArtPad\Response
     */
    protected function notify( $message , $type = self::NOTICE_INFO , $dismiss = TRUE ){
        
        if(is_admin() ){
            /**
             * admin_notices hook is nolonger available once this controller gets triggered
             */
            //$content = array(
            //    'message' => $message,
            //    'type' => $type,
            //    'dismiss' => $dismiss,
            //);
            //add_action( 'admin_notices', function() use($content){
            //    printf('<div class="notice notice-%s %s">%s</div>',
            //            $content['type'],
            //            $content['dismiss'] ? 'is-dismissible' : '',
            //            $content['message']);
            //} );
            printf('<div class="notice notice-%s %s">%s</div>',
                        $type,
                        $dismiss ? 'is-dismissible' : '',
                        $message);

        }
        else{
            //handle log here
        }
        
        return $this;
    }
    /**
     * @return string
     */
    public function __toString() {
        $class = explode('\\', get_class($this));
        return $class[count($class) - 1 ];
    }
    /**
     * @return string
     */
    public final function module(){
        $class = explode('\\', get_class($this));
        return count($class) > 1 ?
            $class[count($class) - 2 ] :
            $class[count($class) - 1 ]  ;
    }
    /**
     * @param string $model
     * @param array $data
     * @return \CODERS\ArtPad\Model
     */
    protected function model( $model , array $data = array() ){
        return Model::Instance( $model , $data );
    }
    /**
     * @param string $view
     * @return \CODERS\ArtPad\View
     */
    protected final function view( $view ){
        return View::create( $view );
    }
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    private final function execute( Request $request ){
        
        $call = sprintf('%s_action', $request->action() );

        return method_exists($this, $call) ?
                $this->$call( $request ) :
                $this->error($request);
    }
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected function error(Request $request ){

        printf('Undefined action [%s]', strval($request));
        
        var_dump($request);

        return FALSE;
    }
    /**
     * @return string
     */
    protected final function ts(){
        return date( 'YmdHis' );
    }
    /**
     * 
     * @param mixed $output
     * @param int $status 200
     * @return type
     */
    protected function ajax( $output , $status = 200 ){
        
        header('Content-type: application/json');
        
        $response = array(
            'ts' => $this->ts(),
            'status' => $status
        );
        
        switch( TRUE ){
            case is_null($output):
                //null responses are considered as empty, nothing bad happened, then TRUE
                break;
            case is_bool($output):
                $response['data'] = intval($output);
                break;
            //case is_numeric($output):
            //case is_string($output):
            case is_array($output):
                //serialize the whole array
                $response['data'] = $output;
                break;
            case is_object($output) && method_exists($output, 'serialize' ):
                //parse model result
                $response['data'] = $output->data();
                break;
            default:
                $response['data'] = $output;
                break;
        }
        
        $response['debug'] = \ArtPad::stamp();
        
        print json_encode( $response );
        
        return TRUE;
    }
    /**
     * @param \CODERS\ArtPad\Request $request
     */
    abstract protected function default_action(Request $request );
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return \CODERS\ArtPad\Response | boolean
     * @throws \Exception
     */
    public static final function create( Request $request ){
        
        try{
            
            $path = sprintf('%s/modules/%s/controllers/%s.php',
                    preg_replace( '/\\\\/', '/', CODERS__REPOSITORY__DIR ),
                    strtolower( $request->module() ), strtolower( $request->controller() ) );

            $class = sprintf('\CODERS\ArtPad\%s\%sController',
                    $request->module( TRUE ), $request->controller() );

            if(file_exists($path)){
                require_once $path;
                if(class_exists($class) && is_subclass_of($class, self::class)){
                    $C = new $class();
                    return $C->execute( $request );
                }
                else{
                    throw new \Exception(sprintf('Invalid Controller <b>%s</b>',$class) );
                }
            }
            else{
                throw new \Exception(sprintf('Invalid path <b>%s</b>',$path) );
            }
        }
        catch (\Exception $ex) {
            if(is_admin()){
                printf('<div class="notice notice-error"><p>%s</p></div>',$ex->getMessage());
            }
            else{
                print( $ex->getMessage( ) );
            }
        }
        
        return FALSE;
    }
    /**
     * @return \CODERS\ArtPad\Response | boolean
     */
    public static final function Route( $route ){
        
        $request = Request::route( $route );
        //var_dump( strval( $request ) ); 
        $controller = self::create( $request );
        //var_dump($controller);
        return $controller;
    }
}






