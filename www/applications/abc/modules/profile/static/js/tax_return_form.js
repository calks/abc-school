

	jQuery(document).ready(function(){
		
		var periods_container = jQuery('#education-periods');

		
		function set_controls_visibility() {
			var periods_count = periods_container.find('.user-inputs > div').length;
			if (periods_count <= 1) {
				periods_container.find('.remove-item').hide();
			}
			else {
				periods_container.find('.remove-item').show();
			}
		}
		
		
		function collect_data() {
			var data = [];
			
			var year_fields = periods_container.find('input.period-start-year');
			var comment_fields = periods_container.find('input.period-comment');
			
			console.log(year_fields);
			
			var idx = 0;
			year_fields.each(function(){
				var year = jQuery(this).val();
				var comment = comment_fields.eq(idx).val();				
				idx++;
				data.push({
					start_year: year,
					comment: comment
				});
			});
			
			return data;
			
		}
		
		
		function render_period_row(period_start, period_comment) {
			var user_inputs = periods_container.find('.user-inputs'); 
			var row = jQuery('<div />').appendTo(user_inputs);
			
			var period_select = jQuery('<select />').attr({
				name: 'education_periods[][start_year]'
			}).appendTo(row);
			jQuery.each(education_periods_options, function(start_year, option_title){
				jQuery('<option />').attr({
					value: start_year
				}).html(option_title).appendTo(period_select);
			});
			
			jQuery('<span />').html(' стоимость: ').appendTo(row);
			var comment_input = jQuery('<input />').attr({
				type: 'text'
			}).val(period_comment).appendTo(row);
			
			jQuery('<a />').attr({
				href: '#'
			}).addClass('remove-item').html('X').appendTo(row).click(function(event){
				event.preventDefault();
				row.remove();
				set_controls_visibility();				
			});
			
			
			set_controls_visibility();
		}
		
		
		var data = collect_data();
		console.log(data);
		
		
		jQuery.each(data, function(idx, item){
			render_period_row(item['start_year'], item['comment']);
		});
		
		
		periods_container.find('.add-item').click(function(event) {
			event.preventDefault();			
			render_period_row(null, null);			
		});
		
		
		
	});