<?php namespace CODERS\Repository;

abstract class Model{
    
    protected function __construct( array $data = array( ) ) {
        
    }
    /**
     * @return string
     */
    public final function __toString() {
        $name = explode('\\', get_class($this));
        
        return $name[count($name) - 1 ];
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
     * @param string $name
     * @return mixed
     */
    public final function __get($name) {
        switch( TRUE ){
            case preg_match(  '/^is_/' , $name ):
                //RETURN BOOLEAN
                $is = preg_replace('/_/', '', $name);
                return method_exists($this, $is) ? $this->$is( ) : FALSE;
            case preg_match(  '/^list_/' , $name ):
                //RETURN LIST
                $list = preg_replace('/_/', '', $name);
                return method_exists($this, $list) ? $this->$list() : array();
            default:
                //RETURN STRING
                $get = sprintf('get%s',preg_replace('/_/', '', $name));
                return method_exists($this, $get) ? $this->$get() : '';
        }
    }
    /**
     * @param string $name
     * @param mixed $arguments
     * @return mixed
     */
    public final function __call($name, $arguments) {
        switch( TRUE ){
            case preg_match(  '/^is_/' , $name ):
                //RETURN BOOLEAN
                $is = sprintf('is%s', preg_replace('/_/', '', $name));
                return method_exists($this, $is) ? $this->$is( $arguments ) : FALSE;
            case preg_match(  '/^list_/' , $name ):
                //RETURN LIST
                $list = sprintf('list%s', preg_replace('/_/', '', $name));
                return method_exists($this, $list) ? $this->$list( $arguments ) : array();
            default:
                //RETURN STRING
                $get = sprintf('get%s',preg_replace('/_/', '', $name));
                return method_exists($this, $get) ? $this->$get( $arguments ) : '';
        }
    }
    
    
    /**
     * @param string $request
     * @return \CODERS\Repository\Model | boolean
     * @throws \Exception
     */
    public static final function create( $request ){
        
        try{
            $extract = explode('.', $request);
            $module = $extract[0];
            $model = count($extract)  > 1 ? $extract[1] : 'main';

            $path = sprintf('%s/modules/%s/models/%s.php',
                    preg_replace( '/\\\\/', '/', CODERS__REPOSITORY__DIR ),
                    strtolower( $module ), strtolower( $model ) );

            $class = sprintf('\CODERS\Repository\%s\%sModel',
                    strtolower( $module ),strtolower( $model ) );

            if(file_exists($path)){
                require_once $path;
                if(class_exists($class) && is_subclass_of($class, self::class)){
                    return new $class();
                }
                else{
                    throw new \Exception(sprintf('Invalid Model %s',$class) );
                }
            }
            else{
                throw new \Exception(sprintf('Invalid path %s',$path) );
            }
            return FALSE;
        }
        catch (Exception $ex) {
            die( $ex->getMessage());
        }
        
        return FALSE;
    }
}


