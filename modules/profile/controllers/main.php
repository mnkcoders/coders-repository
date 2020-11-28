<?php namespace CODERS\Repository\Profile;
/**
 * 
 */
final class MainController extends \CODERS\Repository\Response{
    /**
     * @param \CODERS\Repository\Request $request
     * @return boolean
     */
    protected final function default_action(\CODERS\Repository\Request $request) {
        $model = $this->importModel('user.account');
        $view = $this->importView('user.profile');
        $view->setModel($model)->setLayout('profile')->display();
        return TRUE;
    }
}


