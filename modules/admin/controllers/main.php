<?php namespace CODERS\ArtPad\Admin;
/**
 * 
 */
final class MainController extends \CODERS\ArtPad\Response{
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function default_action(\CODERS\ArtPad\Request $request) {
        $model = $this->importModel('admin.dashboard');
        $view = $this->importView('admin.dashboard');
        $view->setModel($model)->setLayout('dashboard')->display();
        return TRUE;
    }
    
    protected final function project_action( \CODERS\ArtPad\Request $request ){
        
        $project = \CODERS\ArtPad\Project::load( $request->get('ID',''));
        
        $this->importView('admin.project')
                ->setModel($project)
                ->setLayout('project')
                ->display();
        
        return TRUE;
    }
    /**
     * 
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function tier_action( \CODERS\ArtPad\Request $request ){
        
        $tier_id = $request->get('ID','');
        
        $display = $this->importView('admin.tier');
        
        if(strlen($tier_id)){
            $tier = \CODERS\ArtPad\Tier::Load( $tier_id );
            if( FALSE !== $tier ){

                $display->setModel($tier)
                        ->setLayout('tier')
                        ->display();
                return TRUE;
            }
        }

        $display->setLayout('tier.error')->display();
        
        return TRUE;
    }
    
    protected final function dashboard_action( \CODERS\ArtPad\Request $request ){
        
        return TRUE;
    }
    
    protected final function remove_action( \CODERS\ArtPad\Request $request ){
        
        return TRUE;
    }
    
    protected final function create_action( \CODERS\ArtPad\Request $request ){
        
        return TRUE;
    }
    /**
     * 
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function upload_action( \CODERS\ArtPad\Request $request ){
        
        $files = \CODERS\ArtPad\Resource::upload('upload', $request->get('collection','default'));
        $names = array();
        foreach($files as $file ){
            $names[] = $file->name;
        }
        return $this->default_action($request->redirect('admin.main.default',array('files'=>$names)));
    }
    
    protected final function collection_action( \CODERS\ArtPad\Request $request ){
        
        var_dump(\CODERS\ArtPad\Resource::collection(0));
        
        return TRUE;
    }
}


