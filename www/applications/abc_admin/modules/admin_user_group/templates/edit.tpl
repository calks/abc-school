

        <div class="top_comment">
            {if $action == 'add'}
                Добавление группы
            {else}
                Редактирование группы
            {/if}
        </div>
        
        <form action="{$form_action}" method="POST" enctype="multipart/form-data">
        <table summary="" align="center">
	        <tr>
	        	<td align="right" class="buttom_form">
		            <input type="button" onclick="javascript:window.location.href='{$back_link}'" name="back" value="&lt;&lt;Назад к списку">
		            <input type="submit" name="save" value="Сохранить">
		            <input type="reset" name="reset" value="Сбросить">
		        </td>
		    </tr>
	        <tr>
	        	<td>
		            <table summary="" class="edit">
		            	<tr><th>Филиал *:</th><td>{$form->render('branch_id')}</td></tr>
			            <tr><th>Название *:</th><td>{$form->render('title')}</td></tr>
			            <tr><th>Стоимость в месяц:</th><td>{$form->render('month_price')}</td></tr>
			            <tr><th>Комментарий к стоимости:</th><td>{$form->render('month_price_comment')}</td></tr>
			            {*<tr><th>Число мест *:</th><td>{$form->render('capacity')}</td></tr>
			            <tr><th>Набор до *:</th><td>{$form->render('opened_before')}</td></tr>
			            <tr><th>Начало обучения *:</th><td>{$form->render('education_starts')}</td></tr>*}
			            <tr><th>Описание :</th><td>{$form->render('description')}</td></tr>
			            
			            <tr>
			            	<th>Расписание</th>
			            	<td>
			            		<table class="schedule">
			            			<tr class="weekday">
			            				{foreach from=$weekdays item=day_name}
			            					<td>{$day_name}</td>
			            				{/foreach}
			            			</tr>
			            			<tr class="entries">
			            				{foreach from=$weekdays key=day_number item=day_name}
			            					<td>
			            						{foreach from=$schedule.$day_number item=entry}
			            							<div class="entry">
			            								<a class="del" href="#" title="Удалить"></a>
			            								<input type="text" name="schedule[{$day_number}][]" value="{$entry->starts_at|date_format:'%H:%M'}" />
			            							</div>
			            						{/foreach}			            					
			            					</td>
			            				{/foreach}
			            			</tr>
			            			<tr class="actions">
			            				{foreach from=$weekdays key=day_number item=day_name}
			            					<td>
			            						<a href="#" id="add_entry_{$day_number}">добавить</a>
			            					</td>
			            				{/foreach}
			            			</tr>
			            		</table>			            	
			            	</td>
			            </tr>
			            
		            </table>	
	            	<br>
	            	* - обязательное поле
	        	</td>
	        </tr>
	        <tr>
	        	<td align="right" class="buttom_form">
		            <input type="button" onclick="javascript:window.location.href='{$back_link}'" name="back" value="&lt;&lt;Назад к списку">
		            <input type="submit" name="save" value="Сохранить">
		            <input type="reset" name="reset" value="Сбросить">
		            <input type="hidden" name="action" value="{$action}">		            
		            {$form->render('id')}
		        </td>
		    </tr>
        </table>
        </form>
        <br>
