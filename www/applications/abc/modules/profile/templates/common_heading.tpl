
		{if $group_select}			
			<form class="mini profile-filter" action="" method="get">
				{if $user->role!='student'}
					<div class="full_width">
						<label>Филиал</label>
						{$branch_select->getAsHtml()} 
					</div>
					{if $teacher_select}
						<div class="full_width">	
							<label>Преподаватель</label>
							{$teacher_select->getAsHtml()}
						</div>
					{/if}		
					
					<div class="full_width">	
						<label>Группа</label>
						{$group_select->getAsHtml()}
					</div>	
				{/if}
				{if $year_select}
					<div class="full_width">
						<label>Год</label>
						{$year_select->getAsHtml()}
						{if $user->role!='student' && $user->role!='teacher'}{$year_period_select->getAsHtml()}{/if}
					</div>	
					{if $user->role!='student'}
						<div class="full_width">
							<label></label>
							<input type="checkbox" name="debtors_only" value="1" {if $smarty.get.debtors_only}checked="checked"{/if}>
							&nbsp;&nbsp;Только должники
						</div>	
					{/if}
				{elseif $from_select}
					<div class="full_width">
						<label>Дата с</label>
						{$from_select->getAsHtml()}
						<label class="narrow">по</label>
						{$to_select->getAsHtml()}
					</div>	
				{/if}	
				
				{if $user->role!='student' && $user->role!='teacher'}
					<div class="full_width">
						<label>Студент</label>
						<input type="text" name="student_name" value="{$smarty.get.student_name}" size="80">
						<input type="hidden" name="student_id" value="{$smarty.get.student_id}">
						<a class="clear_student" href="#">очистить</a>
					</div>
				{/if}
			</form>
			
						
			{literal}
				<script type="text/javascript">

					function submitFilter(changed_input) {
						var el = $(changed_input);
						var frm = el.parents('form')  

						if(el.is('[name=branch]')) {
							frm.find('[name=group]').val('');
							frm.find('[name=teacher]').val('');
						}
						
						if(el.is('[name=teacher]')) {
							frm.find('[name=group]').val('');
						}

						if(el.is('[name=branch]') || el.is('[name=group]') || el.is('[name=teacher]')) {
							frm.find('[name=student_name]').val('');
							frm.find('[name=student_id]').val('');
						}


						
						frm.submit();			

					}


					$('select[name=branch], select[name=group], select[name=teacher], select[name=payment_start_year], select[name=payment_start_year_half], input[name=from], input[name=to]').bind('change', function(){
						submitFilter(this);
					});

					$('input[name=debtors_only]').bind('click', function(){
						submitFilter(this);
					});

					
					{/literal}{if $user->role!='student'}{literal}

					
						$("input[name=student_name]").autocomplete({
							source: function(request, response) {							
								$.ajax({
							 		url: "/profile/student_lookup",
							 		dataType: "json",
							 		data: {
							 			name: request.term
							 		},
							 		success: function(data) {
								 		$.each(data, function(idx, item){
	
									 	});
							 			response($.map(data, function(item) {
								 			console.log(item);
								 			return {
								 				label: item.name,
								 				value: item.name,
								 				user_id: item.id,
								 				group_id: item.group_id
								 			}
							 			}));
							 		}
								 });
							 },
							 minLength: 2,
							 select: function(event, ui) {
								 var id_input = $('input[name=student_id]');
								 $('input[name=student_name]').val(ui.item.label);  
								 id_input.val(ui.item.user_id);
								 id_input.parents('form').submit();
							 }
						});
	
	
						$(".clear_student").click(function(){
							$('input[name=student_name]').val('');
							$('input[name=student_id]').val('').parents('form').submit();
							return false;
						});
					{/literal}{/if}{literal}
										
				</script>
			{/literal}
			
			{if $group_title && $user->role!='student'}
				<br><h2>Группа {$group_title}</h2>
			{/if}

			{if $group_month_price}
				<p class="month_price">
					<span class="price"><b>Стоимость обучения: {$group_month_price}</b>{if $group_month_price_comment}<span class="asterisk">*</span>{/if}</span>
					{if $group_month_price_comment}
						<span class="comment">*{$group_month_price_comment}</span>
					{/if}
				</p>
			{/if}


			{$group_schedule_html}
							
			{if $holidays}
				<div class="holidays">
					{$holidays}
				</div>
			{/if}
			
			{if $teacher_names && $user->role!='teacher'}				
				<p>{$teacher_names}</p>
			{/if}	
			
			
		{/if}	
