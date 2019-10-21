

	jQuery(document).ready(function(){
		
		var periods_container = jQuery('#education-periods');
		var files_container = jQuery('#files');

		
		function set_controls_visibility(container) {
			var items_count = container.find('.user-inputs > div').length;
			if (items_count <= 1) {
				container.find('.remove-item').hide();
			}
			else {
				container.find('.remove-item').show();
			}
		}
		
		
		function render_period_row(period_start, period_comment) {
			var user_inputs = periods_container.find('.user-inputs'); 
			var row = jQuery('<div />').appendTo(user_inputs);
			
			var period_select = jQuery('<select />').attr({
				name: 'education_periods[start_year][]'
			}).appendTo(row);
			jQuery.each(education_periods_options, function(start_year, option_title){
				jQuery('<option />').attr({
					value: start_year
				}).html(option_title).appendTo(period_select);
			});
			
			jQuery('<span />').html(' стоимость: ').appendTo(row);
			var comment_input = jQuery('<input />').attr({
				type: 'text',
				name: 'education_periods[comment][]'
			}).val(period_comment).appendTo(row);
			
			jQuery('<a />').attr({
				href: '#'
			}).addClass('remove-item').html('X').appendTo(row).click(function(event){
				event.preventDefault();
				row.remove();
				set_controls_visibility(periods_container);				
			});
			
			
			set_controls_visibility(periods_container);
		}

		
		function render_file_row() {
			var user_inputs = files_container.find('.user-inputs'); 
			var row = jQuery('<div />').appendTo(user_inputs);
			
			jQuery('<input />').attr({
				type: 'file',
				name: 'attachment[]'
			}).appendTo(row);
			
			
			jQuery('<a />').attr({
				href: '#'
			}).addClass('remove-item').html('X').appendTo(row).click(function(event){
				event.preventDefault();
				row.remove();
				set_controls_visibility(files_container);				
			});
			
			
			set_controls_visibility(files_container);
		}

		
		
		jQuery.each(education_periods, function(idx, item){
			render_period_row(item['start_year'], item['comment']);
		});
		
		for(var i=0; i<files_count; i++) {
			render_file_row();
		}
		
		
		periods_container.find('.add-item').click(function(event) {
			event.preventDefault();			
			render_period_row(null, null);			
		});
		
		
		files_container.find('.add-item').click(function(event) {
			event.preventDefault();			
			render_file_row();			
		});

		
		
		
	});