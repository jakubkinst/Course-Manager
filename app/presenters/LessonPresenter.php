<?php

/**
 * LessonPresenter
 *
 * @author Jakub Kinst
 */
class LessonPresenter extends BaseCoursePresenter {

    public $lid;
    
    protected function beforeRender() {
	parent::beforeRender();
	if (null!= $this->getParam('lid')){
	    $this->lid = $this->getParam('lid');	    
	    // check if lesson id corresponds to course id
	    if (CourseModel::getCourseIDByLessonID($this->lid) != $this->cid)
		throw new BadRequestException;
	}
    }

    /**
     * Homepage template render
     * @param type $lid 
     */
    public function renderHomepage($lid) {

	$this->paginator->itemsPerPage = 10;
        $this->paginator->itemCount = CourseModel::countComments($this->lid);
	
	$this->template->lesson = CourseModel::getLessonByID($this->lid);
	$this->template->resources = ResourceModel::getLessonFiles($this->lid);
	$this->template->comments = CourseModel::getComments($this->lid,$this->paginator->offset,$this->paginator->itemsPerPage);
    }

    public function actionAddResource($lid) {
	$this->checkTeacherAuthority();
	$uploader = new Uploader($this, 'uploader');
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
	if (CourseModel::addComment($values,$this->lid)){
	    $this->flashMessage('Comment added.', $type = 'success');
	    $this->redirect('lesson:homepage',$this->lid);
	}
	else $this->flashMessage('There was an error adding the comment.', $type = 'error');
    }

    public function handleDelete($lid) {	
	$this->checkTeacherAuthority();
	if (CourseModel::deleteLesson($lid)) {
	    $this->flashMessage('Lesson was successfully deleted', 'success');
	    $this->redirect('Course:homepage');
	} else
	    $this->flashMessage('There was an error deleting the lesson', 'error');
    }

    public function renderEdit($lid) {
	$this->checkTeacherAuthority();
	$lesson = CourseModel::getLessonByID($lid);
	$this->getComponent('editForm')->setDefaults($lesson);
    }

    public function createComponentEditForm() {
	$form = new AppForm;
	$form->setTranslator($this->translator);
	$form->addText('topic', 'Topic:*')
		->addRule(Form::FILLED, 'Set lesson topic.');
	$form->addTextArea('description', 'Lesson description:')->getControlPrototype()->class = "texyla";
	$form->addSubmit('send', 'Save');
	$form->onSubmit[] = callback($this, 'editFormSubmitted');
	return $form;
    }

    public function editFormSubmitted($form) {
	$values = $form->getValues();
	CourseModel::editLesson($this->lid, $values);
	$this->flashMessage('Lesson edited', $type = 'success');
	$this->redirect('homepage');
    }

}