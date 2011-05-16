<?php //netteCache[01]000344a:2:{s:4:"time";s:21:"0.91030700 1305482383";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:5:"Cache";i:1;s:9:"checkFile";}i:1;s:58:"C:\xampp\htdocs\Course-Manager\app\templates\Sign\in.latte";i:2;i:1299799366;}i:1;a:3:{i:0;a:2:{i:0;s:5:"Cache";i:1;s:10:"checkConst";}i:1;s:19:"Framework::REVISION";i:2;s:30:"7616569 released on 2011-03-10";}}}?><?php

// source file: C:\xampp\htdocs\Course-Manager\app\templates\Sign\in.latte

?><?php
$_l = LatteMacros::initRuntime($template, NULL, 'akdrb26i2s'); unset($_extends);


//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lb604dfbd9fb_content')) { function _lb604dfbd9fb_content($_l, $_args) { extract($_args)
?>

<h1>Sign in</h1>

<?php $_ctrl = $control->getWidget("signInForm"); if ($_ctrl instanceof IPartiallyRenderable) $_ctrl->validateControl(); $_ctrl->render() ;
}}

//
// end of blocks
//

if ($_l->extends) {
	ob_start();
} elseif (isset($presenter, $control) && $presenter->isAjax() && $control->isControlInvalid()) {
	return LatteMacros::renderSnippets($control, $_l, get_defined_vars());
}
$robots = 'noindex' ?>

<?php if (!$_l->extends) { call_user_func(reset($_l->blocks['content']), $_l, get_defined_vars()); }  
if ($_l->extends) {
	ob_end_clean();
	LatteMacros::includeTemplate($_l->extends, get_defined_vars(), $template)->render();
}
