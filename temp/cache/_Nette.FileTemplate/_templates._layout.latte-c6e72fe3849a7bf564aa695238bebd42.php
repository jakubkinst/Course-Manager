<?php //netteCache[01]000344a:2:{s:4:"time";s:21:"0.04006100 1305565504";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:5:"Cache";i:1;s:9:"checkFile";}i:1;s:58:"C:\xampp\htdocs\Course-Manager\app\templates\@layout.latte";i:2;i:1305565497;}i:1;a:3:{i:0;a:2:{i:0;s:5:"Cache";i:1;s:10:"checkConst";}i:1;s:19:"Framework::REVISION";i:2;s:30:"7616569 released on 2011-03-10";}}}?><?php

// source file: C:\xampp\htdocs\Course-Manager\app\templates\@layout.latte

?><?php
$_l = LatteMacros::initRuntime($template, NULL, 'ofsf1lgs9i'); unset($_extends);

if (isset($presenter, $control) && $presenter->isAjax() && $control->isControlInvalid()) {
	return LatteMacros::renderSnippets($control, $_l, get_defined_vars());
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Course Manager</title>
        <!-- import BluePrint CSS framework -->
        <link rel="stylesheet" media="screen,projection,tv" href="<?php echo TemplateHelpers::escapeHtml($basePath) ?>/css/blueprint/screen.css" type="text/css" />
        <link rel="stylesheet" media="print" href="<?php echo TemplateHelpers::escapeHtml($basePath) ?>/css/blueprint/print.css" type="text/css" />
        <!--[if IE]><link rel="stylesheet" href="<?php echo TemplateHelpers::escapeHtmlComment($basePath) ?>/css/blueprint/ie.css" type="text/css" media="screen, projection"><![endif]-->

        <!-- import custom CSS styles -->
        <link rel="stylesheet" media="screen,projection,tv" href="<?php echo TemplateHelpers::escapeHtml($basePath) ?>/css/screen.css" type="text/css" />
        <link rel="stylesheet" media="print" href="<?php echo TemplateHelpers::escapeHtml($basePath) ?>/css/print.css" type="text/css" />
        <link rel="shortcut icon" href="<?php echo TemplateHelpers::escapeHtml($basePath) ?>/favicon.ico" type="image/x-icon" />
    </head>

    <body>

        <div class="container ">
            <div  id="header" class="span-24 last">
                <div id="logo">
                    <h1>Course Manager</h1>
                </div>
                <div id="mainmenu">
                    <ul>
                        <li><a href="<?php echo TemplateHelpers::escapeHtml($control->link("courselist:homepage")) ?>">My Courses</a></li>
                        <li><a href="<?php echo TemplateHelpers::escapeHtml($control->link("messages:inbox")) ?>"><b>Messages(2)</b></a></li>
                        <li><a href="<?php echo TemplateHelpers::escapeHtml($control->link("settings:homepage")) ?>">Settings</a></li>
                        <li><a href="<?php echo TemplateHelpers::escapeHtml($control->link("help:homepage")) ?>">Help</a></li>
                        <li><?php if ($logged): ?>

                            <a href="<?php echo TemplateHelpers::escapeHtml($control->link("user:homepage", array('id' => $userid))) ?>">                        
                            <?php echo TemplateHelpers::escapeHtml($user->email) ?>

                            </a>
                        <?php endif ?></li>
                    </ul>
                </div>

            </div>


            <div class="span-19" id="main">
                <div id="content" class="border">

                    <!-- content -->
<?php LatteMacros::callBlock($_l, 'content', $template->getParams()) ?>
                    <!-- /content -->
                </div>
            </div>

            <div class="span-5 last" id="sidebar">
                <div id="loginpart" class="border">
<?php if ($logged): ?>
                    Logged as <?php echo TemplateHelpers::escapeHtml($user->email) ?> <br />
                    <a href="<?php echo TemplateHelpers::escapeHtml($control->link("user:logout")) ?>">logout</a>
<?php else: ?>
                    <?php $_ctrl = $control->getWidget("signInForm"); if ($_ctrl instanceof IPartiallyRenderable) $_ctrl->validateControl(); $_ctrl->render() ?> <br />
                    <a href="<?php echo TemplateHelpers::escapeHtml($control->link("user:register")) ?>">register</a>
<?php endif ?>

                </div>
                <div id="courselist" class="border">
<?php if ($logged): ?>
                    <h3 class="caps">My courses</h3>
<?php foreach ($iterator = $_l->its[] = new SmartCachingIterator($courses) as $course): ?>
                    <div class="div-course border">
                        <a href="<?php echo TemplateHelpers::escapeHtml($control->link("course:homepage", array('id' => $course['id']))) ?>"><?php echo TemplateHelpers::escapeHtml($course['name']) ?></a>
                        <div class="italic">Lector Name</div>
                        <div class="time">date/time</div>
                        <ul>
                            <li><a class="demo-active" target="demo-iframe" href="lessons.xhtml">Lessons</a></li>
                            <li><a class="demo-active" target="demo-iframe" href="results.xhtml">Results</a></li>
                            <li><a class="demo-active" target="demo-iframe" href="assignments.xhtml"><b>Assignments(1)</b></a></li>
                            <li><a class="demo-active" target="demo-iframe" href="resources.xhtml">Resources</a></li>
                            <li><a class="demo-active" target="demo-iframe" href="forum.xhtml">Forum</a></li>
                            <li><a class="demo-active" target="demo-iframe" href="events.xhtml">Events</a></li>
                        </ul>
                    </div>
<?php endforeach; array_pop($_l->its); $iterator = end($_l->its) ?>
                    <div id="add-course"><a href="<?php echo TemplateHelpers::escapeHtml($control->link("course:add")) ?>">+ Add Course</a></div>
<?php endif ?>

                </div>
                <div id="events" class="border">
                    <h3 class="caps">Upcoming events</h3>
                    <div class="event border">
                        <div class="date">Friday 21.3.2010 12:00</div>
                        <div class="name"><b>Test 4</b></div>
                        <div class="description">
                            Nam pellentesque luctus sadsag lewilksl akjdhweu...
                        </div>
                    </div>
                    <hr />
                    <div class="event border">
                        <div class="date">Monday 24.3.2010 12:00</div>
                        <div class="name"><b>Test 5</b></div>
                        <div class="description">
                            Aliquam erat volutpat. Integer nec tincidunt felis...</div>
                    </div>
                    <hr />
                </div>
            </div>
        </div>
        <div id="footer" class="border">
            Course Manager by Jakub Kinst
        </div>
    </body>
</html>
