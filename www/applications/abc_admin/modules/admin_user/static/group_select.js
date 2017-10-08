

	$(document).ready(function(){
		
		var single_select = $('select[name=group_select]');
		
		single_select.change(function(){
			var group_id = $(this).val();
			
			$('.multiple input').removeAttr('checked');
			$('.multiple input[value='+group_id+']').attr({checked: 'checked'});
		});
		
		function setSingleSelect() {
			if($('.multiple input:checked').size()==0) {
				single_select.val(0);
			}
			else {
				single_select.val($('.multiple input:checked:first').val());
			}
		}
		
		
		$('.multiple input').click(function(){
			setSingleSelect();
		});
		
		
		$('select[name=role]').change(function(){
			if ($(this).val() == 'student') {				
				single_select.change();
				$('.multiple').addClass('hidden');
				$('.single').removeClass('hidden');
				$('.student_only').removeClass('hidden');
			}	
			else {
				$('.multiple').removeClass('hidden');
				$('.single').addClass('hidden');
				$('.student_only').addClass('hidden');
			}
			
			$('.for_student, .for_teacher').addClass('hidden');
			$('.for_' + $(this).val()).removeClass('hidden');	
		});
		
		setSingleSelect();
		$('select[name=role]').change();
		
	});

