
	$(document).ready(function(){
		
		$('.schedule .actions a').live('click', function(){
			var day_number = parseInt($(this).attr('id').toString().substr(10));
			
			var cell = $('.schedule .entries td:nth-child('+day_number+')')
			var entry = $('<div />').addClass('entry').appendTo(cell);
			$('<a />').addClass('del').attr({
				href: '#',
				title: 'Удалить'
			}).appendTo(entry);
			
			$('<input />').attr({
				type: 'text',
				name: 'schedule['+day_number+'][]'				
			}).val('00:00').appendTo(entry);
			
			return false;
		});
		
		
		
		function validateTable(show_message) {
			var has_format_errors = false;
			var has_intersection_errors = false;
			if (typeof(show_message) == 'undefined') show_message = true;
			
			$('.schedule input').removeClass('error');
			
			$('.schedule .entries td').each(function(){
				var timestamps = {};
				var cell = $(this);
				cell.find('.entry').each(function(){
					var inp = $(this).find('input');
					var time = inp.val().toString();
					
					var time_matches = time.match(/^([0-2]?[0-9]):[0-5][0-9]$/); 
					
					if (!time_matches || parseInt(time_matches[1])>23) {
						has_format_errors = true;
						inp.addClass('error');
					}
					else if(typeof(timestamps[time]) != 'undefined') {
						has_intersection_errors = true;
						inp.addClass('error');
						timestamps[time].find('input').addClass('error');
					}
					else {
						var entry = $(this);
						timestamps[time] = entry;
					}
				});
			});
			
			
			if (show_message && (has_format_errors || has_intersection_errors)) {
				var error_message = '';
				if (has_format_errors) error_message = error_message + 'Не все поля расписания заполнены правильно. ';
				if (has_intersection_errors) error_message = error_message + 'Время начала занятий пересекается. ';
				alert(error_message);
			}
			
			return !has_format_errors && !has_intersection_errors;
			
		}
		
		
		$('.schedule .entry .del').live('click', function(){
			var entry = $(this).parents('.entry:first');
			entry.slideUp(300, function(){
				entry.remove();
				validateTable(false);
			});
			return false;
		});

		
		$('.schedule input').live('keyup', function(){
			validateTable(false);
		});
		
		$('.schedule').parents('form').submit(function(){
			return validateTable();			 
		});
		
		
		$('.schedule').show();
		
	});