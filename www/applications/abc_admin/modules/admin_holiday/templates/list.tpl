
	
    <div class="link_add">
    	<a href="{$add_link}">Добавить праздник</a>
    </div>
    
    <br clear="all">
    <br clear="all">
    
    <table class="list" id="hover" summary="">
	    <tr>	    	
	        <th>Id</th>
	        <th>Дата</th>
	        <th>Название</th>
	        <th>Показывать</th>
	        <th>Повторять</th>
	        <th>Редактировать</th>
	        <th>Удалить</th>
	    </tr>
	    {foreach key=key item=object from=$objects name=objectlist}		    
	    	<tr class="{cycle values="odd,even"}">
		        <td class="delete">
		        	{$object->id}
		        </td>
		        <td>
		        	{$object->date|date_format:"%d.%m.%Y"}
		        </td>
		        <td class="delete">
		        	{$object->title}
		        </td>
		        <td class="delete">
		        	{$object->visibility_str}
		        </td>
		        <td class="delete">
		        	{if $object->repeat_annually}Да{else}Нет{/if}
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
