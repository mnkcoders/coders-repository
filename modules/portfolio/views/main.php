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
}



