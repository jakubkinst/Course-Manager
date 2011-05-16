<?php //netteCache[01]000344a:2:{s:4:"time";s:21:"0.00505100 1305537347";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:5:"Cache";i:1;s:9:"checkFile";}i:1;s:58:"C:\xampp\htdocs\Course-Manager\app\templates\@layout.phtml";i:2;i:1305537340;}i:1;a:3:{i:0;a:2:{i:0;s:5:"Cache";i:1;s:10:"checkConst";}i:1;s:19:"Framework::REVISION";i:2;s:30:"7616569 released on 2011-03-10";}}}?><?php

// source file: C:\xampp\htdocs\Course-Manager\app\templates\@layout.phtml

?><?php
$_l = LatteMacros::initRuntime($template, NULL, 'ak302hhhyc'); unset($_extends);


//
// block head
//
if (!function_exists($_l->blocks['head'][] = '_lbb4d517a2df_head')) { function _lbb4d517a2df_head($_l, $_args) { extract($_args)
;
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
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <meta name="description" content="Nette Framework web application skeleton" /><?php if (isset($robots)): ?>
        <meta name="robots" content="<?php echo TemplateHelpers::escapeHtml($robots) ?>" />
<?php endif ?>

        <title>Course Manager</title>

        <!-- import BluePrint CSS framework -->
        <link rel="stylesheet" media="screen,projection,tv" href="<?php echo TemplateHelpers::escapeHtml($basePath) ?>/css/blueprint/screen.css" type="text/css" />
        <link rel="stylesheet" media="print" href="<?php echo TemplateHelpers::escapeHtml($basePath) ?>/css/blueprint/print.css" type="text/css" />
        <!--[if IE]><link rel="stylesheet" href="<?php echo TemplateHelpers::escapeHtmlComment($basePath) ?>/css/blueprint/ie.css" type="text/css" media="screen, projection"><![endif]-->
        <!-- import custom CSS styles -->
        <link rel="stylesheet" media="screen,projection,tv" href="<?php echo TemplateHelpers::escapeHtml($basePath) ?>/css/screen.css" type="text/css" />
        <link rel="stylesheet" media="print" href="<?php echo TemplateHelpers::escapeHtml($basePath) ?>/css/print.css" type="text/css" />


        <link rel="shortcut icon" href="<?php echo TemplateHelpers::escapeHtml($basePath) ?>/favicon.ico" type="image/x-icon" />

        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo TemplateHelpers::escapeHtml($basePath) ?>/js/netteForms.js"></script>
	<?php if (!$_l->extends) { call_user_func(reset($_l->blocks['head']), $_l, get_defined_vars()); } ?>

    </head>

    <body><?php foreach ($iterator = $_l->its[] = new SmartCachingIterator($flashes) as $flash): ?>
        <div class="flash <?php echo TemplateHelpers::escapeHtml($flash->type) ?>"><?php echo TemplateHelpers::escapeHtml($flash->message) ?></div>
<?php endforeach; array_pop($_l->its); $iterator = end($_l->its) ?>
        <div class="container">
            <div id="header" class="span-24 last shadow-pull">
                <h1>Course Manager</h1>
                <div id="loginpart">
<?php if ($logged): ?>
                    Logged as <?php echo TemplateHelpers::escapeHtml($user->email) ?> <br />
                    <a href="<?php echo TemplateHelpers::escapeHtml($control->link("user:logout")) ?>">logout</a>
<?php else: ?>
                    <?php $_ctrl = $control->getWidget("signInForm"); if ($_ctrl instanceof IPartiallyRenderable) $_ctrl->validateControl(); $_ctrl->render() ?> <br />
                    <a href="<?php echo TemplateHelpers::escapeHtml($control->link("user:register")) ?>">register</a>
<?php endif ?>

                </div>
            </div>
            <div class="span-19 colborder" id="content">
<?php LatteMacros::callBlock($_l, 'content', $template->getParams()) ?>
            </div>

            <div class="span-4 last" id="sidebar">
<?php if ($logged): ?>
                    <div id="courselist">
                        <h3 class="caps">My courses</h3>
<?php foreach ($iterator = $_l->its[] = new SmartCachingIterator($courses) as $course): ?>
                            <div class="div-course shadow-rb">
                                <a href=""><?php echo TemplateHelpers::escapeHtml($course['name']) ?></a>
                                <div class="italic"><?php echo TemplateHelpers::escapeHtml($course['description']) ?></div>
                                <ul>
                                    <li><a href="">Classes</a></li>
                                    <li><a href="">Statistics</a></li>
                                    <li><a href="">Assignments</a></li>
                                    <li><a href="">Calendar</a></li>
                                </ul>
                            </div>
<?php endforeach; array_pop($_l->its); $iterator = end($_l->its) ?>
                    </div>
<?php endif ?>
            </div>
        </div>
    </body>
</html>
<?php
if ($_l->extends) {
	ob_end_clean();
	LatteMacros::includeTemplate($_l->extends, get_defined_vars(), $template)->render();
}
