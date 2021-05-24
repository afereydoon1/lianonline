jQuery(document).ready(function($) {
	
	// TOOLTIPS
	$('[data-toggle="tooltip"]').tooltip({
		trigger : 'hover'
	});
	
	// SLIM SCROLL
	$('.slimscroll').slimScroll({
		position		: 'left',
		height			: '100%',
		alwaysVisible	: true
	});
	
	// TOGGLE SUB MENU
	$(document).on('click', '.app-panel-menu .has-children > .nav-link', function (e) {
        e.preventDefault();
		var $li = $(this).closest('li');
		
		if( $li.hasClass('open') )
		{
			$('.app-panel-menu .has-children').removeClass('open');
			$('.app-panel-menu .children').fadeOut(200);
		}
		else
		{
			$('.app-panel-menu .has-children').removeClass('open');
			$('.app-panel-menu .children').fadeOut(200);
			$li.addClass('open');
			$li.find('.children').fadeIn(200);
		}
    });
	
	// TOGGLE MOBILE MENU
	$(document).on('click', '#app-toggle-mobile-menu', function (e) {
        e.preventDefault();
        $('.app-panel-menu').toggleClass('open');
		$('#app-toggle-mobile-menu').toggleClass('is-active');
    });
	
	// TOUCH MOBILE MENU
	$('#app-panel-wrapper').swipe({
		swipeStatus:function(event, phase, direction, distance, duration, fingers) {
			if( phase == "move" && direction == "right" ) {
				$('.app-panel-menu').removeClass('open');
			}
			if( phase == "move" && direction == "left" ) {
				$('.app-panel-menu').addClass('open');
			}
		}
	});
	
	
	
	// LOGIN REQUIRED ERROR
	$(document).on('click', '.mlm-need-to-purchase-plan-btn', function (e) {
		e.preventDefault();
        swal({
			title	: 'خطا',
			text	: 'جهت دانلود مستقیم این محصول باید اشتراک ویژه سایت را خریداری نمایید.',
			icon	: 'error',
			button	: 'باشه!',
		});
    });
})