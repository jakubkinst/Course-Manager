<?php

/**
 * Description of LessonPresenter
 *
 * @author JerRy
 */
class LessonPresenter extends BasePresenter {

   
    /** @persistent */ public $lessonid;
    public $isTeacher;
    public $isStudent;
    public function actionHomepage($lessonid){
        $this->lessonid = $lessonid;
        $this->isTeacher = CourseModel::isTeacher(Environment::getUser()->getIdentity(),CourseModel::getCourseIDByLessonID($this->lessonid));
        $this->isStudent = CourseModel::isStudent(Environment::getUser()->getIdentity(),CourseModel::getCourseIDByLessonID($this->lessonid));
        
        $this->template->isStudent = $this->isStudent;
        $this->template->isTeacher = $this->isTeacher;
        if ($this->isTeacher || $this->isStudent){
            $this->template->lesson = CourseModel::getLessonByID($this->lessonid);
            $this->template->comments = CourseModel::getComments($this->lessonid);
            foreach ($this->template->comments as $comment){
                $comment['user'] = UserModel::getUser($comment['User_id']);
            }
        }
    }
    
     protected function createComponentCommentForm() {
        $form = new AppForm;
        $form->addTextArea('content', 'Comment:')
                ->addRule(Form::FILLED, 'Fill comment.');
        $form->addSubmit('send', 'Post');
        $form->addHidden('lesson_id', $this->lessonid);
        $form->onSubmit[] = callback($this, 'commentFormSubmitted');
        
        return $form;
    }
    public function commentFormSubmitted($form){$values = $form->getValues();
        $values['added'] = new DateTime;
        $values['user_id'] = UserModel::getUserID(Environment::getUser()->getIdentity());
        CourseModel::addComment($values);
        $this->redirect('lesson:homepage',$values['lesson_id'] );        
        
    }

}