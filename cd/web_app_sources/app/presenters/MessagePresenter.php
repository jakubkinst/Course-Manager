<?php

/**
 * Presenter dedicated to Message Module.
 * Provides sending messages between users.
 *
 * @author     Jakub Kinst <jakub@kinst.cz> (@link http://jakub.kinst.cz)
 * @package    Course-Manager/Presenters
 */
class MessagePresenter extends BasePresenter {
	/*
	 * =============================================================
	 * =======================  Actions ============================
	 */

	/**
	 * Messages homepage - redirect to inbox
	 * @param int $cid Course ID
	 */
	public function actionHomepage($cid) {
		// redirect to inbox
		$this->redirect('inbox');
	}

	/**
	 * New message page
	 * @param string $email pre-filled e-mail address
	 */
	public function actionNew($email, $subject = null, $content = null) {
		$form = $this->getComponent('newMessage');
		$form->setDefaults(array(
			'to' => $email,
			'subject' => $subject,
			'content' => $content
		));
	}

	/**
	 * Inbox page
	 */
	public function renderInbox() {
		$this->template->inbox = MessageModel::getInbox();
	}

	/**
	 * Outbox page
	 */
	public function renderOutbox() {
		$this->template->outbox = MessageModel::getOutbox();
	}

	/**
	 * Show message
	 * @param int $mid Message ID
	 */
	public function actionShowMessage($mid) {

		MessageModel::setRead($mid);
	}

	/**
	 * Show message
	 * @param int $mid Message ID
	 */
	public function renderShowMessage($mid) {
		$message = MessageModel::getMessage($mid);
		$uid = UserModel::getLoggedUser()->id;
		Debug::barDump($uid);
		Debug::barDump($message->from);
		Debug::barDump($message->to);
		if ($message->from->id == $uid || $message->to->id == $uid)
			$this->template->message = $message;
		else
			throw new BadRequestException;
	}

	/**
	 * New Message Form
	 * @return AppForm
	 */
	protected function createComponentNewMessage() {

		function myValidator($item) {
			$result = dibi::query('SELECT id FROM user WHERE email=%s', $item->getValue());
			return!(count($result) === 0);
		}

		$form = new AppForm;
		$form->setTranslator($this->translator);
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
	 * New Message form handler
	 * @param AppForm $form
	 */
	public function newMessageFormSubmitted(AppForm $form) {
		$values = $form->getValues();
		if (MessageModel::sendMessage($values)) {
			$this->flashMessage('Message Sent.', $type = 'success');
			$this->redirect('inbox');
		}
		else
			$this->flashMessage('There was an error sending the message.', $type = 'error');
	}

}
