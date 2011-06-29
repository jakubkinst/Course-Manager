<?php //netteCache[01]000350a:2:{s:4:"time";s:21:"0.79896300 1309258479";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:5:"Cache";i:1;s:9:"checkFile";}i:1;s:64:"C:\xampp\htdocs\Course-Manager\app\templates\User\register.latte";i:2;i:1309258339;}i:1;a:3:{i:0;a:2:{i:0;s:5:"Cache";i:1;s:10:"checkConst";}i:1;s:19:"Framework::REVISION";i:2;s:30:"7616569 released on 2011-03-10";}}}?><?php

// source file: C:\xampp\htdocs\Course-Manager\app\templates\User\register.latte

?><?php
$_l = LatteMacros::initRuntime($template, NULL, 'ahfln4b3xr'); unset($_extends);


//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lb47a2ae41b7_content')) { function _lb47a2ae41b7_content($_l, $_args) { extract($_args)
;$_ctrl = $control->getWidget("registerForm"); if ($_ctrl instanceof IPartiallyRenderable) $_ctrl->validateControl(); $_ctrl->render() ?>

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
?>

<?php if (!$_l->extends) { call_user_func(reset($_l->blocks['content']), $_l, get_defined_vars()); }  
if ($_l->extends) {
	ob_end_clean();
	LatteMacros::includeTemplate($_l->extends, get_defined_vars(), $template)->render();
}
