<?php namespace CODERS\Repository;
/**
 * 
 */
abstract class View{
    /**
     * @var \CODERS\Repository\Model
     */
    private $_model = null;
    /**
     * @var string
     */
    private $_layout = 'default';
    /**
     * 
     */
    protected function __construct() {
        
    }
    /**
     * @return string
     */
    public final function __toString() {

        $NS = explode('\\', get_class($this ) );
        
        $class = $NS[count($NS) - 1 ];
        
        $suffix = strrpos($class, 'View');
        
        return substr($class, 0 ,$suffix);
    }
    /**
     * @return string
     */
    protected final function __date(){
        return date('Y-m-d');
    }
    /**
     * @return string
     */
    protected final function __time(){
        return date('Y-m-d H:i:s');
    }
    /**
     * @param string $name
     * @return mixed
     */
    public final function __get($name) {
        switch( TRUE ){
            case preg_match(  '/^action_/' , $name ):
                $action = substr($name, strlen('action_'));
                return $this->__action(
                        $action,
                        $this->__label($action),
                        'button-primary');
            case preg_match(  '/^value_/' , $name ):
                return $this->hasModel() ? $this->model()->$name : '';
            case preg_match(  '/^input_/' , $name ):
                $method = preg_replace('/_/', '', $name);
                $element = substr($name, strlen('input_'));
                return method_exists($this, $method) ?
                        $this->$method( ) :
                        $this->__input( $element );
            case preg_match(  '/^label_/' , $name ):
                $element = substr($name, strlen('label_'));
                $label = $this->__label($element);
                return self::__HTML('label', array(
                        'class' => 'label',
                        'for' => 'id_' . $element
                    ), $label);
            case preg_match(  '/^fieldset_/' , $name ):
                $element = substr($name, strlen('fieldset_'));
                $label = sprintf('label_%s',$element);
                $input = sprintf('input_%s',$element);
                return self::__HTML('fieldset',
                        array('class'=>'form-input'),
                        array($this->$label,$this->$input));
            case preg_match(  '/^view_/' , $name ):
                $layout = preg_replace('/_/', '.', substr($name, strlen('view_')));
                $view = $this->getView( $layout );
                if(file_exists($view)){
                    require $view;
                }
                return sprintf('<!-- VIEW [ %s ] -->',$name);
            case preg_match(  '/^display_/' , $name ):
                $display = preg_replace('/_/', '', $name);
                return method_exists($this, $display) ?
                        $this->$display( ) :
                        sprintf('<!-- INVALID DISPLAY %s -->',$display);
            case preg_match(  '/^is_/' , $name ):
                //RETURN BOOLEAN
                $is = preg_replace('/_/', '', $name);
                return method_exists($this, $is) ?
                        $this->$is( ) :
                        $this->__model($name, FALSE );
            case preg_match(  '/^list_/' , $name ):
                //RETURN LIST
                $list = preg_replace('/_/', '', $name);
                return method_exists($this, $list) ?
                        $this->$list() :
                        $this->__model($name, array());
            case preg_match(  '/^error_/' , $name ):
                //RETURN LIST
                $error = $this->hasModel() ? $this->model()->$name : '';
                return strlen($error) ? self::__HTML('span', array(
                    'class' => 'error'
                ), $error) : '';
            default:
                $get = sprintf('get%s',preg_replace('/_/', '', $name));
                return method_exists($this, $get) ?
                        $this->$get( ) :
                        $this->__model($name, sprintf('<!-- INVALID DATA-SOURCE %s -->',$name));
        }
    }
    /**
     * @param string $name
     * @param mixed $arguments
     * @return mixed
     */
    public final function __call($name, $arguments) {
        $params = is_array( $arguments ) ? $arguments[0] : $arguments;
        switch( TRUE ){
            case $name === 'action':
                if(is_array($arguments) && count($arguments)){
                    $action = $arguments[0];
                    $label = count( $arguments) > 1 ? $arguments[1] : $action;
                    $class = count( $arguments) > 2 ? $arguments[2] : '';
                    $url = \CODERS\Repository\Request::url($action);
                    return self::__HTML('a', array(
                        'class' => 'button ' . $class,
                        'href' => $url,
                        'target' => '_self'
                    ), $label);
                }
                return '<!-- INVALID OR EMPTY ACTION -->';
            case preg_match(  '/^action_/' , $name ):
                $action = substr($name, strlen('action_'));
                $label = is_array($arguments) && count( $arguments ) ?
                        $arguments[0] :
                        $this->__label($action);
                return $this->__action(
                        $action,
                        $label,
                        count( $arguments ) > 1 ? $arguments[1] : 'button-primary');
            case preg_match(  '/^display_/' , $name ):
                $display = preg_replace('/_/', '', $name);
                return method_exists($this, $display) ?
                        $this->$display( $params ) :
                        sprintf('<!-- INVALID DISPLAY %s -->',$name);
            case preg_match(  '/^is_/' , $name ):
                //RETURN BOOLEAN
                $is = preg_replace('/_/', '', $name);
                return method_exists($this, $is) ?
                        $this->$is( $params ) :
                        $this->__model($name, FALSE );
            case preg_match(  '/^list_/' , $name ):
                //RETURN LIST
                $list = preg_replace('/_/', '', $name);
                return method_exists($this, $list) ?
                        $this->$list( $params ) :
                        $this->__model($name, array());
            default:
                $get = sprintf('get%s',preg_replace('/_/', '', $name));
                return method_exists($this, $get) ?
                        $this->$get( $params ) :
                        $this->__model($name, sprintf('<!-- INVALID DATA-SOURCE %s -->',$name));
        }
    }
    /**
     * @param string $element
     * @return string
     */
    protected function __input( $element ){
        
        if( $this->_model->has( $element ) ){

            $id = 'id_' . $element;
            $value = $this->_model->value($element,'');
            $label = $this->__label($element);
            
            switch( $this->_model->type($element) ){
                case Model::TYPE_CHECKBOX:
                    $checkbox = array(
                        'type' => 'checkbox',
                        'name' => $element,
                        'value' => '1',
                        'id' => $id,
                        'class' => 'form-input'
                    );
                    if( $value ){
                        $checkbox['checked'] = 'ckecked';
                    }
                    return self::__HTML( 'label',
                            array( 'class' => 'form-input', 'for' => $id ),
                            array( self::__HTML('input', $checkbox), $label ));
                case Model::TYPE_NUMBER:
                    return self::__HTML('input', array(
                        'type' => 'number',
                        'name' => $element,
                        'value' => $this->_model->getValue($element,0),
                        'id' => $id,
                        'class' => 'form-input'
                    ));
                case Model::TYPE_CURRENCY:
                    return self::__HTML('input', array(
                        'type' => 'number',
                        'name' => $element,
                        'value' => $this->_model->getValue($element,0),
                        'id' => $id,
                        'class' => 'form-input'
                    ));
                case Model::TYPE_FLOAT:
                    return self::__HTML('input', array(
                        'type' => 'number',
                        'name' => $element,
                        'value' => $this->_model->getValue($element,0),
                        'id' => $id,
                        'class' => 'form-input'
                    ));
                case Model::TYPE_DATE:
                    return self::__HTML('input', array(
                        'type' => 'date',
                        'name' => $element,
                        'value' => $value,
                        'placeholder' => $label,
                        'id' => $id,
                        'class' => 'form-input'
                    ));
                case Model::TYPE_DATETIME:
                    return self::__HTML('input', array(
                        'type' => 'date',
                        'name' => $element,
                        'value' => $value,
                        'placeholder' => $label,
                        'id' => $id,
                        'class' => 'form-input'
                    ));
                case Model::TYPE_TEXTAREA:
                    $label = sprintf('label_',$element);
                    return self::__HTML('textarea', array(
                        'name' => $element,
                        'placeholder' => $label,
                        'id' => $id,
                        'class' => 'form-input'
                    ),$value);
                case Model::TYPE_EMAIL:
                    return self::__HTML('input', array(
                        'type' => 'email',
                        'name' => $element,
                        'value' => $value,
                        'placeholder' => $label,
                        'id' => $id,
                        'class' => 'form-input'
                    ));
                case Model::TYPE_TEXT:
                default:
                    return self::__HTML('input', array(
                        'type' => 'text',
                        'name' => $element,
                        'value' => $value,
                        'placeholder' => $label,
                        'id' => 'id_' . $element,
                        'class' => 'form-input'
                    ) );
            }
        }
        
        return sprintf('<!-- INVALID INPUT [%s] -->',$element);
    }
    /**
     * @param string $request
     * @param mixed $default
     * @param arary $args
     * @return mixed
     */
    protected final function __model( $request , $default = '' , array $args = array( ) ){
        if( $this->hasModel() ){
            //pass request rightaway
            return $this->model()->$request( $args );
        }
        return $default;
    }
    /**
     * @param string $action
     * @param string $label
     * @param string|array $class
     * @return string
     */
    protected final function __action( $action , $label , $class = '' ){
        return self::__HTML('button', array(
            'type' => 'submit',
            'name' => Request::ACTION,
            'value' => $action,
            'id' => 'id_'.$action,
            'class' => sprintf('button %s %s',$action,$class),
        ), $label );
    }
    /**
     * @param string $input
     * @return string
     */
    protected function __label( $input ){
        
        $method = sprintf('label%s',$input);
        
        if(method_exists($this, $method) ){
            return $this->$method();
        }
        elseif($this->hasModel()){
            $label = 'label_' . $input;
            return $this->model()->$label;
        }
        return $input;
    }
    /**
     * <element />
     * @param string $TAG
     * @param array $attributes
     * @param mixed $content
     * @return HTML
     */
    protected static final function __HTML( $TAG , array $attributes , $content = null ){

        if( isset( $attributes['class'])){
            if(is_array($attributes['class'])){
                $attributes['class'] = implode(' ', $attributes['class']);
            }
        }
        
        $serialized = array();
        
        foreach( $attributes as $att => $val ){
            $serialized[] = sprintf('%s="%s"',$att,$val);
        }
        
        if( !is_null($content) ){

            if(is_object($content)){
                $content = strval($content);
            }
            elseif(is_array($content)){
                $content = implode(' ', $content);
            }
            
            return sprintf('<%s %s>%s</%s>' , $TAG ,
                    implode(' ', $serialized) , strval( $content ) ,
                    $TAG);
        }
        
        return sprintf('<%s %s />' , $TAG , implode(' ', $serialized ) );
    }
    /**
     * @param string $url
     * @param string $module
     * @return string
     */
    public static final function assetUrl( $url , $module = '' ){
        
        if(strlen($module)){
            return sprintf('%smodules/%s/client/%s',
                    CODERS__REPOSITORY__URL, $module, $url );
        }
        
        //if( preg_match('/http(s?)\:\/\//i', $url) ) {
        //    return $url;
        //}
        
        return $url;
    }
    /**
     * @param string $url
     * @param string $module Empty by default
     * @return string
     */
    public static final function assetName( $url , $module = '' ){
        $path = explode('/', $url);
        $file_name = explode('.',$path[ count($path) - 1 ]);
        return strlen($module) ?
            sprintf('%s-%s-%s', \CodersRepo::ENDPOINT , $module, $file_name[0]) :
            implode('-', $file_name);
    }
    /**
     * Preload all required scripts
     * @param array $script_list List of scripts containing their required dependencies
     * @param string $module Module name
     */
    public static final function attachScripts( $script_list , $module = '' ){
        $action_hook = ( $module === 'admin' ) ? 'admin_enqueue_scripts' : 'enqueue_scripts';
        add_action( $action_hook , function() use( $script_list , $module ){
            foreach( $script_list as $script => $deps ){
                $script_url = \CODERS\Repository\View::assetUrl( $script , $module );
                $script_name = \CODERS\Repository\View::assetName($script , $module );
                wp_enqueue_script($script_name,
                        $script_url,
                        is_array($deps) ? $deps : strlen( $deps ) ? array($deps) : array());
            }
        });
    }
    /**
     * Preload all required scripts
     * @param array $style_list List of stylesets
     * @param string $module Module name
     */
    public static final function attachStyles( $style_list = array() , $module = '' ){
        $action_hook = ( $module === 'admin' ) ? 'admin_enqueue_scripts' : 'enqueue_scripts';
        add_action( $action_hook , function() use( $style_list , $module){
            foreach( $style_list as $style ){
                $style_url = \CODERS\Repository\View::assetUrl( $style , $module);
                $style_name = \CODERS\Repository\View::assetName($style,$module);
                wp_enqueue_style( $style_name , $style_url );
            }
        });
    }
    /**
     * @param boolean $lowercase 
     * @return string
     */
    public final function module( $lowercase = FALSE ){
        
        $class = explode('\\', $lowercase ?
                strtolower( get_class($this) ) :
                get_class($this));
        
        return count($class) > 1 ?
            $class[count($class) - 2 ] :
            $class[count($class) - 1 ]  ;
    }
    /**
     * @return \CODERS\Repository\Model
     */
    protected final function model(){
        return $this->_model;
    }
    /**
     * @return boolean
     */
    protected final function hasModel(){
        return $this->_model !== null;
    }
    /**
     * @param \CODERS\Repository\Model $model
     * @return \CODERS\Repository\View
     */
    public final function setModel( $model ){
        if( $this->_model === null && is_subclass_of($model, Model::class)){
            $this->_model = $model;
        }
        return $this;
    }
    /**
     * 
     * @param string $layout
     * @return \CODERS\Repository\View
     */
    public final function setLayout( $layout ){
        $this->_layout = $layout;
        return $this;
    }
    /**
     * @return string
     */
    public final function getLayout(){
        return $this->_layout === 'default' ?
            strtolower(strval($this)) :
            $this->_layout;
    }
    /**
     * @return \CODERS\Repository\View
     */
    public function display( ){
        
        $path = $this->getView( $this->getLayout( ) );
        $css_name = preg_replace('/\./', '-', $this->getLayout());
        printf('<div class="coders-repository %s-view wrap"><!-- CODERS REPO CONTAINER -->', $css_name );
        if( file_exists($path) ){
            require $path;
        }
        else{
            printf('<p>INVALID VIEW PATH <i>%s</i></p>',$path);
        }
        print('<!-- CODERS REPO CONTAINER --></div>');
        
        return $this;
    }
    
    
    
    ////////////////////////////////////////////////////////////////////////////
    ////  CALENDAR COMPONENT SETUP
    ////////////////////////////////////////////////////////////////////////////
    
    /**
     * @param string $date
     * @return string
     */
    protected function renderCalendarNavigator( $date ){
        
        $navigation = array(
            //add nav buttons here
        );
        
        return self::__HTML('td',
                array('colspan'=>7),
                $navigation);
    }
    /**
     * @return array
     */
    protected function listCalendarWeek(){
        return array(
            __('Sunday','coders_repository'),
            __('Monday','coders_repository'),
            __('Tuesday','coders_repository'),
            __('Wednesday','coders_repository'),
            __('Turstay','coders_repository'),
            __('Friday','coders_repository'),
            __('Saturday','coders_repository'),
        );
    }
    /**
     * @param string $date
     * @return int
     */
    protected function getCalendarYear( $date = '' ){
        if(strval($date) === 0 ){
            $date = $this->__date();
        }
        return intval( date('Y', strtotime($date) ) );
    }
    /**
     * @param string $date
     * @return int
     */
    protected function getCalendarMonth( $date = '' ){
        if(strval($date) === 0 ){
            $date = $this->__date();
        }
        return intval( date('m', strtotime($date) ) );
    }
    /**
     * Number of days of this month
     * @param string $date
     * @return int
     */
    protected function getMonthDays( $date = '' ){
        if(strval($date) === 0 ){
            $date = $this->__date();
        }
        return intval( date('t', strtotime($date) ) );
    }
    /**
     * @param string $date
     * @return int
     */
    protected function getCalendarDay( $date = '' ){
        if(strval($date) === 0 ){
            $date = $this->__date();
        }
        return intval( date('d', strtotime($date) ) );
    }
    /**
     * @param string $date
     * @return int
     */
    protected function getCalendarWeekDay( $date = '' ){
        if(strval($date) === 0 ){
            $date = $this->__date();
        }
        return intval( date('w', strtotime($date) ) );
    }
    /**
     * @param string $date
     * @return array
     */
    protected function listCalendarDays( $date = '' ){
        
        if(strlen($date) === 0){
            $date = $this->__date();
        }
        
        $month = $this->getCalendarMonth( $date );
        $year = $this->getCalendarYear($date);
        $count = $this->getMonthDays($date);
        $first = sprintf('%s-%s-1',$year,$month);
        $last = sprintf('%s-%s-%s',$year,$month,$count);
        
        //iterate from 1 to 29-31 day ...
        $first_day = $this->getCalendarWeekDay($first);
        $last_day = $this->getCalendarWeekDay($last);
        
        $list = array();
        
        //days previous month
        
        
        //days current month
        for( $d = 0 ; $d < $count ; $d++ ){
            $list[] = sprintf('%s-%s-%s', $year,$month,$d + 1);
        }
        
        //days mnth after
        
        
        return $list;
    }
    /**
     * @return array
     */
    protected function listCalendarClass(){
        return array('table','calendar');
    }
    /**
     * Render a full calendar table
     * @param string $date
     * @param mixed $class
     * @return string
     */
    protected function displayCalendar( $date = '' ){
        
        if(strlen($date) === 0){
            $date = $this->__date();
        }
        
        $class = $this->listCalendarClass();
        
        //head
        $header = array(
            sprintf('<tr class="navigator">%s</tr>', $this->displayCalendarNavigator($date)),
            sprintf('<tr class="days"><th>%s</th></tr>', implode('</th><th>', $this->listCalendarWeek()) ),
        );
        
        //content
        $calendar = array();
        $week = array();
        $days = $this->listCalendarDays($date);
        
        for(  $d = 0 ; $d < count( $days ) ; $d++ ){
            $week[] =  $this->displayCalendarRecord( $days[ $d ] );
            if( $d > 0 && $d % 7 === 0 ){
                //save and reset
                $calendar[] = sprintf('<tr><td>%s</td></tr>', implode('</td><td>', $week) );
                $week = array();
            } 
        }
        
        return self::__HTML('table',
                array('class'=>$class),
                array(
                    sprintf('<thead>%s</thead>', implode('', $header)),
                    sprintf('<tbody>%s</tbody>', implode('', $calendar))
                ));
    }
    /**
     * What's within a calendar day
     * @param string $date
     * @param boolean $selected
     * @return string
     */
    protected function displayCalendarRecord( $date , $selected = FALSE ){
        
        $today = date('Y-m-d');
        
        $day = $this->getCalendarMonth($date);
        
        $class = array('day');
        
        if( $selected ){
            $class[] = 'selected';
        }
        if( $today === $date ){
            $class[] = 'today';
        }
        
        return self::__HTML('span',
                array('class'=> implode(' ', $class)),
                $day );
    }

    ////////////////////////////////////////////////////////////////////////////
    ////  MAP LOCATION
    ////////////////////////////////////////////////////////////////////////////
    /**
     * 
     * @return string
     */
    protected function listMapLocations( $source ){
        
        return array(
            
        );
    }
    /**
     * @param string $location
     * @return string
     */
    protected function displayMapLocation( $location ){
        return sprintf('<label>%s</label>',$location);
    }
    /**
     * @param string $source
     * @return string
     */
    protected function displayMap( $source ){
        
        $locations = $this->listMapLocations( $source );
        
        $otput = array();
        
        $location_class = '';
        
        foreach( $locations as $loc ){
            
            $otput[] = sprintf('<div class="location %s">%s</div>',
                    $location_class,
                    $this->displayMapLocation($loc));
            
        }
        
        return self::__HTML('div',
                array('class' => 'container map'),
                $otput );
    }
    
    //
    ////////////////////////////////////////////////////////////////////////////
    ////  GENERIC COMPONENTS
    ////////////////////////////////////////////////////////////////////////////
    /**
     * @param string $progress
     * @param string $label
     * @return string
     */
    public static function displayProgressBar( $progress = 0 , $label = '%' ){
        
        if( $progress > 100 ){
            $progress = 100;
        }
        
        $bar = self::__HTML('span',
                array('class'=>'progress','style' => sprintf("width:%s%%;",$progress)),
                '' );
        
        $caption = self::__HTML('span',
                array('class'=>'label'),
                sprintf('%s %s' , $progress , $label ) );
        
        return self::__HTML('span',
                array('class' => 'progress-bar'),
                array( $bar , $caption ));
    }
    /**
     * @param string|int $media_id
     * @return string
     */
    public static function displayWPImage( $media_id ){
        
        $img = wp_get_attachment_image_src( intval($media_id) , 'full' );
        
        if( FALSE !== $img ){
            $w= $img[1];
            $h = $img[2];
            /**
             * 
             */
            $class = ($h/$w < 0.6) ? 'portrait' : 'landscape';
            
            return self::__HTML('img', array(
                'src' => $img[0],
                'class' => 'project-image ' . $class,
            ) );
        }

        return self::__HTML('span', array(
            'class' => 'no-image'
        ) , 'No Image' );
    }
    /**
     * @param string $view
     * @return string
     */
    protected final function getView( $view ){
        return sprintf('%s/modules/%s/html/%s.php',
                CODERS__REPOSITORY__DIR,
                strtolower( $this->module( true ) ),
                $view );
    }
    /**
     * @param String $url
     * @return \CODERS\Repository\View
     */
    protected function attachScript( $url ){
        
        print self::__HTML('script', array(
            'type' => 'text/javascript',
            'src' => self::assetUrl($url, $this->module(TRUE)),
        ));
        
        return $this;
    }
    /**
     * @param string $name
     * @param array $options
     * @param string $value
     * @param mixed $class
     * @param int $size 5
     * @return string
     */
    protected static final function renderList( $name , array $options = array() , $value = '' , $placeholder = '' ,  $class = 'form-input' , $size = 5 ){
        
        $items = array();
        
        if(strlen($placeholder)){
            $items[] = sprintf('<option value="">%s</option>',$placeholder);
        }
        
        foreach( $options as $val => $text ){
            $items[] = sprintf('<option value="%s" %s>%s</option>',
                    $val,
                    strlen($value) && $value == $val ? 'selected' : '',
                    $text);
        }
        
        return self::__HTML('select', array(
            'name' => $name,
            'id' => 'id_' . $name,
            'size' => $size,
            'class' => 'form-input '
        ), $items);
    }
    /**
     * @param string $name
     * @param array $options
     * @param string $value
     * @param mixed $class
     * @return string
     */
    protected static final function renderDropDown( $name , array $options = array(), $value = '' , $placeholder = '', $class = 'form-input' ){
        return self::renderList($name, $options, $value, $placeholder, $class, 1 );
    }
    
    /**
     * @param string $request
     * @return \CODERS\Repository\Response
     */
    public static final function create( $request ){
        
        $call = explode( '.',$request);
        $module = $call[0];
        $view = count( $call ) > 1 ? $call[1] : 'main';
        
        $path = sprintf('%s/modules/%s/views/%s.php',CODERS__REPOSITORY__DIR,$module,$view);
        $class = sprintf('\CODERS\Repository\%s\%sView',$module,$view);
        
        if(file_exists($path)){
            require_once $path;
            
            if(class_exists($class) && is_subclass_of($class, self::class)){
                return new $class();
            }
            else{
                throw new \Exception(sprintf('Invalid View %s',$class) );
            }
        }
        else{
            throw new \Exception(sprintf('Invalid path %s',$path));
        }
        
        return NULL;
    }
}



