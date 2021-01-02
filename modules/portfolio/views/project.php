<?php namespace CODERS\ArtPad\Portfolio;
/**
 * 
 */
final class ProjectView extends \CODERS\ArtPad\View{
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
    * @param string $type
    * @return boolean
    */
   public final function isImage( $type ){
       $format = array(
           'image/png',
           'image/gif',
           'image/bmp',
           'image/jpg',
       );
       
       return in_array($type, $format);
   }
   /**
    * @param string $rid
    * @return string
    */
   public final function getMediaUrl( $rid ){
       return \CODERS\ArtPad\Resource::link($rid);
   }
   /**
    * @param string $rid
    * @return string
    */
   public final function getItemUrl( $rid ){
       return \CODERS\ArtPad\Request::url('portfolio.project',array(
           'ID'=> $this->ID,
           'item' => strlen($rid) ? $rid : $this->collection_id ));
   }


   public final function displayImage( ){
       
   }
}



