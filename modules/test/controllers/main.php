<?php namespace CODERS\ArtPad\Test;
/**
 * 
 */
final class MainController extends \CODERS\ArtPad\Response{
    
    
    protected function default_action(\CODERS\ArtPad\Request $request) {
        
        var_dump($this->query());
        
        
        return TRUE;
    }
    
    private final function query(){
        
        $args = array(  
            'post_type' => 'artpad_project',
            'post_status' => 'publish',
            'posts_per_page' => 8, 
            'orderby' => 'title', 
            'order' => 'ASC', 
        );

        $loop = new \WP_Query( $args ); 
        
        return $loop->posts;
    }
}