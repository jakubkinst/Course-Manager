<?php

/**
 * ResultPresenter
 *
 * @author Jakub Kinst
 */
class ResultPresenter extends BasePresenter {


    /**
     * Homepage template render
     * @param type $cid 
     */
    public function renderHomepage($cid) {
        $this->template->offlinePoints = ResultModel::getOfflinePointAssignmentsResults($cid);
        $this->template->offlineGrades = ResultModel::getOfflineGradeAssignmentsResults($cid);
        $this->template->sums = ResultModel::getOfflinePointsSums($cid);
        $this->template->avgs = ResultModel::getOfflineGradesAvgs($cid);
    }

    /**
     * Add result template render
     * @param type $cid 
     */
    public function renderAdd($cid) {
        $this->checkTeacherAuthority();
    }

    /**
     * Form factory - Add result
     * @return AppForm 
     */
    protected function createComponentAddResultForm() {

        function numberValidator($val) {
            if (is_numeric($val->value) || trim(strval($val->value)) == '')
                return true; else
                return false;
        }

        $form = new AppForm;
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
     * @param type $form 
     */
    public function addResultFormSubmitted($form) {
        $values = $form->getValues();
        ResultModel::addResult($this->cid, $values);
        $this->flashMessage('Results added.', $type = 'success');
        $this->redirect('result:homepage');
    }

}