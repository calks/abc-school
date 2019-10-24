

	$(document).ready(function(){
		
		function quizz_block() {
			if ($('.quizz_blocker').length != 0) return;
			
			$('<div />').addClass('quizz_blocker').css({
				height: $('.quizz_module .dynamic').height(),
				width: $('.quizz_module .dynamic').width()
			}).prependTo('.quizz_module .dynamic').animate({opacity: 0.8}, 400);
		}
		
		function quizz_unblock() {
			if ($('.quizz_blocker').length == 0) return;
			$('.quizz_blocker').animate({opacity: 0}, 400, function(){
				$('.quizz_blocker').remove();	
			});
		}
		
		function getAnswerSelected() {						
			answer_selected = $('.quizz_module form input[name=answer]:checked');
			if (answer_selected.length != 0) return answer_selected.attr('value');			
			return 0;
		}
		
		$('.quizz_module form .submit').live('click', function(event){
			event.preventDefault();
			
			var form = $(this).parents('form');
			var task = form.find('[name=task]').val();
			
			var data = {
				task: task
			}
			
			switch (task) {
				case 'answer':					
					answer_selected = getAnswerSelected();
					if (!answer_selected) {
						form.find('.no_answer').html('Нужно выбрать вариант ответа');
						return;
					}
					else {
						data.answer_id = answer_selected;
					}
					break;
					
				case 'user_info':
					data.name = form.find('[name=name]').val();
					data.age = form.find('[name=age]').val();
					data.school = form.find('[name=school]').val();
					data.grade = form.find('[name=grade]').val();
					data.phone = form.find('[name=phone]').val();
					break;
					
				default: 
					return false;
			
			}
					
					
			quizz_block();
			$.ajax({
				url: '/quizz/ajax',					
				data: data,
				type: 'POST',
				success: function(data) {
					quizz_unblock();
					$('.quizz_module .dynamic').find(':not(.quizz_blocker)').remove();
					$('.quizz_module .dynamic').append(data);
				}					
			});
			
		});
		
		
		$('.quizz_module .option p').live('click', function(){
			if ($(this).parents('.option').find('input:checked').length != 0) return;
			$(this).parents('form').find('input:checked').removeAttr('checked');
			$(this).parents('.option').find('input').attr({checked: 'checked'});
		});	
		
		$('.quizz_module .view_detailed_result').live('click', function(){
			detailed_result = $('.quizz_module .detailed_result'); 
			
			if(detailed_result.hasClass('hidden')) {
				detailed_result.show().removeClass('hidden');
			}
			else {
				detailed_result.hide().addClass('hidden');
			}
		});		
		
		
	});
	
	
	
	
	
	
	
	
	
	
	