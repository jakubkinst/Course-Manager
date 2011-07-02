<?php

/**
 * LessonPresenter
 *
 * @author Jakub Kinst
 */
class LessonPresenter extends BasePresenter {

    /** @persistent Lesson ID */
    public $lid;
    /** Boolean indicating user privileges */
    public $isTeacher;
    /** Boolean indicating user privileges */
    public $isStudent;

    /**
     * Initialize variables for template
     * @param type $cid 
     */
    public function init($lid) {
        $this->lid = $lid;
        $this->isTeacher = CourseModel::isTeacher(Environment::getUser()->getIdentity(), CourseModel::getCourseIDByLessonID($this->lid));
        $this->isStudent = CourseModel::isStudent(Environment::getUser()->getIdentity(), CourseModel::getCourseIDByLessonID($this->lid));

        $this->template->isStudent = $this->isStudent;
        $this->template->isTeacher = $this->isTeacher;
        if ($this->isTeacher || $this->isStudent) {
            $this->template->lesson = CourseModel::getLessonByID($this->lid);
            $this->template->comments = CourseModel::getComments($this->lid);
            foreach ($this->template->comments as $comment) {
                $comment['user'] = UserModel::getUser($comment['User_id']);
            }
        }
    }

    /**
     * Before Render security check
     */
    public function beforeRender() {
        parent::beforeRender();
        if (!$this->logged) {
            $this->flashMessage('Please login.', $type = 'unauthorized');
            $this->redirect('courselist:homepage');
        }
    }

    /**
     * Homepage template render
     * @param type $lid 
     */
    public function renderHomepage($lid) {
        $this->init($lid);
    }

    /**
     * Form factory - Add comment
     * @return AppForm 
     */
    protected function createComponentCommentForm() {
        $form = new AppForm;
        $form->addTextArea('content', 'Comment:')
                ->addRule(Form::FILLED, 'Fill comment.');
        $form->addSubmit('send', 'Post');
        $form->onSubmit[] = callback($this, 'commentFormSubmitted');

        return $form;
    }

    /**
     * Add comment form handler
     * @param type $form 
     */
    public function commentFormSubmitted($form) {
        $values = $form->getValues();
        $values['added'] = new DateTime;
        $values['user_id'] = UserModel::getUserID(Environment::getUser()->getIdentity());

        $values['Lesson_id'] = $this->lid;
        CourseModel::addComment($values);
        $this->flashMessage('Comment added.', $type = 'success');
        $this->redirect('lesson:homepage');
    }

}