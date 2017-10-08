


	function _block(container_selector) {		
		if ($('.blocker').length != 0) return;
		
		if (typeof(container_selector) == 'undefined') {
			container_selector = 'body';
		}
		
		$('<div />').addClass('blocker').css({
			height: $(container_selector).height(),
			width: $(container_selector).width(),
			zIndex: 99
		}).prependTo(container_selector).animate({opacity: 0.8}, 400);
	}
	
	function _unblock() {		
		if ($('.blocker').length == 0) return;
		$('.blocker').animate({opacity: 0}, 400, function(){
			$('.blocker').remove();	
		});
	}		
	
	
	var jgrowl_loaded = false;
	
	function overlay_message(type, text) {
		if (jgrowl_loaded) {
			jQuery.jGrowl(text, {
				theme: type
			});			
		}
		else {			
			$.getScript('/applications/abc_admin/static/js/jquery.jgrowl.min.js', function(){
				jgrowl_loaded = true;

				jQuery.jGrowl.defaults.closerTemplate = '<div>[ скрыть все сообщения ]</div>';
				jQuery.jGrowl.defaults.life = 10000;
				jQuery.jGrowl.defaults.speed = 100;

				overlay_message(type, text);
			});	
		}
	}

	
	$(document).ready(function(){		
		$('td.caption').click(function(){
			var cb = $(this).prev('.checkbox').find('input');
			cb.click();
		});
	});

