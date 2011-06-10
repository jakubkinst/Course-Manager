<?php

/**
 * Description of LessonPresenter
 *
 * @author JerRy
 */
class LessonPresenter extends BasePresenter {

   
    public $lessonid;
    public $approved;
    public function actionHomepage($lid){
        $this->lessonid = $lid;
        $this->approved = CourseModel::approvedUser(Environment::getUser()->getIdentity(),CourseModel::getCourseIDByLessonID($lid));
           
        $this->template->approved = $this->approved;
        if ($this->approved){
            $this->template->lesson = CourseModel::getLessonByID($lid);
            $this->template->comments = CourseModel::getComments($lid);
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