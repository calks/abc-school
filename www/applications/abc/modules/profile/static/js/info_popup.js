
	jQuery(document).ready(function($){
		
		
		$('.user_info').click(function(){
			var endpoint = $(this).attr('href').toString();
			var is_teacher_view_link = $(this).hasClass('teacher');
			var popup = $('<div />').appendTo('body');
			var popup_content = $('<div />').addClass('text').appendTo(popup);
			
			var buttons;
			if (is_teacher_view_link || !can_edit_others_profile) {
				buttons = {
					'Закрыть': function(){
						popup.dialog('close');
					}
				}
			}
			else {
				buttons = {
					'Сохранить': function(){
						var form_data = grab_form_data(popup_content.find('form'));
						form_data.submit = 1;						
						makeRequest(form_data, function(response){
							var error = typeof(response.error) != 'undefined' ? response.error : null;
							if (!error) popup.dialog('close');
						});
					},
					'Отмена': function(){
						popup.dialog('close');
					}
				}
			}	
			
			
			popup.dialog({
				title: is_teacher_view_link ? 'Карточка преподавателя' : 'Карточка ученика',
				width: 560,
				position: 'center',
				modal: true,
				resizable: false,
				autoOpen: true,
				buttons: buttons,
				close: function(){
					popup.remove();					
				}
			});	

			
			function centerDialog() {
				var dialog = popup.parents('.ui-dialog');
				var dialog_width = dialog.outerWidth();
				var dialog_height = dialog.outerHeight();
				var window_width = $(window).width();
				var window_height = $(window).height();
				var left = Math.ceil(window_width/2-dialog_width/2);
				var top = Math.ceil(window_height/2-dialog_height/2);
				if (top < 10) top = 10;
				top += $(window).scrollTop();
				dialog.css({
					left: left,
					top: top
				});
			}
			
			
			function makeRequest(data, callback) {
				if (typeof(data) == 'undefined') data = {};
				data.ajax = 1;
				_block(popup);
				$.ajax({
					url: endpoint,
					type: 'post',
					dataType: 'json',
					data: data,
					success: function(response){
						_unblock();
						var error = typeof(response.error) != 'undefined' ? response.error : null;
						if (error) overlay_message('error', error);
						
						var message = typeof(response.message) != 'undefined' ? response.message : null;
						if (message) overlay_message('ok', message);
						
						var content = typeof(response.content) != 'undefined' ? response.content : null;
						if (content) {
							popup_content.html(content);
							centerDialog();
						}
					
						if (typeof(callback) == 'function') callback(response);
					}					
				});
				
				
			}
			
			makeRequest({}, function(response){
				var error = typeof(response.error) != 'undefined' ? response.error : null;
				if (error) popup.dialog('close');
			});
			
			
			
			
			
			return false;
			
		});
		
	});