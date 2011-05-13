<?php //netteCache[01]000340a:2:{s:4:"time";s:21:"0.40125500 1302079015";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:5:"Cache";i:1;s:9:"checkFile";}i:1;s:54:"C:\xampp\htdocs\rp\app\templates\Course\homepage.phtml";i:2;i:1302079012;}i:1;a:3:{i:0;a:2:{i:0;s:5:"Cache";i:1;s:10:"checkConst";}i:1;s:19:"Framework::REVISION";i:2;s:30:"7616569 released on 2011-03-10";}}}?><?php

// source file: C:\xampp\htdocs\rp\app\templates\Course\homepage.phtml

?><?php
$_l = LatteMacros::initRuntime($template, NULL, 'hi7h39u4v2'); unset($_extends);


//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lb16a66357c0_content')) { function _lb16a66357c0_content($_l, $_args) { extract($_args)
?>

<div class="span-19">
    <h3 class="flaground shadow-rb"><?php echo TemplateHelpers::escapeHtml($activeCourse['label']) ?></h3>
</div>
<?php for ($i=1;$i<=6;$i++): ?>
<!-- Lesson -->
<div class="span-19">
    <div class="lesson flaground shadow-rb">
            <h4>Lesson <?php echo TemplateHelpers::escapeHtml($i) ?></h4>
            <div class="date">12.3.2011</div>
            <div class="summary">Blablabla</div>
            <div class="materials">Materials</div>
    </div>
</div>
<?php endfor ?>

<?php
}}


//
// block head
//
if (!function_exists($_l->blocks['head'][] = '_lb3e07560a2f_head')) { function _lb3e07560a2f_head($_l, $_args) { extract($_args)
?>

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
if (!$_l->extends) { call_user_func(reset($_l->blocks['content']), $_l, get_defined_vars()); } ?>


<?php if (!$_l->extends) { call_user_func(reset($_l->blocks['head']), $_l, get_defined_vars()); }  
if ($_l->extends) {
	ob_end_clean();
	LatteMacros::includeTemplate($_l->extends, get_defined_vars(), $template)->render();
}
