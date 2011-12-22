
$(document).ready(function(){

	// hide elements with class hide
	$('.hide').hide();

	// confirm dialog when deleting
	$("a.delete").click(function(event) {
		event.preventDefault();
		if (this.getAttribute("msg")!=null)
			msg = this.getAttribute("msg");
		else
			msg = delete_confirm_message;
		if (confirm(msg))
			window.location = this.href;
	});

	// accordion
	$( "#accordion" ).accordion();

	// datepickers
	$( ".datetimepicker" ).datetimepicker();
	$( ".datepicker" ).datepicker();

	// Set texyla defaults
	$.texyla.setDefaults({
		baseDir: texylaHome,
		previewPath: texyla_preview_link,
		bottomRightPreviewToolbar:[],
		language: "en",
		toolbar:['bold', 'italic','color',null,'h1','h2','h3','h4', null,'center','left','right','justify',null, 'ul', 'ol', null,'blockquote','sub','sup','code','codeHtml','codePhp','codeInline', 'link', null, 'emoticon', 'symbol', "img", "table", null]
	});

	// Texyla textareas
	$('.texyla').texyla();



	// Flash messages fade out
	setTimeout(function() {
		$('#flashMessages').fadeOut();
	}, 2000);

	// set ajax links
	$("a.ajax").live("click", function (event) {
		event.preventDefault();
		$.get(this.href);
	});

	// ajax form sending
	$("form.ajax").submit(function () {
		$(this).ajaxSubmit();
		return false;
	});
	// ajax form sending
	$("form.ajax :submit").click(function () {
		$(this).ajaxSubmit();
		return false;
	});
	// ajax snipped graphical update
	jQuery.nette.updateSnippet = function (id, html) {
		$("#" + id).fadeTo("fast", 0.01, function () {
			$(this).html(html).fadeTo("fast", 1);
		});
	};

	//set jquery multiselect
	$("select.multi").multiselect({
		noneSelectedText: choose_anwsers_message,
		minWidth:300,
		autoOpen:false
	});
	$("select.multi").multiselect('close');

	//loginbox dropdown
	$("#userButton").click(function(){
		$("#loginBox").slideToggle();
	});


	//course minimenu dropdown
	if (active_course_id!=''){
		$('#ul_'+active_course_id).show();
	}
	$('.dropdown').click(function(event){
		event.preventDefault();
		var id = this.id;
		$('#ul_'+id).slideToggle();
	});

	// lesson dropdown
	$('.lesson_dropdown').click(function(event){
		event.preventDefault();
		var id = this.id;
		$('#div_'+id).slideToggle();
	});
});