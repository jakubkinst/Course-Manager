<?php

/**
 * Presenter dedicated to Assignment module of the application
 *
 * @author     Jakub Kinst <jakub@kinst.cz> (@link http://jakub.kinst.cz)
 * @package    Course-Manager/Presenters
 */
class AssignmentPresenter extends BaseCoursePresenter {
	/*
	 * =============================================================
	 * ==================     Variables    =========================
	 */

	/**
	 * Assignment ID
	 * @var int
	 */
	var $aid;

	/** @persistent */
	public $cid;

	/**
	 * Is this assignment auto-correcting ?
	 * @var boolean
	 */
	var $autocorrect = false;

	/*
	 * =============================================================
	 * =================   Parent overrides   ======================
	 */

	protected function startup() {
		// set Assignment id if it is available
		if (null != $this->getParam('aid')) {
			$this->aid = $this->getParam('aid');
			$this->cid = AssignmentModel::getCourseIDByAssignmentID($this->aid);
		}
		parent::startup();
	}

	/*
	 * =============================================================
	 * =======================  Actions ============================
	 */

	/**
	 *
	 * @param int $cid Course ID
	 */
	public function renderHomepage($cid) {
		$this->template->assignments = AssignmentModel::getAssignments($this->cid);
	}

	/**
	 * Show assignment details
	 * @param int $aid Assignment ID
	 */
	public function actionShow($aid) {
		$this->aid = $aid;
	}

	public function renderEditInfo($aid) {
		$this->checkTeacherAuthority();
		$this['editInfoForm']->setDefaults(AssignmentModel::getAssignment($aid));
	}

	/**
	 * 	Show assignment details
	 * @param int $aid Assignment ID
	 */
	public function renderShow($aid) {
		$assignment = AssignmentModel::getAssignment($aid);
		$this->template->assignment = $assignment;
		$this->template->isSolved = AssignmentModel::isSolved($aid);
		$this->template->canSolve = AssignmentModel::canSolve($aid);
	}

	/**
	 * Assignment solving
	 * @param int $aid Assignment ID
	 */
	public function actionSolve($aid) {
		if (!AssignmentModel::canSolve($aid, 2))
			throw new BadRequestException;
		$this->aid = $aid;
	}

	/**
	 * Assignment solving
	 * @param int $aid Assignment ID
	 */
	public function renderSolve($aid) {
		$assignment = AssignmentModel::getAssignment($aid);
		$this->template->assignment = $assignment;
		$this->template->questions = AssignmentModel::getQuestions($aid);
		if (AssignmentModel::isSolved($aid)) {
			$form = $this->getComponent('solveForm');
			$anwsers = AssignmentModel::getAnwsers($aid);
			$form->setDefaults($anwsers);
			$this->template->currentAnswers = $anwsers;
		}
		else
			AssignmentModel::startSolving($aid);

		// set real endtime for template
		// (time when the form will be submitted automatically)
		$realEndTime = AssignmentModel::getRealEndTime($aid);
		if (date_sub($realEndTime, date_interval_create_from_date_string('1 day')) > new DateTime)
			$this->template->realEndTime = date_add(new DateTime, date_interval_create_from_date_string('1 day'))->format('Y-m-d H:i:s');
		else
			$this->template->realEndTime = AssignmentModel::getRealEndTime($aid)->format('Y-m-d H:i:s');
	}

	/**
	 * Adding new assignment
	 * @param boolean $ac auto-correct
	 */
	public function actionAdd($ac) {
		$this->autocorrect = $ac;
	}

	/**
	 * Adding new assignment
	 * @param boolean $ac auto-correct
	 */
	public function renderAdd($ac) {
		$this->checkTeacherAuthority();
	}

	/**
	 * Assignment editting - adding questions
	 * @param int $aid Assignment ID
	 */
	public function actionEdit($aid) {
		$this->template->assignment = AssignmentModel::getAssignment($aid);
		$this->aid = $aid;
	}

	/**
	 * Assignment editting - adding questions
	 * @param int $aid Assignment ID
	 */
	public function renderEdit($aid) {
		$this->checkTeacherAuthority();

		$this->template->questions = AssignmentModel::getQuestions($aid);
		if ($this->isAjax()) {
			$this->invalidateControl('virtualFormSnippet');
			$this->invalidateControl('virtualFormSnippet');
		}
	}

	/**
	 * Correcting assignment
	 * @param int $aid Assignment ID
	 */
	public function actionCorrect($aid) {
		$this->checkTeacherAuthority();
		$this->aid = $aid;

		$assignment = AssignmentModel::getAssignment($aid);
		$this->template->assignment = $assignment;
		$this->template->questions = AssignmentModel::getQuestions($aid);
		$this->template->submissions = AssignmentModel::getSubmissions($aid);
	}

	/**
	 * Download file uploaded to assignment
	 * @param int $afid Assignment file id
	 */
	public function actionDownloadFile($afid) {
		// check if resource id corresponds to course id
		$file = AssignmentModel::getAnwserFile($afid);
		if ($file['Course_id'] != $this->cid)
			throw new BadRequestException;
		$ext = pathinfo($file->filename, PATHINFO_EXTENSION);
		$name = $file->firstname . $file->lastname . '_' . $file->label . '.' . $ext;
		$this->sendResponse(new DownloadResponse(WWW_DIR . '/../uploads/' . $file->filename, $name));
	}

	/*
	 * =============================================================
	 * ==================  Signal Handlers =========================
	 */

	/**
	 * Removes question from assignment
	 * @param int $qid Question ID
	 */
	public function handleRemove($qid) {
		$this->checkTeacherAuthority();
		// check if question id corresponds to course id
		if (AssignmentModel::getCourseIDByQuestionID($qid) != $this->cid) {
			throw new BadRequestException;
		}
		AssignmentModel::removeQuestion($qid);
	}

	public function handleDeleteAssignment($aid) {
		$this->checkTeacherAuthority();
		if (AssignmentModel::deleteAssignment($aid)) {
			$this->flashMessage(_('Assignment Deleted'), 'success');
			$this->redirect('assignment:homepage', $this->cid);
		}
		else
			$this->flashMessage(_('There was an error deleting the assignment'), 'error');
	}

	/*
	 * =============================================================
	 * ==================  Form factories  =========================
	 */

	/**
	 * Correcting form - teacher awarding points
	 * @return AppForm
	 */
	protected function createComponentCorrectForm() {
		$form = new AppForm;
		$form->setTranslator($this->translator);
		foreach ($this->template->submissions as $submission) {
			$uid = $submission['user']->id;
			$form->addText($uid)
					->addRule(Form::INTEGER, 'Point value must be a number')
					->setDefaultValue($submission['points']);
			if ($this->template->assignment->maxpoints > 0)
				$form[$uid]->addRule(Form::RANGE, 'Points must be between 0 and max. points', array(0, $this->template->assignment->maxpoints));
		}
		$form->addSubmit('submit', 'Save');
		$form->onSubmit[] = callback($this, 'submitCorrectForm');

		return $form;
	}

	/**
	 * Correcting form handler
	 * @param AppForm $form
	 */
	public function submitCorrectForm(AppForm $form) {
		$values = $form->getValues();
		if (AssignmentModel::saveResult($this->aid, $values)) {
			$this->flashMessage('Correction successfully saved.', $type = 'success');
			$this->redirect('show', $this->aid);
		}
		else
			$this->flashMessage('There was an error saving the correction.', $type = 'error');
	}

	/**
	 * Solving form - for students
	 * @return AppForm
	 */
	protected function createComponentSolveForm() {
		$form = new AppForm;
		$form->setTranslator($this->translator);
		foreach (AssignmentModel::getQuestions($this->aid) as $value) {
			$label = $value->label;
			if ($value->type == 'text')
				$form->addText($value->id, $label);
			if ($value->type == 'textarea')
				$form->addTextArea($value->id, $label);
			if ($value->type == 'file')
				$form->addFile($value->id, $label);
			if ($value->type == 'radio')
				$form->addRadioList($value->id, $label, AssignmentModel::parseChoices($value->choices));
			if ($value->type == 'multi')
				$form->addMultiSelect($value->id, $label, AssignmentModel::parseChoices($value->choices))
								->getControlPrototype()->class = "multi";
		}
		$form->addSubmit('submit', 'Submit');
		$form->onSubmit[] = callback($this, 'submitSubmission');

		return $form;
	}

	/**
	 * Solving form handler
	 * @param AppForm $form
	 */
	public function submitSubmission(AppForm $form) {
		$values = $form->getValues();

		$now = new DateTime;
		$assignment = AssignmentModel::getAssignment($this->aid);
		//accept two seconds after deadline
		if (AssignmentModel::canSolve($this->aid, 2))
			if ($assignment->autocorrect) {
				$result = AssignmentModel::getCorrected($values, $this->aid);
				if ($result >= 0) {
					$this->flashMessage('Submission submitted successfully. You have scored ' . $result . ' points !', $type = 'success');
					$this->redirect('result:homepage', $this->cid);
				}
				else
					$this->flashMessage('There was an error submitting your submission', $type = 'error');
			} else {
				if (AssignmentModel::submitSubmission($values, $this->aid)) {
					$this->flashMessage('Submission submitted successfully.', $type = 'success');
					$this->redirect('homepage');
				}
				else
					$this->flashMessage('There was an error submitting your submission', $type = 'error');
			}
		else
			$this->flashMessage('It is too late or too early to submit for this assignment', $type = 'error');
	}

	/**
	 * Adding new Assignment Form
	 * @return AppForm
	 */
	protected function createComponentAddAssignmentForm() {
		$form = new AppForm;
		$form->setTranslator($this->translator);
		$form->addText('name', 'Name*')
				->setRequired();
		$form->addTextArea('description', 'Description:');
		$form->addText('assigndate', 'Open Date:')->getControlPrototype()->class = "datetimepicker";
		$form->addText('duedate', 'Close Date:')->getControlPrototype()->class = "datetimepicker";
		$form->addText('maxpoints', 'Max. Points (0=no max points):')
				->setDefaultValue('0')
				->addRule(Form::INTEGER, 'Max. points must be a number');
		$form->addText('timelimit', 'Time limit in minutes (0=no limit):')
				->addRule(Form::INTEGER, 'Time limit must be a number')
				->setDefaultValue('0')
				->addRule(Form::INTEGER, 'Max. points must be a number');
		$form->addSubmit('submit', 'Add');
		$form->addHidden('autocorrect', $this->autocorrect);
		$form->onSubmit[] = callback($this, 'addAssignment');
		return $form;
	}

	/**
	 * Edit Assignment Info Form
	 * @return AppForm
	 */
	protected function createComponentEditInfoForm() {
		$form = new AppForm;
		$form->setTranslator($this->translator);
		$form->addText('name', 'Name*')
				->setRequired();
		$form->addTextArea('description', 'Description:');
		$form->addText('assigndate', 'Open Date:')->getControlPrototype()->class = "datetimepicker";
		$form->addText('duedate', 'Close Date:')->getControlPrototype()->class = "datetimepicker";
		$form->addText('maxpoints', 'Max. Points (0=no max points):')
				->setDefaultValue('0')
				->addRule(Form::INTEGER, 'Max. points must be a number');
		$form->addText('timelimit', 'Time limit in minutes (0=no limit):')
				->addRule(Form::INTEGER, 'Time limit must be a number')
				->setDefaultValue('0')
				->addRule(Form::INTEGER, 'Max. points must be a number');
		$form->addSubmit('submit', 'Save');
		$form->onSubmit[] = callback($this, 'editAssignmentInfo');
		return $form;
	}

	/**
	 * Edit assignment info form handler
	 * @param AppForm $form
	 */
	public function editAssignmentInfo(AppForm $form) {
		$values = $form->getValues();
		$res = AssignmentModel::editAssignmentInfo($values, $this->aid);
		if ($res) {
			$this->flashMessage('Assignment successfully edited.', $type = 'success');
			$this->redirect('show', $this->aid);
		}
		else
			$this->flashMessage('There was an error editting the Assignment.', $type = 'error');
	}

	/**
	 * Adding new assignment form handler
	 * @param AppForm $form
	 */
	public function addAssignment(AppForm $form) {
		$values = $form->getValues();
		$newaid = AssignmentModel::addAssignment($values, $this->cid);
		if ($newaid != -1) {
			$this->redirect('edit', $newaid);
		}
		else
			$this->flashMessage('There was an error adding the Assignment.', $type = 'error');
	}

	/**
	 * Virtual Form showing new assignment preview
	 * @return AppForm
	 */
	protected function createComponentVirtualForm() {
		$form = new AppForm;
		$form->setTranslator($this->translator);
		foreach ($this->template->questions as $value) {
			$label = Html::el()->setHtml(htmlspecialchars($value->label) .
					'<a class="ajax" href="' . $this->link('remove!', $value->id) . '"><span class="ui-icon ui-icon-trash"></span></a>');
			if ($value->type == 'text')
				$form->addText('input' . $value->id, $label)->setDefaultValue($value->rightanwser);
			if ($value->type == 'textarea')
				$form->addTextArea('input' . $value->id, $label);
			if ($value->type == 'file')
				$form->addFile('input' . $value->id, $label);
			if ($value->type == 'radio')
				$form->addRadioList('input' . $value->id, $label, AssignmentModel::parseChoices($value->choices))->setDefaultValue(AssignmentModel::getRadioAnwserPos($value, $value->rightanwser));
			if ($value->type == 'multi')
				$form->addMultiSelect('input' . $value->id, $label, AssignmentModel::parseChoices($value->choices))->setDefaultValue(AssignmentModel::getMultiAnwserArray($value, $value->rightanwser))
								->getControlPrototype()->class = "multi";
		}

		return $form;
	}

	/**
	 * Add text input to assignment form
	 * @return AppForm
	 */
	protected function createComponentAddTextForm() {
		$form = new AppForm;
		$form->setTranslator($this->translator);
		$form->getElementPrototype()->class[] = "ajax";
		$form->addGroup('Example');
		$form->addText('example', "My Label")->setDisabled();
		$form->addGroup('Set Label');
		$form->addTextArea('label');
		if ($this->template->assignment->autocorrect)
			$form->addText('ranwser', 'Right Anwser:*')
					->addRule(Form::FILLED, 'Right anwser must be filled');
		$form->addSubmit('add', 'Add');
		$form->onSubmit[] = callback($this, 'addText');
		return $form;
	}

	/**
	 * Add text input form handler
	 * @param AppForm $form
	 */
	public function addText(AppForm$form) {
		$values = $form->getValues();
		if (!isset($values['ranwser']))
			$values['ranwser'] = null;
		AssignmentModel::addText($values['label'], $this->aid, $values['ranwser']);
	}

	/**
	 * Add file upload to assignment
	 * @return AppForm
	 */
	protected function createComponentAddFileForm() {
		$form = new AppForm;
		$form->setTranslator($this->translator);
		$form->getElementPrototype()->class[] = "ajax";
		$form->addGroup('Example');
		$form->addFile('example', 'My Label:');
		$form->addGroup('Set Label');
		$form->addTextArea('label');
		$form->addSubmit('add', 'Add');
		$form->onSubmit[] = callback($this, 'addFile');
		return $form;
	}

	/**
	 * Add file upload handler
	 * @param AppForm $form
	 */
	public function addFile(AppForm $form) {
		$values = $form->getValues();
		AssignmentModel::addFile($values['label'], $this->aid);
	}

	/**
	 * Add textarea to assignment
	 * @return AppForm
	 */
	protected function createComponentAddTextAreaForm() {
		$form = new AppForm;
		$form->setTranslator($this->translator);
		$form->getElementPrototype()->class[] = "ajax";
		$form->addGroup('Example');
		$form->addTextArea('example', "My Label")->setDisabled();
		$form->addGroup('Set Label');
		$form->addTextArea('label');
		$form->addSubmit('add', 'Add');
		$form->onSubmit[] = callback($this, 'addTextArea');
		return $form;
	}

	/**
	 * Add textarea handler
	 * @param AppForm $form
	 */
	public function addTextArea(AppForm $form) {
		$values = $form->getValues();
		AssignmentModel::addTextArea($values['label'], $this->aid);
	}

	/**
	 * Add radio to assignment form
	 * @return AppForm
	 */
	protected function createComponentAddRadioForm() {
		$form = new AppForm;
		$form->setTranslator($this->translator);
		$form->getElementPrototype()->class[] = "ajax";
		$form->addGroup('Example');
		$form->addRadioList('example', "My Label", array('option 1', 'option 2', 'option 3'))->setDisabled();
		$form->addGroup('Set Label and choices');
		$form->addTextArea('label');
		$form->addText('val1');
		$form->addText('val2');
		$form->addText('val3');
		$form->addText('val4');
		$form->addText('val5');
		$form->addText('val6');
		$form->addText('val7');
		$form->addText('val8');
		if ($this->template->assignment->autocorrect)
			$form->addText('ranwser', 'Right Anwser:*')
					->setOption('description', _('Use ; as delimiter'))
					->addRule(Form::FILLED, 'Right anwser must be filled');
		$form->addSubmit('add', 'Add');
		$form->onSubmit[] = callback($this, 'addRadio');
		return $form;
	}

	/**
	 * Add RadioButtons to assignment form handler
	 * @param AppForm $form
	 */
	public function addRadio(AppForm $form) {
		$values = $form->getValues();
		$choices = array(
			$values['val1'],
			$values['val2'],
			$values['val3'],
			$values['val4'],
			$values['val5'],
			$values['val6'],
			$values['val7'],
			$values['val8']
		);
		if (!isset($values['ranwser']))
			$values['ranwser'] = null;
		AssignmentModel::addRadio($values['label'], $choices, $this->aid, $values['ranwser']);
	}

	/**
	 * Add multiselect to assignment form
	 * @return AppForm
	 */
	protected function createComponentAddMultiSelectForm() {
		$form = new AppForm;
		$form->setTranslator($this->translator);
		$form->getElementPrototype()->class[] = "ajax";
		$form->addGroup('Example');
		$form->addMultiSelect('example', "My Label", array('option 1', 'option 2', 'option 3'))->setDisabled()->setDefaultValue(array(1, 2))
						->getControlPrototype()->class = "multi";
		$form->addGroup('Set Label and choices');
		$form->addTextArea('label');
		$form->addText('val1');
		$form->addText('val2');
		$form->addText('val3');
		$form->addText('val4');
		$form->addText('val5');
		$form->addText('val6');
		$form->addText('val7');
		$form->addText('val8');
		if ($this->template->assignment->autocorrect)
			$form->addText('ranwser', 'Right Anwsers delim. by ";" :*')
					->addRule(Form::FILLED, 'Right anwser must be filled');
		$form->addSubmit('add', 'Add');
		$form->onSubmit[] = callback($this, 'addMultiSelect');
		return $form;
	}

	/**
	 * Add multiselect form handler
	 * @param AppForm $form
	 */
	public function addMultiSelect(AppForm $form) {
		$values = $form->getValues();
		$choices = array(
			$values['val1'],
			$values['val2'],
			$values['val3'],
			$values['val4'],
			$values['val5'],
			$values['val6'],
			$values['val7'],
			$values['val8']
		);
		if (!isset($values['ranwser']))
			$values['ranwser'] = null;
		AssignmentModel::addMultiSelect($values['label'], $choices, $this->aid, str_replace(';', '#', $values['ranwser']));
	}

}