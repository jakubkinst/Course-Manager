<?php

/**
 * Message presenter.
 *
 */
class MessagePresenter extends MasterPresenter {   
    /**
     * Homepage template render
     * @param type $cid 
     */    
    public function actionHomepage($cid){
        // redirect to inbox
        $this->redirect('inbox');
    }
    
    public function renderInbox($cid) {     
        $this->template->inbox = MessageModel::getInbox();  
    }    
    public function renderOutbox($tid){  
       $this->template->outbox = MessageModel::getOutbox();       
    }
    public function actionShowMessage($mid){
        MessageModel::setRead($mid);
    }
    public function renderShowMessage($mid) {     
        $this->template->message = MessageModel::getMessage($mid);
        
    }
    
     /**
     * Form factory - Add topic Form
     * @return AppForm 
     */
    protected function createComponentNewMessage() {
        
        function myValidator($item) {
            $result = dibi::query('SELECT id FROM user WHERE email=%s', $item->getValue());
            return !(count($result) === 0);
        }
        
        $form = new AppForm;        
        $form->addText('to', 'To:*')
                ->setEmptyValue('@')
                ->addRule(Form::EMAIL, 'Enter valid e-mail')
                ->addRule('myValidator', 'This e-mail address is not registered.');
        $form->addText('subject', 'Subject:');
        $form->addTextArea('content', 'Content:');        
        $form->addSubmit('post', 'Send');
    
        $form->onSubmit[] = callback($this, 'newMessageFormSubmitted');
        return $form;
    }

    /**
     * add topic form handler
     * @param type $form 
     */
    public function newMessageFormSubmitted($form) {
        $values = $form->getValues();
        if (MessageModel::sendMessage($values)){        
            $this->flashMessage('Message Sent.', $type = 'success');
            $this->redirect('inbox');
        }
        else 
            $this->flashMessage('There was an error sending the message.', $type = 'error');
        
    }
       
}
