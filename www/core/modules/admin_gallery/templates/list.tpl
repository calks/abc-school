

    <div class="link_add">
    	<a href="{$add_link}">Добавить раздел</a>
    </div>
    
    <br clear="all">
    
    <table class="list" id="hover" summary="">
	    <tr>	        
	        <th>Название</th>
	        <th>Фото</th>        
	        <th>Активен</th>
	        <th>Выше</th>
	        <th>Ниже</th>
	        <th>Редактировать</th>
	        <th>Удалить</th>
	    </tr>
    
		{foreach key=key item=object from=$objects name=objectlist}
	        {if $smarty.foreach.objectlist.first}
	            {assign var='up' value="0"}
	        {else}
	            {assign var='up' value="1"}
	        {/if}
	        {if $smarty.foreach.objectlist.last}
	            {assign var='down' value="0"}
	        {else}
	            {assign var='down' value="1"}
	        {/if}
		    <tr class="{cycle values='odd,even'}">
	        	<td>
	        		{$object->name}
	        	</td>
	        	<td class="delete">
	        		{$object->photos_count} (<a href="/admin/admin_gallery_photo?search_gallery={$object->id}">смотреть</a>)
	        	</td>
		        <td class="delete">
		        	{if $object->active}да{else}нет{/if}
		        </td>
		        <td class="up" style="padding-left:{$level*20+5}px">{if $up}<a href="{$object->moveup_link}"><IMG SRC="{$app_img_dir}/up.gif" width="9" height="11" ALT="Move Up"></a>{/if}</td>
		        <td class="up" style="padding-left:{$level*20+5}px" >{if $down}<a href="{$object->movedown_link}"><IMG SRC="{$app_img_dir}/down.gif" width="9" height="11" ALT="Move Down"></a>{/if}</td>
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
