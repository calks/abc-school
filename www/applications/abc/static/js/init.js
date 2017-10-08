		

	$(document).ready(function(){
		$('form input, form select, form textarea').each(function(){
			input_name = $(this).attr('name');
			form_container = $(this).parents('form');
			error_div = form_container.find('div.error.' + input_name);
			
			if (error_div.length > 0) $(this).addClass('error');			
		});		
		
	});