<?php //netteCache[01]000356a:2:{s:4:"time";s:21:"0.52466900 1305563654";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:5:"Cache";i:1;s:9:"checkFile";}i:1;s:70:"C:\xampp\htdocs\Course-Manager\app\templates\CourseList\homepage.latte";i:2;i:1305563615;}i:1;a:3:{i:0;a:2:{i:0;s:5:"Cache";i:1;s:10:"checkConst";}i:1;s:19:"Framework::REVISION";i:2;s:30:"7616569 released on 2011-03-10";}}}?><?php

// source file: C:\xampp\htdocs\Course-Manager\app\templates\CourseList\homepage.latte

?><?php
$_l = LatteMacros::initRuntime($template, NULL, 'b31wcgua25'); unset($_extends);


//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lbf030d6888b_content')) { function _lbf030d6888b_content($_l, $_args) { extract($_args)
;if ($logged): foreach ($iterator = $_l->its[] = new SmartCachingIterator($courses) as $course): ?>
        <div class="course">
            <h3><a href="<?php echo TemplateHelpers::escapeHtml($control->link("course:homepage", array('id' => $course['id']))) ?>"><?php echo TemplateHelpers::escapeHtml($course['name']) ?></a></h3>
            <?php echo TemplateHelpers::escapeHtml($course['description']) ?>

        </div>
<?php endforeach; array_pop($_l->its); $iterator = end($_l->its) ?>
    <a href="<?php echo TemplateHelpers::escapeHtml($control->link("course:add")) ?>">+ Add Course</a>
<?php else: ?>
    <h2></h2>
    <p>
        Welcome to Course Manager. Please sign in.<br />
        You can register <a href="<?php echo TemplateHelpers::escapeHtml($control->link("user:register")) ?>">here</a>
    </p>
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
?>

<?php if (!$_l->extends) { call_user_func(reset($_l->blocks['content']), $_l, get_defined_vars()); }  
if ($_l->extends) {
	ob_end_clean();
	LatteMacros::includeTemplate($_l->extends, get_defined_vars(), $template)->render();
}
