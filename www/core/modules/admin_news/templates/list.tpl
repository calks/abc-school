

    <div class="link_add">
    	<a href="{$add_link}">Добавить новость</a>
    </div>
    
    <br clear="all">
    
    <table class="list" id="hover" summary="">
	    <tr>
	        <th>Дата</th>
	        <th>Название</th>        
	        <th>Активна</th>
	        <th>Редактировать</th>
	        <th>Удалить</th>
	    </tr>
    
		{foreach key=key item=object from=$objects name=objectlist}
		    <tr class="{cycle values='odd,even'}">
		    	<td class="delete">
		    		{$object->date|date_format:"%d.%m.%Y"}
		    	</td>
	        	<td>
	        		{$object->title}
	        	</td>	  
		        <td class="delete">
		        	{if $object->active}да{else}нет{/if}
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
