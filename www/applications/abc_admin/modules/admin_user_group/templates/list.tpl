
	
    <div class="link_add">
    	<a href="{$add_link}">Добавить группу</a>
    </div>
    
    <br clear="all">
    <br clear="all">
    
    <table class="list" id="hover" summary="">
	    <tr>	    	
	        <th>Id</th>	        
	        <th>Название</th>
	        <th>Филиал</th>
	        {*<th>Набор до</th>
	        <th>Начало обучения</th>
	        <th>Число мест</th>*}
	        <th>Стоимость в месяц</th>
	        <th>Комментарий к стоимости</th>
	        <th>Пользователи</th>
	        <th>Редактировать</th>
	        <th>Удалить</th>
	    </tr>
	    {foreach key=key item=object from=$objects name=objectlist}		    
	    	<tr class="{cycle values="odd,even"}">
		        <td class="delete">
		        	{$object->id}
		        </td>
		        <td>
		        	{$object->title}
		        </td>
		        <td>
		        	{$object->branch_name}
		        </td>
		        {*<td class="delete">
		        	{$object->opened_before|date_format:"%d.%m.%Y"}
		        </td>
		        <td class="delete">
		        	{$object->education_starts|date_format:"%d.%m.%Y"}
		        </td>
		        <td class="delete">
		        	{$object->capacity}
		        </td>*}
		        <td class="delete">
		        	{$object->month_price_str}
		        </td>
		        <td>
		        	{$object->month_price_comment}
		        </td>

		        <td class="delete">
		        	{$object->user_count} (<a href="/admin/admin_user?group_id={$object->id}">перейти</a>)
		        </td>
		        <td class="delete">
		        	<a href="{$object->edit_link}">
		        		<img src="{$app_img_dir}/edit.gif" width="15" height="15" alt="Редактировать">
		        	</a>
		        </td>
		        <td class="delete">
		        	<a onclick="return confirm('Точно удалить?');" href="{$object->delete_link}">
			        	<img src="{$app_img_dir}/delete.gif" width="14" height="14" alt="Удалить">			        	
		        	</a>
		        </td>
		    </tr>
    	{/foreach}
    </table>
    
    {if $pagenav}<br><br>{$pagenav->Display()}{/if}
