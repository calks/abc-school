	{if $layout=='embeded'}
	
		{foreach from=$errors item=e}
			<div class="error hidden">{$e}</div>
		{/foreach}
	
		<form action="{$form_action}" method="POST" enctype="multipart/form-data">
	        <table summary="" align="center">
		        <tr>
		        	<td>
			            <table summary="" class="edit">
				            <tr>
				            	<th>Картинка <span class="num"></span> :</th>
				            	<td>{$form->render('image')}</td>
				            </tr>
				            <tr>
				            	<th>Комментарий <span class="num"></span> :</th>
				            	<td>{$form->render('comment')}</td>
				            </tr>				            
			            </table>	
		        	</td>
		        </tr>
	        </table>
	        
	        
	        <input type="submit" name="save" value="Сохранить" style="display: none">						            
			<input type="hidden" name="action" value="{$action}">
			<input type="hidden" name="layout" value="{$layout}">
			<input type="hidden" name="gallery_id" value="{$gallery_id}">
	        
        </form>	


	{else}
        <div class="top_comment">
			Добавление фото
        </div>
        
        <div class="error"></div>
        
        <form action="{$form_action}" method="POST" enctype="multipart/form-data">
        <table summary="" align="center">
	        <tr>
	        	<td align="right" class="buttom_form">
		            <input type="button" onclick="javascript:window.location.href='{$back_link}'" name="back" value="&lt;&lt;Назад к списку">
		            <input type="submit" name="save" value="Сохранить">		            
		            <input type="button" name="add-field" value="Добавить поле">
		        </td>
		    </tr>
	        <tr>
	        	<td>
		            <table summary="" class="edit">
		            	<tr><th>Галерея :</th><td>{$form->render('gallery_id')}</td></tr>
		            </table>		            	
	            	<br>
	            	* - обязательное поле
	        	</td>
	        </tr>
	        <tr>
	        	<td align="right" class="buttom_form">
		            <input type="button" onclick="javascript:window.location.href='{$back_link}'" name="back" value="&lt;&lt;Назад к списку">
		            <input type="submit" name="save" value="Сохранить">
		            <input type="button" name="add-field" value="Добавить поле">
					<input type="hidden" name="action" value="{$action}">
					<input type="hidden" name="layout" value="{$layout}">
		        </td>
		    </tr>
        </table>
        </form>
        <br>
        
        <script type="text/javascript">
			var add_photo_iframe_src = '{$add_photo_iframe_src}';
		</script>
        
	{/if}
