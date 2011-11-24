
$(document).ready(function(){
        
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
    $(document).ready(function(){
	$("select").multiselect({
	    header: "Choose Anwsers !"
	});
    });
});