<?php namespace CODERS\ArtPad\Profile;
/**
 * 
 */
final class MainController extends \CODERS\ArtPad\Response{
    /**
     * @param \CODERS\ArtPad\Request $request
     * @return boolean
     */
    protected final function default_action(\CODERS\ArtPad\Request $request) {
        $model = $this->model('user.account');
        $view = $this->view('user.profile');
        $view->setModel($model)->setLayout('profile')->display();
        return TRUE;
    }
}


