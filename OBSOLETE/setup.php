<?php defined('ABSPATH') or die;
/**
 * @global wpdb $wpdb
 * @global string $table_prefix
 * @return boolean
 */
function coders_repository_db_install(){
    
    global $wpdb,$table_prefix;
    
    $script_path = sprintf('%s/sql/setup.sql',__DIR__);
    
    if(file_exists($script_path)){
        
        $script_file = file_get_contents($script_path);
        
        if( $script_file !== FALSE && strlen($script_file)){
            
            $script_sql = preg_replace('/{{TABLE_PREFIX}}/',$table_prefix,$script_file);

            if($wpdb->query($script_sql)){

                return TRUE;
            }
        }
    }

    return FALSE;
}

coders_repository_db_install();
