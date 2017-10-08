

        <div class="top_comment">
            {if $action == 'add'}
                Добавление пользователя
            {else}
                Редактирование пользователя
            {/if}
        </div>
        
        <form action="{$form_action}" method="POST" enctype="multipart/form-data">
        <table summary="" align="center">
	        <tr>
	        	<td align="right" class="buttom_form">
		            <input type="button" onclick="javascript:window.location.href='{$back_link}'" name="back" value="&lt;&lt;Назад к списку">
		            <input type="submit" name="save" value="Сохранить">
		            <input type="reset" name="reset" value="Сбросить">
		        </td>
		    </tr>
	        <tr>
	        	<td>
		            <table summary="" class="edit">
			            <tr><th>Активен? :</th><td>{$form->render('active')}</td></tr>
			            <tr><th>Роль :</th><td>{$form->render('role')}</td></tr>
			            <tr><th>Имя *:</th><td>{$form->render('firstname')}</td></tr>
			            <tr><th>Фамилия *:</th><td>{$form->render('lastname')}</td></tr>			            
			            <tr><th>Email *:</th><td>{$form->render('email')}</td></tr>
			            <tr><th>Логин *:</th><td>{$form->render('login')}</td></tr>
			            <tr><th>Пароль *:</th><td>{$form->render('pass')}</td></tr>
			            <tr class="for_student"><th>ФИО родителей :</th><td>{$form->render('parents')}</td></tr>
			            <tr class="for_student"><th>Домашний телефон :</th><td>{$form->render('phone')}</td></tr>
			            <tr class="for_student for_teacher"><th>Мобильный телефон :</th><td>{$form->render('cell_phone')}</td></tr>
			            <tr class="for_student for_teacher"><th>Дополнительная информация :</th><td>{$form->render('info')}</td></tr>

			            <tr>
			            	<th>Группа :</th>
			            	<td>
			            		<div class="single hidden">{$form->render('group_select')}</div>
			            		<div class="multiple hidden">{$form->render('group_id')}</div>			            	
			            	</td>
			            </tr>
			            
			            
		            </table>	
	            	<br>
	            	* - обязательное поле
	        	</td>
	        </tr>
	        <tr>
	        	<td align="right" class="buttom_form">
		            <input type="button" onclick="javascript:window.location.href='{$back_link}'" name="back" value="&lt;&lt;Назад к списку">
		            <input type="submit" name="save" value="Сохранить">
		            <input type="reset" name="reset" value="Сбросить">
		            <input type="hidden" name="action" value="{$action}">		            
		            {$form->render('id')}
		        </td>
		    </tr>
        </table>
        </form>
        <br>
