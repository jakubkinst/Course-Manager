<?php

/**
 * Description of CourseListPresenter
 *
 * @author JerRy
 */
class CourseListPresenter extends BasePresenter {

    /**
     * (non-phpDoc)
     *
     * @see Nette\Application\Presenter#startup()
     */
    protected function startup() {
        parent::startup();
    }

    public function actionDefault() {
        
    }

    public function renderDefault() {
        $this->template->courses = CourseListModel::getCourses();
    }

}