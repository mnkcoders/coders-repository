<?php namespace CODERS\ArtPad;
/**
 * 
 */
abstract class View{
    /**
     * @var \CODERS\ArtPad\Model
     */
    private $_model = null;
    /**
     * @var string
     */
    private $_layout = 'default';
    /**
     * @var array
     */
    private $_navigator = array( /* list all navigation paths here*/ );
    /**
     * 
     */
    protected function __construct() {

        if(is_admin()){
            $current = $this->getCurrentAdminPage();
            $parent = $this->getAdminPageParent();
            if( $current !== $parent ){
                $this->addNavPath(
                        __('Artist Pad','coders_artpad'),
                        Request::url('admin.main'))->addNavPath(
                                get_admin_page_title(),
                                Request::url('admin.collection'));
            }
            else{
                $this->addNavPath(
                        __('Artist Pad','coders_artpad'),
                        Request::url('admin.main'));
            }
        }
    }
    /**
     * @return string
     */
    public final function __toString() {
        return $this->__class();
    }
    /**
     * View's Class Name
     * @return String
     */
    protected final function __class(){
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
                //QUICK ACTION DISPLAY
                $action = substr($name, strlen('action_'));
                return $this->__action(
                        $action,
                        $this->__label($action),
                        'button-primary');
            case preg_match(  '/^input_/' , $name ):
                //FORM INPUT DISPLAY
                $method = preg_replace('/_/', '', $name);
                $element = substr($name, strlen('input_'));
                return method_exists($this, $method) ?
                        $this->$method( ) :
                        $this->__input( $element );
            case preg_match(  '/^label_/' , $name ):
                //LABEL DISPLAY
                $element = substr($name, strlen('label_'));
                $label = $this->__label($element);
                return self::__HTML('label', array(
                        'class' => 'label',
                        'for' => 'id_' . $element
                    ), $label);
            case preg_match(  '/^fieldset_/' , $name ):
                //FORM-FIELDSET DISPLAY
                $element = substr($name, strlen('fieldset_'));
                $label = sprintf('label_%s',$element);
                $input = sprintf('input_%s',$element);
                return self::__HTML('fieldset',
                        array('class'=>'form-input'),
                        array($this->$label,$this->$input));
            case preg_match(  '/^view_/' , $name ):
                //LOAD EXTRA VIEW
                $layout = preg_replace('/_/', '.', substr($name, strlen('view_')));
                $view = $this->getView( $layout );
                if(file_exists($view)){
                    require $view;
                }
                return sprintf('<!-- VIEW [ %s ] -->',$name);
            case preg_match(  '/^display_/' , $name ):
                //CUSTOM DISPLAY
                $display = preg_replace('/_/', '', $name);
                return method_exists($this, $display) ?
                        $this->$display( ) :
                        sprintf('<!-- INVALID DISPLAY %s -->',$display);
            case preg_match(  '/^is_/' , $name ):
                //RETURN BOOLEAN
                $is = preg_replace('/_/', '', $name);
                return method_exists($this, $is) ?
                        $this->$is( ) :
                        $this->model()->$is( );
            case preg_match(  '/^list_/' , $name ):
                //RETURN LIST
                $list = preg_replace('/_/', '', $name);
                return method_exists($this, $list) ?
                        $this->$list() :
                        $this->model()->$name();
            case preg_match(  '/^error_/' , $name ):
                //RETURN LIST
                return $this->hasModel() ?
                    $this->__error( $this->model()->$name ) :
                    '';
            default:
                $get = sprintf('get%s',preg_replace('/_/', '', $name));
                return method_exists($this, $get) ?
                        $this->$get( ) :
                        $this->model()->$get( );
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
                    $class = count( $arguments) > 2 ? $arguments[2] : 'button';
                    $url = \CODERS\ArtPad\Request::url($action);
                    return self::__HTML('a', array(
                        'class' => $class,
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
                        $this->model()->$is( $params );
            case preg_match(  '/^list_/' , $name ):
                //RETURN LIST
                $list = preg_replace('/_/', '', $name);
                return method_exists($this, $list) ?
                        $this->$list( $params ) :
                        $this->model()->$list( $params );
            default:
                $get = sprintf('get%s',preg_replace('/_/', '', $name));
                return method_exists($this, $get) ?
                        $this->$get( $params ) :
                        $this->model()->$get( $params );
        }
    }
    /**
     * @param string $element
     * @return string
     */
    protected function __input( $element ){
        
        if( $this->hasModel() && $this->model()->has( $element ) ){

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
     * @param string $error
     * @return string Error display
     */
    protected function __error( $error ){
        return strlen($error) ?
            self::__HTML('span', array( 'class' => 'error' ), $error) :
            '';
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
            return sprintf('%smodules/%s/views/client/%s',
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
            sprintf('%s-%s-%s', \ArtPad::ENDPOINT , $module, $file_name[0]) :
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
                $script_url = \CODERS\ArtPad\View::assetUrl( $script , $module );
                $script_name = \CODERS\ArtPad\View::assetName($script , $module );
                $deps = is_array($deps) ? $deps : array( $deps );
                wp_enqueue_script($script_name,$script_url, $deps );
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
                $style_url = \CODERS\ArtPad\View::assetUrl( $style , $module);
                $style_name = \CODERS\ArtPad\View::assetName($style,$module);
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
     * @return \CODERS\ArtPad\Model
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
     * @param \CODERS\ArtPad\Model $model
     * @return \CODERS\ArtPad\View
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
     * @return \CODERS\ArtPad\View
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
     * @return \CODERS\ArtPad\View
     */
    public function display( ){
        
        $path = $this->getView( $this->getLayout( ) );
        $css_name = preg_replace('/\./', '-', $this->getLayout());
        printf('<div class="coders-artpad %s-view wrap"><!-- CODERS REPO CONTAINER -->', $css_name );
        if( file_exists($path) ){
            require $path;
        }
        else{
            printf('<p>INVALID VIEW PATH <i>%s</i></p>',$path);
        }
        print('<!-- CODERS REPO CONTAINER --></div>');
        
        $this->displayDebug();
        
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
        
        $header = self::__HTML('strong',
                array( 'class' => 'header calendar-date' ),
                $this->getCalendarDayName($date) . ' ' . $this->getCalendarMonthName($date));
        
        $yearDropdown = self::renderDropDown('calendar_year',
                $this->listCalendarYears(),
                $this->getCalendarYear(),
                '', //no placeholder
                'centered calendar-year form-input');
        
        $left = self::__HTML('span', array('class'=>'button'), __('Previous','coders_artpad'));
        
        $right = self::__HTML('span', array('class'=>'button'), __('Next','coders_artpad'));
        
        $navigation = array(
            //add nav buttons here
            '<!-- ' . $date . '-->',
            sprintf('<li class="left">%s</li>',$left),
            sprintf('<li class="center">%s</li>',$header . ' ' . $yearDropdown),
            sprintf('<li class="right">%s</li>',$right),
        );
        
        return self::__HTML('ul',
                array('class'=>'calendar-nav inline'),
                $navigation);
    }
    /**
     * @return array
     */
    protected function listCalendarWeek(){
        $days =  array(
            __('Sunday','coders_artpad'),
            __('Monday','coders_artpad'),
            __('Tuesday','coders_artpad'),
            __('Wednesday','coders_artpad'),
            __('Turstay','coders_artpad'),
            __('Friday','coders_artpad'),
            __('Saturday','coders_artpad'),
        );
        
        $first = $this->getCalendarFirstWeekDay();
        
        if( $first > 0 ){
            $a = array_slice($days, $first);
            $b = array_slice($days, 0 , $first);
            return array_merge($a,$b);
        }
        
        return $days;
    }
    /**
     * @param string $date
     * @return int
     */
    protected function getCalendarYear( $date = '' ){
        if(strlen($date) === 0 ){
            $date = $this->__date();
        }
        return intval( date('Y', strtotime($date) ) );
    }
    /**
     * @param string $date
     * @return int
     */
    protected function getCalendarMonth( $date = '' ){
        if(strlen($date) === 0 ){
            $date = $this->__date();
        }
        return intval( date('m', strtotime($date) ) );
    }
    /**
     * Number of days of this month
     * @param int $date
     * @return int
     */
    protected function getMonthDays( $date ){
        if(strlen($date) === 0 ){
            $date = $this->__date();
        }
        return intval( date('t', strtotime( $date ) ) );
    }
    /**
     * @param string $date
     * @return int
     */
    protected function getCalendarDay( $date = '' ){
        if(strlen($date) === 0 ){
            $date = $this->__date();
        }
        return intval( date('d', strtotime($date) ) );
    }
    /**
     * @return int
     */
    protected function getCalendarFirstWeekDay(){
        return 1; //montay?
    }
    /**
     * @param string $date
     * @return int
     */
    protected function getCalendarWeekDay( $date = '' ){
        if(strlen($date) === 0 ){
            $date = $this->__date();
        }
        return intval( date('w', strtotime($date) ) ) - $this->getCalendarFirstWeekDay();
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
        $count = $this->getMonthDays( $date );
        $first = sprintf('%s-%s-1',$year,$month);
        $last = sprintf('%s-%s-%s',$year,$month,$count);
        
        //iterate from 1 to 29-31 day ...
        $first_day = $this->getCalendarWeekDay($first);
        $last_day = $this->getCalendarWeekDay($last);
        
        $first_week_day = $this->getCalendarFirstWeekDay();
        
        $list = array();
        
        //days previous month
        if( $first_day > $first_week_day ){
            $month_before = $month-1;
            $month_before_last = $this->getMonthDays(sprintf('%s-%s-0%s',$year,$month_before,1));
            //var_dump($month_before . ':' . $month_before_last);
            $month_before_offset = $month_before_last - $first_day + $first_week_day;
            for( $d = $month_before_offset ; $d <= $month_before_last ; $d++ ){
                $list[] = sprintf('%s-%s-%s',$year, $month_before, $d );
            }
        }
        
        //days current month
        for( $d = 0 ; $d < $count ; $d++ ){
            $list[] = $d + 1 < 10 ? 
                    sprintf('%s-%s-0%s', $year,$month, $d + 1) :
                    sprintf('%s-%s-%s', $year,$month, $d + 1) ;
        }

        //days mnth after
        if( $last_day < 6 ){
            $month_after = $month+1;
            for( $d = 1 ; $d <= 7 - $last_day ; $d++ ){
                $list[] = sprintf('%s-%s-0%s',$year, $month_after, $d );
            }
        }
        
        return $list;
    }
    /**
     * @return array
     */
    protected function listCalendarClass(){
        return array('table','calendar');
    }
    /**
     * @return array
     */
    protected function listCalendarYears(){
        $years = array();
        for( $y = 1980 ; $y < 2050 ; $y++ ){
            $years[ $y ] = $y;
        }
        return $years;
    }
    /**
     * @return array
     */
    protected function listCalendarMonths(){
        return array(
            __('January','coders_artpad'),
            __('February','coders_artpad'),
            __('March','coders_artpad'),
            __('April','coders_artpad'),
            __('May','coders_artpad'),
            __('June','coders_artpad'),
            __('July','coders_artpad'),
            __('August','coders_artpad'),
            __('September','coders_artpad'),
            __('October','coders_artpad'),
            __('November','coders_artpad'),
            __('December','coders_artpad'),
        );
    }
    /**
     * @param string|int $date
     * @return string
     */
    protected function getCalendarMonthName( $date = '' ){

        $month = $this->getCalendarMonth($date);
        
        $list = $this->listCalendarMonths();
        
        //var_dump($month);
        
        return $month > 0 ? $list[ $month - 1 ] : $list[ 0 ];
    }
    /**
     * @param string $date
     * @return string
     */
    protected function getCalendarDayName( $date ){

        $day = $this->getCalendarDay($date);

        switch( $day % 10 ){
            case 1:
                return sprintf( '%sst',$day );
            case 2:
                return sprintf( '%snd',$day );
            case 3:
                return sprintf( '%srd',$day );
            default:
                return sprintf( '%sth',$day );
        }
        
        return '';
    }
    /**
     * @param string $date
     * @return string
     */
    protected function getCalendarDate( $date = '' ){
        if(strlen($date) === 0){
            $date = $this->__date();
        }
        
        $year = $this->getCalendarYear($date);
        $month = $this->getCalendarMonthName($date);
        $day = $this->getCalendarDayName($date);
        
        return sprintf('%s %s of %s',$day,$month,$year);
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
            sprintf('<tr class="navigator"><td colspan="7">%s</td></tr>', $this->renderCalendarNavigator($date)),
            sprintf('<tr class="days"><th>%s</th></tr>', implode('</th><th>', $this->listCalendarWeek()) ),
        );
        
        //content
        $calendar = array();
        $week = array();
        $days = $this->listCalendarDays($date);
        $currentMonth = $this->getCalendarMonth($date);
        $today = date('Y-m-d');
        
        for(  $d = 0 ; $d < count( $days ) ; $d++ ){
            if( $d > 0 && $d % 7 === 0 ){
                //save and reset
                $calendar[] = sprintf('<tr>%s</tr>', implode('', $week) );
                $week = array();
            } 
            $cls = array( 'day' );
            if( $today === $date ){ $cls[] = 'today'; }
            if( $this->getCalendarMonth($days[$d]) !== $currentMonth ){ $cls[] = 'disable'; }
            $week[] = sprintf('<td class="%s">%s</td>',
                    implode(' ',$cls),
                    $this->renderCalendarRecord( $days[ $d ] ) );
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
    protected function renderCalendarRecord( $date ){
        
        return $this->getCalendarDay($date);
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
    /**
     * @param string $title
     * @param string $url
     * @return \CODERS\ArtPad\View
     */
    protected final function addNavPath( $title , $url = '' ){
        if( !array_key_exists( $title , $this->_navigator ) ){
            $this->_navigator[ $title ] = $url;
        }
        return $this;
    }
    /**
     * @return string
     */
    protected final function getAdminPageParent(){
        return get_admin_page_parent();
    }
    /**
     * @return string
     */
    protected final function getCurrentAdminPage(){
        if(is_admin()){
            $current = explode('-',preg_replace('/_/', '-', get_current_screen()->id ));
            return $current[count($current)-1];
        }
        return '';
    }
    /**
     * @return string
     */
    protected function displayNavigator(){
        
        $items = array();
        
        /*if(is_admin()){
            $current = explode('-',preg_replace('/_/', '-', get_current_screen()->id ));
            if( $current[count($current)-1] !== get_admin_page_parent()){
                $items[] = sprintf('<li class="root"><a href="%s" target="_self">%s</a></li>',
                        Request::url('admin.main'),
                        __('Artist Pad','coders_artpad'));
                $items[] = sprintf('<li class="parent-%s">%s</li>',
                    get_admin_page_parent(),
                    get_admin_page_title());
            }
            else{
                $items[] = sprintf('<li class="root">%s</li>', get_admin_page_title());
            }
        }*/
        
        $nodes = array_keys($this->_navigator);

        for( $i = 0 ; $i < count($nodes) ; $i++ ){
            
            $class = $i === 0 ? 'home' : 'node';
            $title = $nodes[$i ];
            $url = $this->_navigator[ $title ];
            
            $content = $i < count($nodes) - 1 ?
                    $this->renderLink($url, $title) :
                    self::__HTML('span', array('class'=>$class),$title);
            
            $items[] = sprintf('<li class="%s">%s</li>',$class,$content);
        }
        
        /*foreach( $this->_navigator as $title => $url ){
            $content =  strlen($url) ?
                    $this->renderLink($url, $title) :
                    self::__HTML('span', array( 'class' => 'current', ), $title);
            
            $items[] = sprintf('<li>%s</li>',$content);
        }*/
        return count( $items ) ?
                self::__HTML('ul', array( 'class' => 'navigator panel left inline','id'=>'id_artpad_nav' ), $items ) :
                $this->__class();
    }
    /**
     * 
     */
    protected function displayDebug(){
        
        var_dump(\ArtPad::stamp( TRUE ));
        
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
        return sprintf('%s/modules/%s/views/html/%s.php',
                CODERS__REPOSITORY__DIR,
                strtolower( $this->module( true ) ),
                $view );
    }
    /**
     * @param String $url
     * @return \CODERS\ArtPad\View
     */
    protected function attachScript( $url ){
        
        print self::__HTML('script', array(
            'type' => 'text/javascript',
            'src' => self::assetUrl($url, $this->module(TRUE)),
        ));
        
        return $this;
    }
    /**
     * @param string $url
     * @param string $title
     * @param string $target
     * @param string $class
     * @return string|HTML
     */
    protected static function renderLink( $url , $title , $target = '_self' , $class = 'link') {
        
        return self::__HTML('a', array(
            'href' => $url,
            'target' => $target,
            'class' => $class,
        ), $title);
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
     * @return \CODERS\ArtPad\View
     */
    public static final function create( $request ){
        
        $call = explode( '.',$request);
        $module = $call[0];
        $view = count( $call ) > 1 ? $call[1] : 'main';
        
        $path = sprintf('%s/modules/%s/views/%s.php',CODERS__REPOSITORY__DIR,$module,$view);
        $class = sprintf('\CODERS\ArtPad\%s\%sView',$module,$view);
        
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
/**
 * 
 */
final class ModelAdapter{
    /**
     * @var \CODERS\ArtPad\Model
     */
    private $_model = null;
    /**
     * @param \CODERS\ArtPad\Model $model
     */
    private final function __construct( \CODERS\ArtPad\Model $model ) {
        $this->_model = $model;
    }
    /**
     * @return string
     */
    public final function __toString() {
        return get_class($this->_model);
    }
    /**
     * @param string $name
     */
    public final function __get($name) {
        $this->_model->$name();
    }
    /**
     * @param string $name
     * @param array $arguments
     */
    public final function __call($name , $arguments ) {
        $this->_model->$name( $arguments );
    }
    /**
     * @param \CODERS\ArtPad\Model $model
     */
    public static final function import( \CODERS\ArtPad\Model $model ){

        return new ModelAdapter( $model );
    }
}







