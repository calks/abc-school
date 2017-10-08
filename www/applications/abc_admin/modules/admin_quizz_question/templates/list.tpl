

	<h3>
		Вопросы к тесту "{$quizz->name}"
		| <a href="/admin/admin_quizz">назад к тестам</a>	
	</h3>

    <div class="link_add">
    	<a href="{$add_link}">Добавить вопрос</a>
    </div>
    
    <br clear="all">
    
    <table class="list" id="hover" summary="">
	    <tr>	        
	        <th>Вопрос</th>
	        <th>Ответы</th>
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
		    		{$object->content|strip_tags:false|truncate:200}
		    	</td>
	        	<td class="delete">
	        		{$object->answers_count} (<a href="/admin/admin_quizz_question_answer?question={$object->id}">смотреть</a>)
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
