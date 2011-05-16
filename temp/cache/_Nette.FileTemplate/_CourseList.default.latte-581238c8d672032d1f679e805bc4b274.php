<?php //netteCache[01]000355a:2:{s:4:"time";s:21:"0.14580500 1305531221";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:5:"Cache";i:1;s:9:"checkFile";}i:1;s:69:"C:\xampp\htdocs\Course-Manager\app\templates\CourseList\default.latte";i:2;i:1305531216;}i:1;a:3:{i:0;a:2:{i:0;s:5:"Cache";i:1;s:10:"checkConst";}i:1;s:19:"Framework::REVISION";i:2;s:30:"7616569 released on 2011-03-10";}}}?><?php

// source file: C:\xampp\htdocs\Course-Manager\app\templates\CourseList\default.latte

?><?php
$_l = LatteMacros::initRuntime($template, NULL, 'mmiq2lvprj'); unset($_extends);


//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lbd69ee60512_content')) { function _lbd69ee60512_content($_l, $_args) { extract($_args)
;foreach ($iterator = $_l->its[] = new SmartCachingIterator($courses) as $course): ?>
    <div class="course">
        <h3><?php echo TemplateHelpers::escapeHtml($course['name']) ?></h3>
        <?php echo TemplateHelpers::escapeHtml($course['description']) ?>

    </div>
<?php endforeach; array_pop($_l->its); $iterator = end($_l->its) ;
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
