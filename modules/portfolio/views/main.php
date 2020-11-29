<?php namespace CODERS\ArtPad\Portfolio;
/**
 * 
 */
final class MainView extends \CODERS\ArtPad\View{
    /**
     * 
     * @return \CODERS\ArtPad\Portfolio\MainView
     */
   public final function display() {
       
        get_header();
        
        parent::display();
        
        get_footer();

        return $this;       
   }
   /**
    * @param string $project_id
    * @return string|URL
    */
   protected final function getProjectLink( $project_id ){
       return \CODERS\ArtPad\Request::url('portfolio.project', array('ID'=>$project_id));
   }
}



