<?php

/**
 * Resource presenter.
 *
 */
class ForumPresenter extends BaseCoursePresenter {


   
    /**
     * Homepage template render
     * @param type $cid 
     */
    public function renderHomepage($cid) {
        $this->checkAuthorization();
        
        // paging control
        $pages = new VisualPaginator($this, 'pages');
        $paginator = $pages->getPaginator();
        $paginator->itemsPerPage = 20;
        $paginator->itemCount = ForumModel::countTopics($cid);
        
        $this->template->topics = ForumModel::getTopics($cid,$paginator->offset,$paginator->itemsPerPage);
        
    }
    
    public function renderShowTopic($tid){
        // check if topic id corresponds to course id
        if (ForumModel::getCourseIDByTopicID($tid) != $this->cid) 
            throw new BadRequestException;
        $this->checkAuthorization();
        
        // paging control
        $pages = new VisualPaginator($this, 'pages');
        $paginator = $pages->getPaginator();
        $paginator->itemsPerPage = 2;
        $paginator->itemCount = ForumModel::countReplies($tid);
        
        $this->template->topic = ForumModel::getTopic($tid);
        $this->template->replies = ForumModel::getReplies($tid,$paginator->offset,$paginator->itemsPerPage);
    }
    
     /**
     * Form factory - Add topic Form
     * @return AppForm 
     */
    protected function createComponentAddTopic() {
        $form = new AppForm;
	$form->setTranslator($this->translator);        
        $form->addText('label', 'Topic:*')        
                ->addRule(Form::FILLED, 'Fill topic.');
        $form->addTextArea('content', 'Content:*')
                ->addRule(Form::FILLED, 'Fill content.');

        $form->addSubmit('post', 'Post');
        $form->onSubmit[] = callback($this, 'addTopicFormSubmitted');
        return $form;
    }

    /**
     * add topic form handler
     * @param type $form 
     */
    public function addTopicFormSubmitted($form) {
        $values = $form->getValues();
        if (ForumModel::addTopic($values,$this->cid)){        
            $this->flashMessage('Topic added.', $type = 'success');
        }
        else 
            $this->flashMessage('There was an error adding the Topic.', $type = 'error');
        
    }
      /**
     * Form factory - Add reply Form
     * @return AppForm 
     */
    protected function createComponentAddReply() {
        $form = new AppForm;    
	$form->setTranslator($this->translator);    
        $form->addTextArea('content', 'Reply:*')
                ->addRule(Form::FILLED, 'Fill content.');

        $form->addSubmit('post', 'Post');
        $form->onSubmit[] = callback($this, 'addReplyFormSubmitted');
        return $form;
    }

    /**
     * Add reply form handler
     * @param type $form 
     */
    public function addReplyFormSubmitted($form) {
        $values = $form->getValues();
        if (ForumModel::addReply($values,$this->getParam('tid'))){        
            $this->flashMessage('Reply added.', $type = 'success');
        }
        else 
            $this->flashMessage('There was an error adding the Reply.', $type = 'error');
        
    }

   
}
