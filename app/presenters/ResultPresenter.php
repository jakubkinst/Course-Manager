<?php

/**
 * Presenter dedicated to Result module
 *
 * @author     Jakub Kinst <jakub@kinst.cz> (@link http://jakub.kinst.cz)
 * @package    Course-Manager/Presenters
 */
class ResultPresenter extends BaseCoursePresenter {
	/*
	 * =============================================================
	 * =======================  Actions ============================
	 */

	/**
	 * Result homepage - show results
	 * @param int $cid Course ID
	 */
	public function renderHomepage($cid) {
		$this->template->offlinePoints = ResultModel::getOfflinePointAssignmentsResults($cid);
		$this->template->onlinePoints = ResultModel::getOnlinePointAssignmentsResults($cid);
		$this->template->offlineGrades = ResultModel::getOfflineGradeAssignmentsResults($cid);
		$this->template->offlineSums = ResultModel::getOfflinePointsSums($cid);
		$this->template->onlineSums = ResultModel::getOnlinePointsSums($cid);
		$this->template->avgs = ResultModel::getOfflineGradesAvgs($cid);
	}

	/**
	 * Add result
	 * @param int $cid Course ID
	 */
	public function renderAdd($cid) {
		$this->checkTeacherAuthority();
	}

	/*
	 * =============================================================
	 * ==================  Form factories  =========================
	 */

	/**
	 * Add result form
	 * @return AppForm
	 */
	protected function createComponentAddResultForm() {

		function numberValidator($val) {
			if (is_numeric($val->value) || trim(strval($val->value)) == '')
				return true; else
				return false;
		}

		$form = new AppForm;
		$form->setTranslator($this->translator);
		$form->addText('name', 'Offline assignment name:*')
				->addRule(Form::FILLED, 'Set assignment name.');
		$form->addText('maxpoints', 'Max points:')
				->addRule('numberValidator', 'Max points/grade must be a number');

		$grade = array(
			0 => 'Points',
			1 => 'Grade',
		);
		$form->addRadioList('grade', 'Points/Grade:', $grade)
				->setDefaultValue(0);
		$students = CourseModel::getStudents($this->cid);
		foreach ($students as $student) {
			$form->addText(strval($student->id), $student->firstname . ' ' . $student->lastname . ' :')
					->addRule('numberValidator', 'Points/grade must be a number');
		}

		$form->addSubmit('send', 'Add Assignment');
		$form->onSubmit[] = callback($this, 'addResultFormSubmitted');

		return $form;
	}

	/**
	 * Add result form handler
	 * @param AppForm $form
	 */
	public function addResultFormSubmitted(AppForm $form) {
		$values = $form->getValues();
		ResultModel::addResult($this->cid, $values);
		$this->flashMessage('Results added.', $type = 'success');
		$this->redirect('result:homepage', $this->cid);
	}

}