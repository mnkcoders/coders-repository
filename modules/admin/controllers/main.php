<?php namespace CODERS\Repository\Admin;
/**
 * 
 */
final class MainController extends \CODERS\Repository\Response{
    /**
     * @param \CODERS\Repository\Request $request
     * @return boolean
     */
    protected final function default_action(\CODERS\Repository\Request $request) {
        $model = $this->importModel('admin.dashboard');
        $view = $this->importView('admin.dashboard');
        $view->setModel($model)->setLayout('dashboard')->display();
        return TRUE;
    }
    
    protected final function project_action( \CODERS\Repository\Request $request ){
        
        $project = \CODERS\Repository\Project::load( $request->get('ID',''));
        
        $this->importView('admin.project')
                ->setModel($project)
                ->setLayout('project')
                ->display();
        
        return TRUE;
    }
    
    protected final function dashboard_action( \CODERS\Repository\Request $request ){
        
        return TRUE;
    }
    
    protected final function remove_action( \CODERS\Repository\Request $request ){
        
        return TRUE;
    }
    
    protected final function create_action( \CODERS\Repository\Request $request ){
        
        return TRUE;
    }
    /**
     * 
     * @param \CODERS\Repository\Request $request
     * @return boolean
     */
    protected final function upload_action( \CODERS\Repository\Request $request ){
        
        $files = \CODERS\Repository\Resource::upload('upload', $request->get('collection','default'));
        $names = array();
        foreach($files as $file ){
            $names[] = $file->name;
        }
        return $this->default_action($request->redirect('admin.main.default',array('files'=>$names)));
    }
    
    protected final function collection_action( \CODERS\Repository\Request $request ){
        
        var_dump(\CODERS\Repository\Resource::collection(0));
        
        return TRUE;
    }
}


