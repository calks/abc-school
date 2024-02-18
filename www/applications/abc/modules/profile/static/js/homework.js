
	$(document).ready(function(){
		
		var table = $('.attendance table.chart');
		
		$('select[name=group],select[name=branch],select[name=teacher]').change(function(){
			
			var el = $(this);
			var frm = el.parents('form')  

			if(el.is('[name=branch]')) {
				frm.find('[name=group]').val('');
				frm.find('[name=teacher]').val('');
			}
			
			if(el.is('[name=teacher]')) {
				frm.find('[name=group]').val('');
			}


			frm.submit();			
			
			//$(this).parents('form').submit();			
		});
		
		function setMissedTwo() {
			var counter = 1;
			$('.attendance .students li').each(function(){
				$(this).removeClass('missed_two');
				if (table.find('tr:nth-child('+counter+') .missed_two').size() > 0) $(this).addClass('missed_two');
				counter++;
			})
		}

		
		
		function getCells(col_number) {
			return table.find('tr td:nth-child('+col_number+')');
		}
		
		
		function startEditing(row) {						
			row.find('textarea').removeClass('hidden');
			row.find('.task').addClass('hidden');
			row.find('.time, .save, .cancel').removeClass('hidden');
			row.find('.create').addClass('hidden');
			row.find('.edit').addClass('hidden');
		}
		
		
		function cancelEditing(row) {
			row.find('textarea').addClass('hidden');
			row.find('.task').removeClass('hidden');
			
			row.find('.time.tmp, .save, .cancel').addClass('hidden');
			row.find('.create').removeClass('hidden');
			row.find('.edit').removeClass('hidden');
		}
		
		
		function applyChanges(row) {
						
			function doSave() {
				var data = {
					entry_id: row.find('input[name=entry_id]').val(),
					entry_date: row.find('input[name=entry_date]').val(),
					task: row.find('textarea').val()						
				};
				
				_block(table);
				
				$.ajax({
					url: '/profile/save_homework',
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
						}
					}
					
				});

			}
			
			
			if ($.trim(row.find('textarea').val().toString()) == '') {
				var save_dialog = $('<div />').html('<p>Вы не ввели текст домашнего задания.</p><p>В таких случаях считается, что ничего не задано и эта запись в таблице выводиться не будет.</p>').appendTo('body').dialog({
					title: 'Пустое домашнее задание',
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
	
		
		
		function showDateDialog(row) {
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
						group_id: $('select[name=group]').val(),
						entry_date: date_select.val(),
						object: 'homework'
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
			
			var active_days = jQuery('[name=schedule_weekdays]').val().split('|');
						
			
			date_dialog.dialog({
				title: 'Добавление домашнего задания',
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
						
						row.find('input[name=entry_date]').val(date);
						row.find('input[name=entry_id]').val(entry_id);
						row.find('span.time').html(date + ' ' + time);
				
						startEditing(row);
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
						yearRange: (today.getMonth() < 9 ? yyyy-1 : yyyy) + ':' + (today.getMonth() >= 9 ? yyyy+1 : yyyy),
						beforeShowDay: function(date) {
							var weekday_number = date.getDay();
							if (weekday_number == 0) {
								weekday_number = 7;
							}
							
							return [active_days.indexOf(weekday_number.toString()) != -1, '', ''];
						}	
					});
					date_select.change();
				}				
				
			});	
			
			time_select.focus();

		}
		
		
		$('.attendance .chart .create').live('click', function(){			
			var row = $(this).parents('tr:first');						
			showDateDialog(row);
			return false;
		});
		
		$('.attendance .chart .edit').live('click', function(){			
			var row = $(this).parents('tr:first');
			startEditing(row);
						
			return false;
		});

		
		$('.attendance .chart .cancel').live('click', function(){
			var row = $(this).parents('tr:first');
			cancelEditing(row);
						
			return false;
		});

		$('.attendance .chart .save').live('click', function(){
			var row = $(this).parents('tr:first');
			applyChanges(row);
						
			return false;
		});
		
		
	});