<?php //netteCache[01]000353a:2:{s:4:"time";s:21:"0.61547100 1307692644";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:5:"Cache";i:1;s:9:"checkFile";}i:1;s:67:"C:\xampp\htdocs\Course-Manager\app\templates\Course\addLesson.latte";i:2;i:1307692540;}i:1;a:3:{i:0;a:2:{i:0;s:5:"Cache";i:1;s:10:"checkConst";}i:1;s:19:"Framework::REVISION";i:2;s:30:"7616569 released on 2011-03-10";}}}?><?php

// source file: C:\xampp\htdocs\Course-Manager\app\templates\Course\addLesson.latte

?><?php
$_l = LatteMacros::initRuntime($template, NULL, '5hsap6oeru'); unset($_extends);


//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lb3b395c6f55_content')) { function _lb3b395c6f55_content($_l, $_args) { extract($_args)
?>
<h3>Add Lesson</h3>
    <?php $_ctrl = $control->getWidget("addLessonForm"); if ($_ctrl instanceof IPartiallyRenderable) $_ctrl->validateControl(); $_ctrl->render() ;
}}

//
// end of blocks
//

if ($_l->extends) {
	ob_start();
} elseif (isset($presenter, $control) && $presenter->isAjax() && $control->isControlInvalid()) {
	return LatteMacros::renderSnippets($control, $_l, get_defined_vars());
}
?>

<?php if (!$_l->extends) { call_user_func(reset($_l->blocks['content']), $_l, get_defined_vars()); }  
if ($_l->extends) {
	ob_end_clean();
	LatteMacros::includeTemplate($_l->extends, get_defined_vars(), $template)->render();
}
