<?php

/**
 * Description of LessonPresenter
 *
 * @author JerRy
 */
class LessonPresenter extends BasePresenter {

   
    /** @persistent Lesson ID */ 
    public $lid;
    
    public $isTeacher;
    public $isStudent;
    
    public function init($lid){
        $this->lid = $lid;
        $this->isTeacher = CourseModel::isTeacher(Environment::getUser()->getIdentity(),CourseModel::getCourseIDByLessonID($this->lid));
        $this->isStudent = CourseModel::isStudent(Environment::getUser()->getIdentity(),CourseModel::getCourseIDByLessonID($this->lid));
        
        $this->template->isStudent = $this->isStudent;
        $this->template->isTeacher = $this->isTeacher;
        if ($this->isTeacher || $this->isStudent){
            $this->template->lesson = CourseModel::getLessonByID($this->lid);
            $this->template->comments = CourseModel::getComments($this->lid);
            foreach ($this->template->comments as $comment){
                $comment['user'] = UserModel::getUser($comment['User_id']);
            }
        }
    }
    
    public function renderHomepage($lid){
        $this->init($lid);
    }
    
     protected function createComponentCommentForm() {
        $form = new AppForm;
        $form->addTextArea('content', 'Comment:')
                ->addRule(Form::FILLED, 'Fill comment.');
        $form->addSubmit('send', 'Post');
        $form->addHidden('lesson_id', $this->lid);
        $form->onSubmit[] = callback($this, 'commentFormSubmitted');
        
        return $form;
    }
    public function commentFormSubmitted($form){$values = $form->getValues();
        $values['added'] = new DateTime;
        $values['user_id'] = UserModel::getUserID(Environment::getUser()->getIdentity());
        CourseModel::addComment($values);
        $this->flashMessage('Comment added.', $type = 'success');
        $this->redirect('lesson:homepage');              
    }

}