<?php //netteCache[01]000352a:2:{s:4:"time";s:21:"0.07256300 1307708875";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:5:"Cache";i:1;s:9:"checkFile";}i:1;s:66:"C:\xampp\htdocs\Course-Manager\app\templates\Lesson\homepage.latte";i:2;i:1307708872;}i:1;a:3:{i:0;a:2:{i:0;s:5:"Cache";i:1;s:10:"checkConst";}i:1;s:19:"Framework::REVISION";i:2;s:30:"7616569 released on 2011-03-10";}}}?><?php

// source file: C:\xampp\htdocs\Course-Manager\app\templates\Lesson\homepage.latte

?><?php
$_l = LatteMacros::initRuntime($template, NULL, 'm8qp4ewc9r'); unset($_extends);


//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lb2cfbe18fc0_content')) { function _lb2cfbe18fc0_content($_l, $_args) { extract($_args)
;if ($approved): ?>
<h2><?php echo TemplateHelpers::escapeHtml($lesson['topic']) ?></h2>
<div><?php echo TemplateHelpers::escapeHtml($lesson['date']) ?></div>
<p><?php echo TemplateHelpers::escapeHtml($lesson['description']) ?></p>
<div id="comments">
    <h4>Comments</h4>
<?php foreach ($iterator = $_l->its[] = new SmartCachingIterator($comments) as $comment): ?>
    <div class="comment">
        <div class="author">
            <?php echo TemplateHelpers::escapeHtml($comment['user']['email']) ?> (<?php echo TemplateHelpers::escapeHtml($comment['added']) ?>)
        </div>
        <div class="content">
            <?php echo TemplateHelpers::escapeHtml($comment['content']) ?>

        </div>
    </div>
        
<?php endforeach; array_pop($_l->its); $iterator = end($_l->its) ;$_ctrl = $control->getWidget("commentForm"); if ($_ctrl instanceof IPartiallyRenderable) $_ctrl->validateControl(); $_ctrl->render() ?>
</div>
<?php else: ?>
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
?>

<?php if (!$_l->extends) { call_user_func(reset($_l->blocks['content']), $_l, get_defined_vars()); }  
if ($_l->extends) {
	ob_end_clean();
	LatteMacros::includeTemplate($_l->extends, get_defined_vars(), $template)->render();
}