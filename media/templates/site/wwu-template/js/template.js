/*20230221PopupFunction*/
$(document).ready(function(){
	/*FixPopup*/
	$('.captionTitle').click(function(){
		var parent = $(this).parent();
		if($(parent).hasClass('active')){
			$(parent).removeClass('active');
		}else{
			$(parent).addClass('active');
		}
	});
	/*MobileClick*/
	$('.mobile_nav').click(function(){
		var parent = $(this).parent();
		if($(parent).hasClass('active')){
			$(parent).removeClass('active');
		}else{
			$(parent).addClass('active');
		}
	});

}); 