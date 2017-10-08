

    <div class="link_add">
    	<a href="{$add_link}">Добавить запись</a>
    </div>
    
    <br clear="all">
    
    <table class="list" id="hover" summary="">
	    <tr>
	        <th>Добавлен</th>
	        <th>Автор</th>
	        <th>Вопрос</th>
	        <th>Активна</th>
	        <th>Редактировать</th>
	        <th>Удалить</th>
	    </tr>
    
		{foreach key=key item=object from=$objects name=objectlist}
		    {if $object->author_notified}
		    	<tr class="{cycle values='odd,even'}">
		    {else}
		    	<tr class="not_approved">
		    {/if}
		    	<td class="delete">
		    		{$object->created|date_format:"%d.%m.%Y %H:%M:%S"}
		    	</td>
	        	<td>
	        		<a href="mailto:{$object->author_email}">
	        			{$object->author_name}
	        		</a>	
	        	</td>
	        	<td>
	        		{$object->question|strip_tags:false|escape:"html_all"|truncate:200}
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
