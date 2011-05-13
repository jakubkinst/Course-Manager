<?php

/**
 * My Application
 *
 * @copyright  Copyright (c) 2010 John Doe
 * @package    MyApplication
 */



/**
 * Course presenter.
 *
 */
class CoursePresenter extends BasePresenter
{

	public function renderHomepage()
	{            
            $courses = $this->getCourses();
            $this->template->activeCourse = $courses[$this->getParam('id',0)];
            $this->template->courses = $courses;
	}
        private function getCourses(){
           $courses = array(
              array('id'=>0,'label'=>'Linearni algebra II','lector'=>'Jiri Fiala','time'=>'PO 14:00'),
               array('id'=>1,'label'=>'Matematicka analyza I','lector'=>'Jan Rataj','time'=>'UT 09:00'),
               array('id'=>2,'label'=>'Uvod do UNIXU','lector'=>'Libor Forst','time'=>'PA 19:20'),
               array('id'=>3,'label'=>'Vyrokova a predikatova logika','lector'=>'Josef Mlcek','time'=>'ST 11:00')
           );
            return $courses;
        }


}
