
	function grab_form_data(form) {
		var data = {};
		jQuery(form).find('input, textarea, select').each(function(){
			if (jQuery(this).is('[type=button]')) return;
			if (jQuery(this).is('[type=checkbox]') && !jQuery(this).is(':checked')) return;
			if (jQuery(this).is('[type=radio]') && !jQuery(this).is(':checked')) return;
						
			var name = jQuery(this).attr('name');
			var value = jQuery(this).is('.default.labeled_with_title') ? '' : jQuery(this).val();
			
			if (name.indexOf('[') != -1) {				
				var matches = name.match(/([a-z0-9_]+)\[([a-z0-9_]*)\]/);
				var fieldname = matches[1];
				var index = matches[2];
				
				if (typeof(data[fieldname]) == 'undefined') data[fieldname] = index=='' ? [] : {};
				if (index=='') {
					data[fieldname].push(value);	
				}
				else {
					data[fieldname][index] = value;
				}
			}
			else {
				data[name] = value;	
			}
		});
		
		return data;
	}



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
			$.getScript('/applications/abc/static/js/jquery.jgrowl.min.js', function(){
				jgrowl_loaded = true;

				jQuery.jGrowl.defaults.closerTemplate = '<div>[ скрыть все сообщения ]</div>';
				jQuery.jGrowl.defaults.life = 10000;
				jQuery.jGrowl.defaults.speed = 100;

				overlay_message(type, text);
			});	
		}
	}


	
	$(document).ready(function(){
		
		$(document).ajaxError(function(event, request, settings){
			_unblock();
			alert('Ошибка AJAX');
		});
		
	});
