$(function(){
	$('.myul li').click(function(){
		$(this).siblings().removeClass('on');
		$(this).addClass('on');
		var forClass = $(this).data('class');
		$('.'+forClass).siblings('section').hide();
		$('.'+forClass).show();
	});
})