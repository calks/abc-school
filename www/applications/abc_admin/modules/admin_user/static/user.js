
	$(document).ready(function(){
		
		function set_group_actions_visibility() {
			if ($('.select_row:checked').size()>0) {
				$('.group_actions').show();
			}
			else {
				$('.group_actions').hide();
				$('#select_all_rows').removeAttr('checked');
			}
		}
		
		$('#select_all_rows').click(function(){
			if($(this).attr('checked')) {
				$('.select_row').attr({checked: 'checked'});
			}
			else {
				$('.select_row').removeAttr('checked');
			}
			set_group_actions_visibility();
		});
		
		
		$('.select_row').click(function(){
			set_group_actions_visibility();			
		});
			
			
		
		
		$('.group_actions .assign_group').bind('click', function(){
			var group_id = parseInt($(this).parents('li').find('select').val());
			
			if (!group_id) {
				alert('Нужно выбрать группу');
				return false;
			}
			
			var users_adding_group = [];
			var users_changing_group = [];
			
			$('.select_row:checked').each(function(){
				var row = $(this).parents('tr');
				var role = row.find('input[name=role]').val();
				var name = $.trim(row.children('td:nth-child(5)').html());				
				var assigned_any = row.find('input.group_id').size() > 0;
				var assigned_this = row.find('input.group_id[value='+group_id+']').size() > 0;
				
				if (assigned_any && !assigned_this) {					
					if (role=='user') users_changing_group.push(name);
					else {
						users_adding_group.push(name);												
					}
				}
			});
			
			function doPost() {
				$('input[name=action]').val('assign_group');
				$('input[name=new_group_id]').val(group_id);
				$('input[name=action]').parents('form').submit();
			}
						
			
			if (users_adding_group.length>0 || users_changing_group.length>0) {
				var message = 'После выполнения этого действия:';
				if (users_adding_group.length>0) {
					message = message + '<br>' + users_adding_group.length + ' пользователям будет добавлена группа';
				}
				if (users_changing_group.length>0) {
					message = message + '<br>' + users_changing_group.length + ' пользователям будет изменена группа';
				}
				
				var dlg = $('<div />').html(message).dialog({
					modal: true,
					resizable: false,
					buttons: {
						'Продолжить': function(){
							doPost();
							$(this).dialog('close');
						},
						'Я передумал': function(){							
							$(this).dialog('close');
						}					
					}
				});
			}
			else {
				doPost();
			}
			
			return false;
		});
		
		$('.group_actions .delete_multiple').bind('click', function(){
			
			
			var users_count = $('.select_row:checked').size();
			var noun;
			if (users_count >= 10 && users_count <= 20) {
				noun = 'пользователей';
			}
			else {
				noun = users_count%10 == 1 ? 'пользователя' : 'пользователей';
			}
				
			var message = 'Подтвердите удаление '+users_count+' '+noun;
			
			var dlg = $('<div />').html(message).dialog({
				modal: true,
				resizable: false,
				buttons: {
					'Все верно, удалить': function(){
						$('input[name=action]').val('delete');				
						$('input[name=action]').parents('form').submit();
						$(this).dialog('close');
					},
					'Я передумал': function(){							
						$(this).dialog('close');
					}					
				}
			});
			
			return false;
		});		
		
		
		$('.check-related').click(function(){
			var master_entity = $(this).attr('data-master-entity');
			var dependant_entity = $(this).attr('data-dependant-entity');
			
			jQuery('input[name^=search_'+dependant_entity+']').removeAttr('checked');
			jQuery('input[name^=search_'+master_entity+']:checked').each(function(){
				var master_id = parseInt($(this).val());
				var dependant_ids = filter_relation_map[master_entity + '_' + dependant_entity][master_id] || {};
				jQuery.each(dependant_ids, function(idx, dependant_id){
					jQuery('input[name^=search_'+dependant_entity+'][value='+dependant_id+']').attr({checked: 'checked'});
				});
			});
			
			
			return false;
		});
		
		
		$('.check-all').click(function(){
			var master_entity = $(this).attr('data-master-entity');
			jQuery('input[name^=search_'+master_entity+']').attr({checked: 'checked'});
			return false;
		});
		
		$('.uncheck-all').click(function(){
			var master_entity = $(this).attr('data-master-entity');
			jQuery('input[name^=search_'+master_entity+']').removeAttr('checked');
			return false;
		});

		
	});