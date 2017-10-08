
	$(document).ready(function(){
		
		var period_select = $('select[name=payment_start_year_half]');
		
		$('.attendance').each(function(){
			
			var acontainer = $(this);
			var acontainer_id = acontainer.attr('id');
			var entity_name = acontainer.hasClass('payment') ? 'payment' : 'attendance';
		
			var table = acontainer.find('table.chart');
			
			
			function maskSelectedStudent() {
				var student_id = parseInt($('input[name=student_id]').val());
				if (!student_id) return false;
				
				var students_list = acontainer.find('.students');				
				students_list.find('li').each(function(){
					var id = parseInt($(this).find('.user_info').attr('href').toString().match(/\/(\d+)/)[1]);
					if (id != student_id) {
						$(this).addClass('hidden');
						table.find('input[value='+id+']:first').parents('tr').addClass('hidden');
					}
				});
				
			}
			
			function setMissedTwo() {
				var counter = 1;
				acontainer.find('.students li').each(function(){
					$(this).removeClass('missed_two');
					if (table.find('tr:nth-child('+counter+') .missed_two').size() > 0) $(this).addClass('missed_two');
					counter++;
				})
			}
			
			
			

			function setYearPeriodVisibility() {				
				if (period_select.size() == 0) return;				
				var chart = acontainer.find('.chart');
				if (chart.size() == 0) return;
				
				var first_half_cols = chart.find('#col_1, #col_2, #col_3, #col_4');
				var second_half_cols = chart.find('#col_5, #col_6, #col_7, #col_8, #col_9');
				
				first_half_cols.removeClass('hidden');
				second_half_cols.removeClass('hidden');
				
				if (period_select.val() == 'first') {
					second_half_cols.addClass('hidden');	
				}
				else if (period_select.val() == 'second') {
					first_half_cols.addClass('hidden');	
				}

			}

	
			
			function setScroll() {
				
				var container = acontainer.find('.chart_container');
				
				if (container.hasClass('mCustomScrollbar')) {
					container.mCustomScrollbar("destroy");	
				}
				
				container.mCustomScrollbar({
					horizontalScroll:true,
					scrollButtons:{
						enable: true
					},
					theme: 'dark-thick'
				});
				
				container.mCustomScrollbar("scrollTo",'right');
			}
			
			function getCells(col_number) {
				return table.find('tr td:nth-child('+col_number+')');
			}
			
			
			function startEditing(col_number) {			
				var cells = getCells(col_number);
				
				cells.find('input').removeClass('hidden');			
				cells.find('a.comment').removeClass('hidden');
				cells.find('.check').addClass('hidden');
				cells.find('.time, .save, .cancel, .check_all').removeClass('hidden');
				cells.find('.create').addClass('hidden');
				cells.find('.edit').addClass('hidden');
			}
			
			
			function cancelEditing(col_number) {
				var cells = getCells(col_number);
				
				cells.find('input').addClass('hidden');
				cells.find('.check').removeClass('hidden');
				cells.find('a.comment').addClass('hidden');
				cells.find('.time.tmp, .save, .cancel, .check_all').addClass('hidden');
				cells.find('.create').removeClass('hidden');
				cells.find('.edit').removeClass('hidden');
			}
			
			
			function applyChanges(col_number) {
				var cells = getCells(col_number);
				
				function doSave() {
					var data = {
						entry_id: cells.find('input[name=entry_id]').val(),
						entry_date: cells.find('input[name=entry_date]').val(),
						users: [],						
						from: $('input[name=from].datepicker').val(),
						to: $('input[name=to].datepicker').val(),
					};
					
					cells.find('input[name^=attendance]').each(function(){
						data['users'].push({
							id: $(this).val(),
							attendance: $(this).is(':checked') ? 1 : 0,
							comment: $(this).parents('td').find('textarea').val()
						});					
					});
					
					cells.find('input[name^=payed]').each(function(){
						data['users'].push({
							id: $(this).val(),
							payed: $(this).is(':checked') ? 1 : 0,
							comment: $(this).parents('td').find('textarea').val()
						});					
					});
					
					
					data.debtors_only = $('input[name=debtors_only]:checked').size() != 0 ? 1 : 0;

						
					_block(table);
					
					$.ajax({
						url: '/profile/save_' + entity_name,
						data: data,
						type: 'post',
						dataType: 'json',
						success: function(data) {
							_unblock();
							var error = typeof(data.error)!='undefined' ? data.error : '';
							if (error) overlay_message('error', error);
							
							var message = typeof(data.message)!='undefined' ? data.message : '';
							if (message) overlay_message('ok', message);
							
							var new_chart = typeof(data.chart)!='undefined' ? data.chart : '';
							if (new_chart) {
								table.html(new_chart);							
								setScroll();
								setMissedTwo();
								maskSelectedStudent();
								setYearPeriodVisibility();
							}
						}
						
					});
				}
				
				var is_empty = cells.find(':checked').size()==0 && cells.find('a.comment.has_one').size()==0;
				
				if (is_empty && entity_name=='attendance') {
					var save_dialog = $('<div />').html('<p>Ни один пользователь не отмечен как присутствовавший.</p><p>В таких случаях считается, что занятия не было и колонка в таблице выводиться не будет.</p>').appendTo('body').dialog({
						title: 'На занятии никого не было',
						modal: true,
						resizable: false,
						autoOpen: true,
						buttons: {
							'Сохранить': function(){
								save_dialog.dialog('close');		
								doSave();					
							},
							'Отмена': function(){
								save_dialog.dialog('close');						
							}
						},
						close: function(){
							save_dialog.remove();					
						}
					});		
				}
				else {
					doSave();
				}
				
			}
		
			
			
			function showDateDialog(col_number) {
				var date_dialog = $('<div />').addClass('attendance-entry-select').appendTo('body');
							
				$('<label />').html('Дата ').appendTo(date_dialog);
							
				var today = new Date();
				var dd = today.getDate().toString();
				if (dd.length == 1) dd = '0' + dd;
				var mm = today.getMonth()+1;
				mm = mm.toString();
				if (mm.length == 1) mm = '0' + mm;
				var yyyy = today.getFullYear();
				
				
				date_select = $('<input />').addClass('date').attr({
					type: 'text',
					name: 'entry_date'				
				}).val(dd + '.' + mm + '.' + yyyy).appendTo(date_dialog);
				
				$('<label />').html('Время ').appendTo(date_dialog);
				
				time_select = $('<select />').attr({
					name: 'entry_id'
				}).appendTo(date_dialog);
				
							
				date_select.change(function(){
					_block(date_dialog);
					$.ajax({
						url: '/profile/schedule_entries',
						type: 'post',
						dataType: 'json',
						data: {
							group_id: acontainer.find('[name=gid]').val(),
							entry_date: date_select.val()
						},
						success: function(data){
							_unblock();
							time_select.html('');
							$('<option />').attr({value: 0}).html('-- Выберите --').appendTo(time_select);
							$.each(data, function(value, caption){
								$('<option />').attr({value: value}).html(caption).appendTo(time_select);
							});
													
						}
					});
					
				});
				
				
				date_dialog.dialog({
					title: 'Создание записи в журнале посещаемости',
					modal: true,
					resizable: false,
					autoOpen: true,
					buttons: {
						'Создать': function(){
							var date = date_select.val();
							var entry_id = parseInt(time_select.val());
							if (!entry_id) {
								overlay_message('error', 'Не выбрано время занятия');
								return false;							
							}
							
							var time = time_select.find('option[value='+entry_id+']').html();
							var cells = getCells(col_number);
							cells.find('input[name=entry_date]').val(date);
							cells.find('input[name=entry_id]').val(entry_id);
							cells.find('span.time').html(date + ' <br/> ' + time);
					
							startEditing(col_number);
							date_dialog.dialog('close');
						},
						'Отмена': function(){
							date_dialog.dialog('close');						
						}
					},
					close: function(){
						date_dialog.remove();					
					},
					open: function(){
						date_select.datepicker({
							dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
							monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
							monthNamesShort: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сент', 'Окт', 'Ноя', 'Дек'],			
							firstDay: 1,
							dateFormat: 'dd.mm.yy',
							changeMonth: true,
							changeYear: true,
							yearRange: (yyyy-1) + ':' + yyyy
						});
						date_select.change();
					}				
					
				});	
				
				time_select.focus();
	
			}
			
			
			$('#'+acontainer_id+' .chart .create').live('click', function(){			
				var head_cell = $(this).parents('td:first');						
				var col_number = parseInt(head_cell.attr('id').toString().substr(4));
				getCells(col_number).find('input').removeAttr('checked');
				showDateDialog(col_number);
				return false;
			});
			
			$('#'+acontainer_id+' .chart .edit').live('click', function(){			
				var head_cell = $(this).parents('td:first');						
				var col_number = parseInt(head_cell.attr('id').toString().substr(4));
				startEditing(col_number);
							
				return false;
			});
	
			
			$('#'+acontainer_id+' .chart .cancel').live('click', function(){
				var head_cell = $(this).parents('td:first');						
				var col_number = parseInt(head_cell.attr('id').toString().substr(4));
				cancelEditing(col_number);
							
				return false;
			});
	
			$('#'+acontainer_id+' .chart .save').live('click', function(){
				var head_cell = $(this).parents('td:first');						
				var col_number = parseInt(head_cell.attr('id').toString().substr(4));
				
				applyChanges(col_number);
							
				return false;
			});
			
			
			$('#'+acontainer_id+' .chart a.comment').live('click', function(){
				
				var cell = $(this).parents('td:first');
				var comment_edit = cell.find('textarea');
				var link = $(this);
				
				var comment_dialog = $('<div />').appendTo('body');
				
				$('<label />').html('Комментарий ').appendTo(comment_dialog);
							
				dialog_comment_edit = $('<textarea />').val(comment_edit.val()).appendTo(comment_dialog).css({
					display: 'block',
					width: 260,
					height: 100
				});
				
				
				comment_dialog.dialog({
					title: 'Редактирование комментария',
					modal: true,
					resizable: false,
					autoOpen: true,
					buttons: {
						'Сохранить': function(){
							var comment_text = $.trim(dialog_comment_edit.val());
							comment_edit.val(comment_text);
							if (comment_text) {
								link.addClass('has_one');
								link.parents('td').find('input').removeAttr('checked');
							}
							else link.removeClass('has_one');
							comment_dialog.dialog('close');
						},
						'Отмена': function(){
							comment_dialog.dialog('close');						
						}
					},
					close: function(){
						comment_dialog.remove();					
					}	
				});	
							
				return false;
			});
	
			
			$('#'+acontainer_id+' .chart .check_all').live('click', function(){				
				var head_cell = $(this).parents('td:first');						
				var col_number = parseInt(head_cell.attr('id').toString().substr(4));
				
				var cells = getCells(col_number);
				
				var there_are_comments = cells.find('a.comment.has_one').size() != 0;
				
				if (there_are_comments) {
					if (entity_name == 'attendance') {
						delete_comment = '<p>Все комментарии о причине отсутствия пользователей будут удалены.</p>'; 
					}
					else {
						delete_comment = '<p>Все комментарии о причине отсутствия оплат будут удалены.</p>';
					}
					
					var delete_comment_dialog = $('<div />').html(delete_comment).appendTo('body').dialog({
						title: 'Введенные комментарии будут удалены',
						modal: true,
						resizable: false,
						autoOpen: true,
						buttons: {
							'Удалить комментарии': function(){
								cells.find('textarea').val('');
								cells.find('a.comment.has_one').removeClass('has_one');
								cells.find('input').not(':checked').attr({checked: 'checked'});
								delete_comment_dialog.dialog('close');		
								
							},
							'Отмена': function(){
								delete_comment_dialog.dialog('close');						
							}
						},
						close: function(){
							delete_comment_dialog.remove();					
						}
					});		
				}
				else {
					cells.find('input').not(':checked').attr({checked: 'checked'});
				}
				
				return false;
				
			});
			
			
			$('#'+acontainer_id+' .chart input').live('click', function(){
				if (!$(this).is(':checked')) return true;
				
				var cell = $(this).parents('td:first');
				
				var comment_link = cell.find('a.comment');
				if (!comment_link.hasClass('has_one')) return true;
				
				var delete_comment = '';
				if (entity_name == 'attendance') {
					delete_comment = '<p>Если вы отметите пользователя как посетившего занятие, введенный комментарий о причине его отсутствия будет удален.</p>'; 
				}
				else {
					delete_comment = '<p>Если вы отметите оплату, введенный комментарий о причине ее отсутствия будет удален.</p>';
				}
				
				
				var delete_comment_dialog = $('<div />').html(delete_comment).appendTo('body').dialog({
					title: 'Введенный комментарий будет удален',
					modal: true,
					resizable: false,
					autoOpen: true,
					buttons: {
						'Удалить комментарий': function(){
							cell.find('textarea').val('');
							comment_link.removeClass('has_one');
							cell.find('input').attr({checked: 'checked'});
							delete_comment_dialog.dialog('close');		
							
						},
						'Отмена': function(){
							delete_comment_dialog.dialog('close');						
						}
					},
					close: function(){
						delete_comment_dialog.remove();					
					}
				});	
				
				return false;
	
				
			});
			
			
			setScroll();
			setMissedTwo();
			maskSelectedStudent();
			setYearPeriodVisibility();
		});
		
	});