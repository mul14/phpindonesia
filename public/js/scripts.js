$(function () {

	// PLACEHOLDER TIP
	$(':input').focusin(function(){
		if ($(this).attr('type') != 'submit' && $(this).attr('data-provider').indexOf('bootstrap-markdown')<0) {
			$(this).tooltip({
				placement: 'right',
				title: $(this).attr('placeholder'),
			});
			$(this).tooltip('show');
		}
	});

	$(':input').focusout(function(){
		$(this).tooltip('hide');
	});
});