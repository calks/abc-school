

	$(document).ready(function(){
		
		$('.education_form_module .form_switch a').click(function(event){
			if ($(this).hasClass('active')) return false;
			
			page_content_container = $(this).parents('.text');
			
			form_name = $(this).attr('class');
			active_form_name = page_content_container.find('form').not('.hidden').attr('class');
			
			active_form_height = page_content_container.find('form.' + active_form_name).height();
				
			content_height = page_content_container.height();
			page_content_container.css({
				height: content_height,
				overflow: 'hidden'				
			});
			
			page_content_container.find('form.' + form_name).find('input:not(.submit):not([type=hidden]), textarea, select').each(function(){
				if ($(this).attr('type') == 'checkbox') $(this).removeAttr('checked');
				if ($(this).attr('type') == 'radio') $(this).removeAttr('checked');
				$(this).val('');
			});
			
			page_content_container.find('div.error').remove();
			page_content_container.find('input.error, textarea.error, select.error').removeClass('error');

			
			page_content_container.find('form.' + active_form_name).slideUp(300, function(){
				page_content_container.find('form.' + form_name).removeClass('hidden');
				page_content_container.find('form.' + active_form_name).addClass('hidden');

				page_content_container.find('form.' + form_name).slideDown(300, function(){
					page_content_container.css({
						height: 'auto'
					});
				});
			});
						
			$(this).parent('div').find('a.' + active_form_name).removeClass('active');
			$(this).addClass('active');
			
			//alert(active_form_name);
			
			
			return false;
		});
		
		
		
	});