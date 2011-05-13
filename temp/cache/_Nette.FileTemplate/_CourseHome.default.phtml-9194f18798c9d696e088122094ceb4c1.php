<?php //netteCache[01]000343a:2:{s:4:"time";s:21:"0.66675100 1302030372";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:5:"Cache";i:1;s:9:"checkFile";}i:1;s:57:"C:\xampp\htdocs\rp\app\templates\CourseHome\default.phtml";i:2;i:1302030369;}i:1;a:3:{i:0;a:2:{i:0;s:5:"Cache";i:1;s:10:"checkConst";}i:1;s:19:"Framework::REVISION";i:2;s:30:"7616569 released on 2011-03-10";}}}?><?php

// source file: C:\xampp\htdocs\rp\app\templates\CourseHome\default.phtml

?><?php
$_l = LatteMacros::initRuntime($template, NULL, '6940z6ieoy'); unset($_extends);


//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lb9b67abbdc8_content')) { function _lb9b67abbdc8_content($_l, $_args) { extract($_args)
?>
<div class="span-19 colborder" id="content">
    <h3 class="loud"><?php echo TemplateHelpers::escapeHtml($activeCourse['label']) ?></h3>
    <p>
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean eget nisi a leo rhoncus posuere sit amet et est. Nam mollis dignissim hendrerit. Ut lacus felis, ultrices quis posuere sit amet, ornare et odio. Integer mi massa, sagittis sit amet feugiat vitae, mollis sed nulla. Sed purus tellus, tristique eget feugiat quis, elementum pulvinar lectus. Suspendisse lectus dui, mattis at imperdiet vel, malesuada id dolor. Etiam aliquet ornare massa a aliquam. Phasellus vel justo non sem tristique mollis vitae ac elit. Praesent eget pharetra augue. Vivamus sollicitudin nulla vel nisi feugiat sodales. Sed rhoncus augue vel enim iaculis quis cursus enim tristique. Praesent porta dui at leo vulputate vehicula. In tempor, augue rutrum porta fermentum, ligula justo blandit nisi, vel malesuada urna quam nec ligula.
    </p>

    <p>
        Maecenas posuere auctor mauris a tempus. Morbi scelerisque libero dolor. Sed dignissim ante in sapien tempor suscipit. Sed eros risus, euismod ut dignissim at, mollis sed velit. Sed eu eros eget orci ullamcorper tincidunt non sit amet sapien. Donec consectetur pellentesque diam, a imperdiet nulla malesuada ac. Ut accumsan cursus dui convallis egestas. Integer pretium ultrices vulputate. Donec enim velit, cursus condimentum pulvinar vel, vestibulum ac neque. Morbi vitae diam at tortor condimentum posuere a eu purus. Pellentesque vehicula porta consectetur. Integer sed justo fringilla nunc posuere gravida. Nullam quis varius risus. Cras non auctor eros. Quisque lobortis odio vel arcu pellentesque sagittis ac id quam. Sed vulputate lectus at nulla venenatis eget semper diam euismod. Ut ut neque tortor. Ut sodales tempor libero, eu ornare urna ullamcorper sit amet.
    </p>
    
</div>

<div class="span-4 last" id="sidebar">
    <div id="courselist">
        <h3 class="caps">My courses</h3>
<?php foreach ($iterator = $_l->its[] = new SmartCachingIterator($courses) as $course): ?>
            <div class="div-course">
                <a href="<?php echo TemplateHelpers::escapeHtml($control->link("default", array('id' => $course['id']))) ?>"><?php echo TemplateHelpers::escapeHtml($course['label']) ?></a>
                <div class="italic"><?php echo TemplateHelpers::escapeHtml($course['lector']) ?></div>
                <div class="time"><?php echo TemplateHelpers::escapeHtml($course['time']) ?></div>
            </div>
<?php endforeach; array_pop($_l->its); $iterator = end($_l->its) ?>

    </div>
</div>

<hr />  
<?php
}}


//
// block head
//
if (!function_exists($_l->blocks['head'][] = '_lb5e91825cc3_head')) { function _lb5e91825cc3_head($_l, $_args) { extract($_args)
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
?>

<?php if (!$_l->extends) { call_user_func(reset($_l->blocks['content']), $_l, get_defined_vars()); } ?>


<?php if (!$_l->extends) { call_user_func(reset($_l->blocks['head']), $_l, get_defined_vars()); }  
if ($_l->extends) {
	ob_end_clean();
	LatteMacros::includeTemplate($_l->extends, get_defined_vars(), $template)->render();
}
