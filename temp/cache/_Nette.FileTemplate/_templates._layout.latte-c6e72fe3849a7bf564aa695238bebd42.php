<?php //netteCache[01]000344a:2:{s:4:"time";s:21:"0.09340600 1309268680";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:5:"Cache";i:1;s:9:"checkFile";}i:1;s:58:"C:\xampp\htdocs\Course-Manager\app\templates\@layout.latte";i:2;i:1309268675;}i:1;a:3:{i:0;a:2:{i:0;s:5:"Cache";i:1;s:10:"checkConst";}i:1;s:19:"Framework::REVISION";i:2;s:30:"7616569 released on 2011-03-10";}}}?><?php

// source file: C:\xampp\htdocs\Course-Manager\app\templates\@layout.latte

?><?php
$_l = LatteMacros::initRuntime($template, NULL, '7n7inyzimv'); unset($_extends);

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
        
        <!-- jQuery load -->
        <script src="http://code.jquery.com/jquery-latest.js"></script>
        
    </head>

    <body>
        <div class="container">
            <div  id="header" class="span-24 last">
                <a href="<?php echo TemplateHelpers::escapeHtml($control->link("courselist:homepage")) ?>">
                    <div id="logo">
                        <h1>Course Manager</h1>
                    </div>
                </a>
<?php if ($logged): ?>
                <div id="mainmenu">
                    <ul>
                        <li><a href="<?php echo TemplateHelpers::escapeHtml($control->link("courselist:homepage")) ?>">My Courses</a></li>
                        <li><a class="notActive" href="<?php echo TemplateHelpers::escapeHtml($control->link("notification:homepage")) ?>">Notifications</a></li>
                        <li><a class="notActive" href="<?php echo TemplateHelpers::escapeHtml($control->link("messages:inbox")) ?>"><b>Messages(2)</b></a></li>
                        <li><a class="notActive" href="<?php echo TemplateHelpers::escapeHtml($control->link("settings:homepage")) ?>">Settings</a></li>
                        <li><a class="notActive" href="<?php echo TemplateHelpers::escapeHtml($control->link("help:homepage")) ?>">Help</a></li>
                        <li>
                            <a class="notActive" href="<?php echo TemplateHelpers::escapeHtml($control->link("user:homepage", array($userid))) ?>">                        
                                <?php echo TemplateHelpers::escapeHtml($user->email) ?>

                            </a>                           
                        </li>
                    </ul>
                </div>
<?php endif ?>

            </div>


            <div class="span-19" id="main">
                <div id="content" class="border">
                    <!-- content -->                    
<?php LatteMacros::callBlock($_l, 'content', $template->getParams()) ?>
                    <!-- /content -->
                </div>
            </div>

            <div class="span-5 last" id="sidebar">
                <div id="loginBox" class="border">
<?php if ($logged): ?>
                        <div class="loggedUser"><?php echo TemplateHelpers::escapeHtml($user->email) ?></div>
                        <div class="buttonLine">
                            <a href="<?php echo TemplateHelpers::escapeHtml($control->link("user:logout")) ?>">LOG OUT</a>
                        </div>
<?php else: $_ctrl = $control->getWidget("signInForm"); if ($_ctrl instanceof IPartiallyRenderable) $_ctrl->validateControl(); $_ctrl->render() ?>
                        <div class="buttonLine">
                            <a href="<?php echo TemplateHelpers::escapeHtml($control->link("user:register")) ?>">REGISTER</a>
                        </div>
<?php endif ?>

                </div>
<?php if ($logged): ?>
                    <div id="courseBox" class="border">                    
                        <h3 class="caps">Teachered courses</h3>
<?php foreach ($iterator = $_l->its[] = new SmartCachingIterator($tCourses) as $course): ?>
                            <div class="course asTeacher">
                                <a href="<?php echo TemplateHelpers::escapeHtml($control->link("course:homepage", array($course['id']))) ?>">
                                    <div class="title"><?php echo TemplateHelpers::escapeHtml($course['name']) ?></div>
                                </a>
                                <div class="lectors">
<?php foreach ($iterator = $_l->its[] = new SmartCachingIterator($course['lectors']) as $lector): ?>
                                        <a href="<?php echo TemplateHelpers::escapeHtml($control->link("user:homepage", array($userid))) ?>" class="lector"><?php echo TemplateHelpers::escapeHtml($lector['email']) ?></a>
<?php endforeach; array_pop($_l->its); $iterator = end($_l->its) ?>
                                </div>
                                <ul>
                                    <li><a href="<?php echo TemplateHelpers::escapeHtml($control->link("course:homepage", array($course->id))) ?>">Lessons</a></li>
                                    <li><a href="<?php echo TemplateHelpers::escapeHtml($control->link("result:homepage", array($course->id))) ?>">Results</a></li>
                                    <li><a class="notActive" href="assignments.xhtml"><b>Assignments</b></a></li>
                                    <li><a class="notActive" href="resources.xhtml">Resources</a></li>
                                    <li><a class="notActive" href="forum.xhtml">Forum</a></li>
                                    <li><a class="notActive" href="events.xhtml">Events</a></li>
                                </ul>
                            </div>
<?php endforeach; array_pop($_l->its); $iterator = end($_l->its) ?>
                        <h3 class="caps">Student courses</h3>
<?php foreach ($iterator = $_l->its[] = new SmartCachingIterator($sCourses) as $course): ?>
                            <div class="course asStudent">
                                <a href="<?php echo TemplateHelpers::escapeHtml($control->link("course:homepage", array($course['id']))) ?>">
                                    <div class="title"><?php echo TemplateHelpers::escapeHtml($course['name']) ?></div>
                                </a>
                                <div class="lectors">
<?php foreach ($iterator = $_l->its[] = new SmartCachingIterator($course['lectors']) as $lector): ?>
                                        <a href="<?php echo TemplateHelpers::escapeHtml($control->link("user:homepage", array($userid))) ?>" class="lector"><?php echo TemplateHelpers::escapeHtml($lector['email']) ?></a>
<?php endforeach; array_pop($_l->its); $iterator = end($_l->its) ?>
                                </div>
                                <ul>
                                    <li><a href="<?php echo TemplateHelpers::escapeHtml($control->link("course:homepage", array($course['id']))) ?>">Lessons</a></li>
                                    <li><a href="<?php echo TemplateHelpers::escapeHtml($control->link("result:homepage", array($course->id))) ?>">Results</a></li>
                                    <li><a class="notActive" href="assignments.xhtml"><b>Assignments</b></a></li>
                                    <li><a class="notActive" href="resources.xhtml">Resources</a></li>
                                    <li><a class="notActive" href="forum.xhtml">Forum</a></li>
                                    <li><a class="notActive" href="events.xhtml">Events</a></li>
                                </ul>
                            </div>
<?php endforeach; array_pop($_l->its); $iterator = end($_l->its) ?>
                    </div>
<?php endif ?>

                               
            </div>
        </div>
        <div id="footer" class="border">
            Course Manager by Jakub Kinst
        </div>
        
        <!-- Flash messages -->
        <div id="flashMessages">
<?php foreach ($iterator = $_l->its[] = new SmartCachingIterator($flashes) as $flash): ?>
                <div class="flash <?php echo TemplateHelpers::escapeHtml($flash->type) ?>">
                    <?php echo TemplateHelpers::escapeHtml($flash->message) ?>

                </div>            
<?php endforeach; array_pop($_l->its); $iterator = end($_l->its) ?>
        </div>
        
        <!-- Load Visual JavaScript functions -->
        <script src="<?php echo TemplateHelpers::escapeHtml($basePath) ?>/js/visual.js" />
    </body>
</html>
