
	
	<script type="text/javascript">
		var filter_relation_map = {$relation_map_json};
	</script>
	
	
    <div class="link_add">
    	<a href="{$add_link}">Добавить пользователя</a>
    	{*<br>
    	<a href="{$csv_link}">Выгрузить в CSV</a>*}
    </div>
    
    <br clear="all">
	    <form action="/admin/admin_user" method="POST">
		    <table class="filter_table">
			    <tr>
			        <th>Роль</th>        
			        <td>{$filter->render("search_role")}</td>			        
			    </tr>
			    <tr>
			        <th>Филиал</th>        
			        <td>
			        	{$filter->render("search_branch")}
			        	<div class="actions">
			        		<span>Выбрать:</span>
			        		<a class="check-all" data-master-entity="branch" href="#">всех</a>
			        		<a class="uncheck-all" data-master-entity="branch" href="#">ни одного</a>
				        </div>	

			        	<div class="actions">
			        		<span>Для выбранных:</span>
				        	<a class="check-related" data-master-entity="branch" data-dependant-entity="teacher" href="#">отметить преподавателей</a>
				        	<a class="check-related" data-master-entity="branch" data-dependant-entity="group" href="#">отметить группы</a>
				        </div>	
			        </td>			        
			    </tr>
			    <tr>
			        <th>Преподаватель</th>        
			        <td>
			        	{$filter->render("search_teacher")}
			        	<div class="actions">
			        		<span>Выбрать:</span>
			        		<a class="check-all" data-master-entity="teacher" href="#">всех</a>
			        		<a class="uncheck-all" data-master-entity="teacher" href="#">ни одного</a>
				        </div>	

			        	<div class="actions">
			        		<span>Для выбранных:</span>				        	
				        	<a class="check-related" data-master-entity="teacher" data-dependant-entity="group" href="#">отметить группы</a>
				        </div>	
			        </td>			        
			    </tr>
			    
			    <tr>
			        <th>Группа</th>        
			        <td>
			        	{$filter->render("search_group")}
			        	<div class="actions">
			        		<span>Выбрать:</span>
			        		<a class="check-all" data-master-entity="group" href="#">все</a>
			        		<a class="uncheck-all" data-master-entity="group" href="#">ни одной</a>
				        </div>	

			        </td>			        
			    </tr>
			    <tr>
			        <th>ФИО/Email</th>        
			        <td>{$filter->render("search_keyword")}</td>
			    </tr>
			    <tr>
			        <th>Показывать</th>        
			        <td>{$filter->render("search_active")}</td>
			    </tr>
			    <tr>
			        <th>Должники</th>        
			        <td>{$filter->render("search_debtors")}</td>
			    </tr>
			    <tr>
			        <th>Показывать по</th>        
			        <td>{$filter->render("search_limit")}</td>
			    </tr>

			    <tr>
			        <th></th>
			        <td align="left" valign="bottom" class="buttom_form"><input type="submit" value="Показать"></td>
			    </tr>
			    
			    {$filter->render("search_order_field")}
			    {$filter->render("search_order_direction")}


		    </table>
	    </form>
	<br clear="all">
	
	<div class=group_actions>
		<h2>Для выбранных:</h2>
		<ul>
			{*<li>
				<a href="#" class="send_message">отправить сообщение</a>
			</li>*}
			<li>
				<a href="#" class="assign_group">назначить группу</a>
				{$group_actions_form->render('group_id')}
			</li>
			<li>
				<a href="#" class="delete_multiple">удалить</a>				
			</li>
			

		</ul>
		
	</div>
	
	
	<div class="object_count">{$count_str}</div>
	
	
    <form action="/admin/admin_user" method="POST" enctype="multipart/form-data">
    	<input type="hidden" name="action" value="">
    	<input type="hidden" name="new_group_id" value="">
    
	    <table class="list" id="hover" summary="">
		    <tr>
		    	<th><input id="select_all_rows" type="checkbox" name="select_all_rows"></th>
		        <th>{$filter->sortLink('Id', 'user.id', '/admin/admin_user', $url_addition)}</th>
		        <th>{$filter->sortLink('Роль', 'user.role', '/admin/admin_user', $url_addition)}</th>		        
		        <th>Группа</th>		        
		        <th>{$filter->sortLink('Имя', 'user_name', '/admin/admin_user', $url_addition)}</th>		        
		        <th>{$filter->sortLink('Email', 'user.email', '/admin/admin_user', $url_addition)}</th>		        
		        <th>{$filter->sortLink('Активен', 'user.active', '/admin/admin_user', $url_addition)}</th>	        
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
			        	<input type="checkbox" class="select_row" name="ids[]" value="{$object->id}">
			        	<input type="hidden" name="role" value="{$object->role}">
			        	{foreach item=group_id from=$object->group_id}
			        		<input type="hidden" class="group_id" name="group_id[]" value="{$group_id}">
			        	{/foreach}
			        </td>
			        <td class="delete">
			        	{$object->id}
			        </td>
			        <td class="delete">
			        	{$object->role_str}
			        </td>
			        <td>
			        	{$object->group_title|default:'не назначена'}
			        </td>
			        <td>
			        	{$object->lastname} {$object->firstname}
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
	</form> 
    
    {if $pagenav}<br><br>{$pagenav->Display()}{/if}
