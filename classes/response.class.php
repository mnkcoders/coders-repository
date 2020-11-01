<?php namespace CODERS\Repository;
/**
 * Description of controller
 */
abstract class Response {
    
    protected function __construct( ) {

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
     * @return \CODERS\Repository\Model
     */
    protected final function importModel( $model ){
        return Model::create($model );
    }
    /**
     * @param string $view
     * @return \CODERS\Repository\View
     */
    protected final function importView( $view ){
        return View::create( $view );
    }
    /**
     * @param \CODERS\Repository\Request $request
     * @return boolean
     */
    private final function execute( Request $request ){
        
        $call = sprintf('%s_action', $request->action() );

        return method_exists($this, $call) ?
                $this->$call( $request ) :
                $this->error($request);
    }
    /**
     * @param \CODERS\Repository\Request $request
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
        
        print json_encode( $response );
        
        return TRUE;
    }
    /**
     * @param \CODERS\Repository\Request $request
     */
    abstract protected function default_action(Request $request );
    /**
     * @param \CODERS\Repository\Request $request
     * @return \CODERS\Repository\Response | boolean
     * @throws \Exception
     */
    public static final function create( Request $request ){
        
        try{

            $path = sprintf('%s/modules/%s/controllers/%s.php',
                    preg_replace( '/\\\\/', '/', CODERS__REPOSITORY__DIR ),
                    strtolower( $request->module() ), strtolower( $request->controller() ) );

            $class = sprintf('\CODERS\Repository\%s\%sController',
                    $request->module(), $request->controller() );

            if(file_exists($path)){
                require_once $path;
                if(class_exists($class) && is_subclass_of($class, self::class)){
                    $C = new $class();
                    return $C->execute( $request );
                }
                else{
                    throw new \Exception(sprintf('Invalid Controller %s',$class) );
                }
            }
            else{
                throw new \Exception(sprintf('Invalid path %s',$path) );
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
     * @return \CODERS\Repository\Response | boolean
     */
    public static final function fromRequest( ){
        
        return self::create( Request::import() );
    }
    /**
     * @return \CODERS\Repository\Response | boolean
     */
    public static final function fromRoute( $route = Request::_DEFAULT ){
        
        return self::create( Request::import( $route ) );
    }
    /**
     * @return \CODERS\Repository\Response | boolean
     */
    public static final function fromAjax( $route = 'admin.ajax' ){
        
        return self::create( Request::ajax( $route ) );
    }
}






