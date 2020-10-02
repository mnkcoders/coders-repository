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
     * @param type $output
     * @return type
     */
    protected function ajax( $output ){
        
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
}






