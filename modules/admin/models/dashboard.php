<?php namespace CODERS\Repository\Admin;
/**
 * 
 */
final class DashboardModel extends \CODERS\Repository\Model {

    /**
     * @param string $image_id
     * @return string
     */
    protected final function getImageUrl( $image_id ){
        $img = wp_get_attachment_image_src($image_id);
        return FALSE !== $img ? $img[ 0 ] : '';
    }
    /**
     * @return string
     */
    protected final function getProjectUrl(){
        return '';
    }
    /**
     * @param int $paginagion
     * @return array
     */
    protected final function listProjects($paginagion = 20) {

        $query = $this->newQuery();

        $projects = $query->select('project');

        $output = array();
        foreach ($projects as $meta) {
            $output[$meta['ID']] = array(
                'title' => $meta['title'],
                'status' => $meta['status'],
                'image_id' => $meta['image_id'],
                'progress' => random_int(0, 100),
                'subscribers' => random_int(0, 1000),
                'date_created' => $meta['date_created'],
                'date_updated' => $meta['date_updated'],
            );
        }
        return $output;
    }

}
