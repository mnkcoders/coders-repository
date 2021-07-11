<?php namespace CODERS\ArtPad\Admin;
/**
 * 
 */
final class PostModel extends \CODERS\ArtPad\Model{
    /**
     * @param array $data
     */
    protected final function __construct(array $data = array()) {
        
        $this->define('ID',self::TYPE_NUMBER,array('value'=>0))
            ->define('post_title',self::TYPE_TEXT  )
            ->define('post_content',self::TYPE_TEXTAREA  )
            ->define('post_excerpt',self::TYPE_TEXTAREA  )
            ->define('post_status',self::TYPE_TEXT  )
            ->define('post_parent',self::TYPE_NUMBER )
            ->define('post_date',self::TYPE_DATETIME  )
            ->define('post_author',self::TYPE_NUMBER )
            ->__preload($data);
        
        parent::__construct( $data );
    }
    /**
     * @param array $input
     * @return \CODERS\ArtPad\Admin\PostModel
     */
    protected final function __populate(array $input) {
        
        if(array_key_exists('ID', $input)){
            $this->__preload($input['ID']);
        }
        //var_dump($input);
        return parent::__populate($input);
    }
    /**
     * @param int id
     * @return \CODERS\ArtPad\Admin\PostModel
     */
    private final function __preload( $ID ){
        $post = get_post( $ID );
        if( FALSE !==  $post ){
        //var_dump($post);
            foreach( $this->listElements() as $element ){
                //var_dump($element . ':' . $post->$element);
                if( isset( $post->$element ) ){
                    $this->setValue($element, $post->$element );
                }
            }
        }
        return $this;
    }
    /**
     * @return string
     */
    public final function getTitle(){
        return $this->value('post_title');
    }
    /**
     * @return string
     */
    public final function getContent(){
        return $this->value('post_content');
    }
    /**
     * @return string
     */
    public final function getExcerpt(){
        return $this->value('post_excerpt');
    }
    /**
     * @return int
     */
    public final function getParent(){
        return $this->value('post_parent');
    }
    /**
     * @return array
     */
    public final function listAdjacent(){
        
        $current = $this->value('ID');
        
        $parent = $this->value('post_parent');
        
        $child_args = array(
                'post_parent' => $parent,
                'post_type' => 'artpad_post',
                'numberposts' => -1 );
        
        $adjacent = get_children($child_args);
        
        $output = array(
            $parent => __('PARENT','coders_artpad')
        );
        
        foreach( $adjacent as $posts ){
            if( $posts->ID != $current ){
                $output[ $posts->ID ] = $posts->post_title;
            }
        }
        
        return $output;
    }
    /**
     * @return array
     */
    public final function listPosts(){
        
        $output = array();
        
        $db = new \WP_Query(array(  
            'post_type' => 'artpad_post',
            //'post_status' => 'publish',
            //'posts_per_page' => -1, 
            //'orderby' => 'title', 
            'orderby' => 'post_date', 
            'order' => 'ASC', 
        ));
        
        foreach( $db->posts as $post ){
            $output[ $post->ID ] = array(
                'title' =>  $post->post_title,
                'url' => get_permalink($post->ID)
            );
        }
        
        return $output;
    }
    /**
     * 
     * @return \CODERS\ArtPad\Admin\PostModel
     */
    public final function save(){
        $values = $this->listValues();
        if( $values['ID'] > 0 ){
            $ID = wp_update_post( $values );
            if( $ID > 0 ){
                //
            }
        }
        
        return $this;
    }
    
    public final function remove(){
        
        return $this;
    }
    
    public final function load(){
        
        $data = array();
        
        return $this->import($data);
    }
}

