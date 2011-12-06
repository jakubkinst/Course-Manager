<?php

/**
 * Resource presenter.
 *
 */
class ForumPresenter extends BaseCoursePresenter {

    public $tid;
    
    protected function startup() {
	if (null != $this->getParam('tid')){	    
	    $this->tid = $this->getParam('tid');
	    $this->cid = ForumModel::getCourseIDByTopicID($this->tid);
	}
	parent::startup();
    }
   
    /**
     * Homepage template render
     * @param type $cid 
     */
    public function renderHomepage($cid) {
        $this->paginator->itemsPerPage = 20;
        $this->paginator->itemCount = ForumModel::countTopics($cid);        
        $this->template->topics = ForumModel::getTopics($cid,$this->paginator->offset,$this->paginator->itemsPerPage);
        
    }
    
    public function renderShowTopic($tid){ 
        // paging control
        $this->paginator->itemsPerPage = 20;
        $this->paginator->itemCount = ForumModel::countReplies($tid);
        
        $this->template->topic = ForumModel::getTopic($tid);
        $this->template->replies = ForumModel::getReplies($tid,$this->paginator->offset,$this->paginator->itemsPerPage);
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
