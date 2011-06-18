<?php //netteCache[01]000356a:2:{s:4:"time";s:21:"0.79788000 1308390648";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:5:"Cache";i:1;s:9:"checkFile";}i:1;s:70:"C:\xampp\htdocs\Course-Manager\app\templates\CourseList\homepage.latte";i:2;i:1308390612;}i:1;a:3:{i:0;a:2:{i:0;s:5:"Cache";i:1;s:10:"checkConst";}i:1;s:19:"Framework::REVISION";i:2;s:30:"7616569 released on 2011-03-10";}}}?><?php

// source file: C:\xampp\htdocs\Course-Manager\app\templates\CourseList\homepage.latte

?><?php
$_l = LatteMacros::initRuntime($template, NULL, 'v8ohsi79bp'); unset($_extends);


//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lbd748a6a823_content')) { function _lbd748a6a823_content($_l, $_args) { extract($_args)
?>
    <div id="courseList_homepage">
<?php if ($logged): ?>
        <h3>Teachered Courses</h3>
<?php foreach ($iterator = $_l->its[] = new SmartCachingIterator($tCourses) as $course): ?>
                <a href="<?php echo TemplateHelpers::escapeHtml($control->link("course:homepage", array($course['id']))) ?>">
                    <div class="course asTeacher">
                        <span class="title"><?php echo TemplateHelpers::escapeHtml($course['name']) ?></span>
                        <p class="description"><?php echo TemplateHelpers::escapeHtml($course['description']) ?></span>
                    </div>
                </a>
<?php endforeach; array_pop($_l->its); $iterator = end($_l->its) ?>
        <div class="buttonLine">
            <a href="<?php echo TemplateHelpers::escapeHtml($control->link("course:add")) ?>">+ Add Course</a>
        </div>
        <h3>Student Courses</h3>
<?php foreach ($iterator = $_l->its[] = new SmartCachingIterator($sCourses) as $course): ?>
                <a href="<?php echo TemplateHelpers::escapeHtml($control->link("course:homepage", array($course['id']))) ?>">
                    <div class="course asStudent">
                        <span class="title"><?php echo TemplateHelpers::escapeHtml($course['name']) ?></span>
                        <p class="description"><?php echo TemplateHelpers::escapeHtml($course['description']) ?></span>
                    </div>
                </a>
<?php endforeach; array_pop($_l->its); $iterator = end($_l->its) ;else: ?>
        <p id="welcome">
            Welcome to Course Manager. Please sign in.<br />
            You can register <a href="<?php echo TemplateHelpers::escapeHtml($control->link("user:register")) ?>">here</a>
        </p>
<?php endif ?>
    </div>
<?php
}}

//
// end of blocks
//

if ($_l->extends) {
	ob_start();
} elseif (isset($presenter, $control) && $presenter->isAjax() && $control->isControlInvalid()) {
	return LatteMacros::renderSnippets($control, $_l, get_defined_vars());
}
if (!$_l->extends) { call_user_func(reset($_l->blocks['content']), $_l, get_defined_vars()); }  
if ($_l->extends) {
	ob_end_clean();
	LatteMacros::includeTemplate($_l->extends, get_defined_vars(), $template)->render();
}
