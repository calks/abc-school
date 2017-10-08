

    <div class="link_add">
    	<a href="{$add_link}">Добавить пользователя</a>
    </div>
    
    <br clear="all">
    
    <table class="list" id="hover" summary="">
	    <tr>
	        <th>Id</th>
	        <th>Имя</th>
	        <th>Email</th>
	        <th>Активен</th>	        
	        <th>Редактировать</th>
	        <th>Удалить</th>
	    </tr>
	    {foreach key=key item=object from=$objects name=objectlist}
		    {if $object->active}
		    	<tr class="{cycle values="odd,even"}">
		    {else}
		    	<tr class="not_approved">
		    {/if}
		        <td class="delete">
		        	{$object->id}
		        </td>
		        <td>
		        	{$object->firstname} {$object->lastname}
		        </td>
		        <td class="delete">
		        	<a href="mailto:{$object->email}">{$object->email}</a>
		        </td>
		        <td class="delete">
		        	{if $object->active == 1}да{else}нет{/if}
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
