<?php

/**
 * LessonPresenter
 *
 * @author Jakub Kinst
 */
class LessonPresenter extends BasePresenter {

    /** @persistent Lesson ID */
    public $lid;

    /**
     * Homepage template render
     * @param type $lid 
     */
    public function renderHomepage($lid) {
        
        // check if lesson id corresponds to course id
        if (CourseModel::getCourseIDByLessonID($lid) != $this->cid) {
            throw new BadRequestException;
        }
        $this->lid = $lid;
	
        $this->template->lesson = CourseModel::getLessonByID($this->lid);
	$this->template->resources = ResourceModel::getLessonFiles($this->lid);
        $this->template->comments = CourseModel::getComments($this->lid);
        
    }
    public function actionAddResource($lid){
	$this->checkTeacherAuthority();
	$uploader  = new Uploader($this, 'uploader');
	$uploader->cid = $this->cid;
	$uploader->lid = $lid;
    }

    /**
     * Form factory - Add comment
     * @return AppForm 
     */
    protected function createComponentCommentForm() {
        $form = new AppForm;
	$form->setTranslator($this->translator);
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