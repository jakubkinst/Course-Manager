<?php //netteCache[01]000352a:2:{s:4:"time";s:21:"0.27812900 1308392330";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:5:"Cache";i:1;s:9:"checkFile";}i:1;s:66:"C:\xampp\htdocs\Course-Manager\app\templates\Lesson\homepage.latte";i:2;i:1308392300;}i:1;a:3:{i:0;a:2:{i:0;s:5:"Cache";i:1;s:10:"checkConst";}i:1;s:19:"Framework::REVISION";i:2;s:30:"7616569 released on 2011-03-10";}}}?><?php

// source file: C:\xampp\htdocs\Course-Manager\app\templates\Lesson\homepage.latte

?><?php
$_l = LatteMacros::initRuntime($template, NULL, '244o33ok6o'); unset($_extends);


//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lbae41ddec96_content')) { function _lbae41ddec96_content($_l, $_args) { extract($_args)
?>
    <div id="lesson_homepage">
<?php if ($isTeacher || $isStudent): ?>
            <h3><?php echo TemplateHelpers::escapeHtml($lesson['topic']) ?></h3>
            <div id="date"><?php echo TemplateHelpers::escapeHtml($lesson['date']) ?></div>
            <p id="description"><?php echo TemplateHelpers::escapeHtml($lesson['description']) ?></p>
            <div id="comments">
                <h4>Comments</h4>
<?php foreach ($iterator = $_l->its[] = new SmartCachingIterator($comments) as $comment): ?>
                    <div class="comment">
                        <div class="author"><?php echo TemplateHelpers::escapeHtml($comment['user']['email']) ?></div>
                        <div class="time">(<?php echo TemplateHelpers::escapeHtml($comment['added']) ?>)</div>                        
                        <div class="content"><?php echo TemplateHelpers::escapeHtml($comment['content']) ?></div>
                    </div>
<?php endforeach; array_pop($_l->its); $iterator = end($_l->its) ?>
            </div>
            <div id="commentForm">
<?php $_ctrl = $control->getWidget("commentForm"); if ($_ctrl instanceof IPartiallyRenderable) $_ctrl->validateControl(); $_ctrl->render() ?>
            </div>
<?php else: ?>
            <div class="error">
                Unauthorized access !
            </div>
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
?>

<?php if (!$_l->extends) { call_user_func(reset($_l->blocks['content']), $_l, get_defined_vars()); }  
if ($_l->extends) {
	ob_end_clean();
	LatteMacros::includeTemplate($_l->extends, get_defined_vars(), $template)->render();
}
