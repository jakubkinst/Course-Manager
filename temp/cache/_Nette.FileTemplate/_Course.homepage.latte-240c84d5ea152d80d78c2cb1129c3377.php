<?php //netteCache[01]000352a:2:{s:4:"time";s:21:"0.66481200 1308076343";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:5:"Cache";i:1;s:9:"checkFile";}i:1;s:66:"C:\xampp\htdocs\Course-Manager\app\templates\Course\homepage.latte";i:2;i:1308076339;}i:1;a:3:{i:0;a:2:{i:0;s:5:"Cache";i:1;s:10:"checkConst";}i:1;s:19:"Framework::REVISION";i:2;s:30:"7616569 released on 2011-03-10";}}}?><?php

// source file: C:\xampp\htdocs\Course-Manager\app\templates\Course\homepage.latte

?><?php
$_l = LatteMacros::initRuntime($template, NULL, 'yyzu32hyoe'); unset($_extends);


//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lbff23aa6d0a_content')) { function _lbff23aa6d0a_content($_l, $_args) { extract($_args)
;if ($isTeacher || $isStudent): ?>
    <div class="span-19">
        <h3 class="flaground shadow-rb"><?php echo TemplateHelpers::escapeHtml($course['name']) ?></h3>
    </div>
<?php if ($isTeacher): ?>
    <a href="<?php echo TemplateHelpers::escapeHtml($control->link("course:addLesson")) ?>">Add lesson</a>
<?php endif ?>
       
<?php foreach ($iterator = $_l->its[] = new SmartCachingIterator($lessons) as $lesson): ?>
        <!-- Lesson -->
        <div class="span-19">
            <div class="lesson flaground shadow-rb">
                <a href="<?php echo TemplateHelpers::escapeHtml($control->link("lesson:homepage", array($lesson['id']))) ?>"><h4><?php echo TemplateHelpers::escapeHtml($lesson['topic']) ?></h4></a>
                <div class="date"><?php echo TemplateHelpers::escapeHtml($lesson['date']) ?></div>
                <div class="summary"><?php echo TemplateHelpers::escapeHtml($lesson['description']) ?></div>
            </div>
        </div>
<?php endforeach; array_pop($_l->its); $iterator = end($_l->its) ;if ($isTeacher): ?>
    <div id="inviteUser">
        <a href="<?php echo TemplateHelpers::escapeHtml($control->link("Course:inviteStudent")) ?>">Invite Student</a>
    </div>
<?php endif ;else: ?>
    <span class="error">
        Unauthorized access !
    </span>
<?php endif ;
}}

//
// end of blocks
//

if ($_l->extends) {
	ob_start();
} elseif (isset($presenter, $control) && $presenter->isAjax() && $control->isControlInvalid()) {
	return LatteMacros::renderSnippets($control, $_l, get_defined_vars());
}
if (!$_l->extends) { call_user_func(reset($_l->blocks['content']), $_l, get_defined_vars()); } ?>

<?php
if ($_l->extends) {
	ob_end_clean();
	LatteMacros::includeTemplate($_l->extends, get_defined_vars(), $template)->render();
}
