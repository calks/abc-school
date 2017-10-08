

    <div class="link_add">
    	<a href="{$add_link}">Добавить фото</a>
    </div>
    
    <br clear="all">
    <form action="/admin/admin_gallery_photo" method="POST">
    <table width="100%">
    <tr>
        <td align="left" width="60">галерея</td>        
        <td align="left" width="1%">{$filter->render("search_gallery")}</td>
        <td align="left" valign="bottom" class="buttom_form"><input type="submit" value="Показать"></td>
    </tr>
    </table>
    </form>
    
    <br clear="all">
    
    <table class="list" id="hover" summary="">
	    <tr>	        
	        <th>Картинка</th>
	        <th>Галерея</th>
	        <th>Комментарий</th>
	        {if $allow_sorting}
		        <th>Выше</th>
		        <th>Ниже</th>	        
	        {/if}
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
	        	<td class="delete">
	        		<img src="{$smarty.const.PHOTOS_URL}/gallery_photo/{$object->id}/tiny/{$object->image}">	        		
	        	</td>
		        <td>
		        	{if $object->gallery_name}{$object->gallery_name}{else}Не выбрана{/if}
		        </td>	  
		        <td>
		        	{$object->comment}
		        </td>
		        {if $allow_sorting}
			        <td class="up" style="padding-left:{$level*20+5}px">{if $up}<a href="{$object->moveup_link}"><IMG SRC="{$app_img_dir}/up.gif" width="9" height="11" ALT="Move Up"></a>{/if}</td>
			        <td class="up" style="padding-left:{$level*20+5}px" >{if $down}<a href="{$object->movedown_link}"><IMG SRC="{$app_img_dir}/down.gif" width="9" height="11" ALT="Move Down"></a>{/if}</td>		        
		        {/if}
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
