
	jQuery(document).ready(function(){
		
		var form_table = jQuery('table.edit');
		var errors_container = jQuery('div.error');
		var iframe_counter = 0;
		
		function updateFieldsGalleryId() {
			var gallery_id = jQuery('[name=gallery_id]').val(); 
			jQuery('iframe').each(function(){
				jQuery(this).contents().find('[name=gallery_id]').val(gallery_id);				
			});
		}
				
		function addImageField() {
			iframe_counter++;
			var ifc = iframe_counter;
			var row = jQuery('<tr />').appendTo(form_table);
			var cell = jQuery('<td />').attr({
				colspan: 2				
			}).appendTo(row);
			jQuery('<iframe />').attr({
				src: add_photo_iframe_src,
				border: 0				
			}).css({
				border: 'none',
				width: 700,
				height: 50
			}).appendTo(cell).load(function(){				
				var iframe = jQuery(this); 
				var num_span = iframe.contents().find('span.num'); 
				num_span.html('#' + ifc);
				var errors = iframe.contents().find('.error');
				if (num_span.size()==0) {
					var ok_msg = jQuery('<div />').css({
						width: iframe.width(),
						height: iframe.height(),
						lineHeight: iframe.height()+'px',
						color: 'green',
						textAlign: 'center'
					}).html('Фото #' + ifc + ' cохранено');
					
					iframe.replaceWith(ok_msg);
					iframe_counter--;
					if (iframe_counter == 0) {
						jQuery('input[name=back]').click();
					}
				}
				else {
					errors.each(function(){					
						errors_container.append('Фото #' + ifc + ': ' + jQuery(this).html() + '<br>');
					});				
				}
				updateFieldsGalleryId();
			});
		}
		
		jQuery('input[name=save]').click(function(){
			errors_container.html('');
			form_table.find('iframe').each(function(){
				jQuery(this).contents().find('input[name=save]').click();				
			});
			return false;
		});
		
		jQuery('select[name=gallery_id]').change(function(){
			var new_gallery_id = jQuery(this).val();
			form_table.find('iframe').each(function(){
				jQuery(this).contents().find('input[name=gallery_id]').val(new_gallery_id);				
			});
		});
		
		jQuery('[name=add-field]').click(function(){
			addImageField();	
			return false;
		});
		
		var gallery_id = jQuery('[name=gallery_id]').change(function(){
			updateFieldsGalleryId();
		}); 
		
		addImageField();
		
		
	});