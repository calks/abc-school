
        <div class="top_comment">
            {if $action == 'add'}
                Добавление страницы
            {else}
                Редактирование страницы
            {/if}
        </div>
        <form action="{$form_action}" method="POST" enctype="multipart/form-data">
        <table summary="" align="center">
        <tr><td align="right" class="buttom_form">
            <input type="button" onclick="javascript:window.location.href='{$back_link}'" name="back" value="&lt;&lt;Назад к списку">
            <input type="submit" name="save" value="Сохранить">
            <input type="reset" name="reset" value="Сбросить">
        </td></tr>
        <tr><td>
            <table summary="" class="edit">
            <tr><th>Родитель :</th><td>{$form->render('parent_id')}</td></tr>
            <tr>
            	<th>URL * :</th>
            	<td>
            		{if $object->protected}
            			{$object->url}
            		{else}
            			{$form->render('url')}
            		{/if}	
            	
            	</td>
            </tr>
            <tr><th>Существующая страница :</th><td>{$form->render('open_link')}</td></tr>
            <tr><th>Активен? :</th><td>{$form->render('active')}</td></tr>
            <tr><th>Доступ :</th><td>{$form->render('access')}</td></tr>
            <tr><th>Тип :</th><td>{$form->render('category')}</td></tr>
            <tr><th>Название в меню *:</th><td>{$form->render('title')}</td></tr>
            <tr><th>Заголовок :</th><td>{$form->render('meta_title')}</td></tr>            
            <tr><th>Контент:</th><td width="800">{$form->render('content')}</td></tr>
            <tr><th>Отображать в меню :</th><td>{$form->render('menu')}</td></tr>
           

            <tr><th>Открывать в новом окне :</th><td>{$form->render('open_new_window')}</td></tr>            
            <tr><th>Meta Descriptions :</th><td>{$form->render('meta_desc')}</td></tr>
            <tr><th>Meta Keywords :</th><td>{$form->render('meta_key')}</td></tr>
            </table>

            <br>
            * - required field
        </td></tr>
        <tr><td align="right" class="buttom_form">
            <input type="button" onclick="javascript:window.location.href='{$back_link}'" name="back" value="&lt;&lt;Назад к списку">
            <input type="submit" name="save" value="Сохранить">
            <input type="reset" name="reset" value="Сбросить">
            <input type="hidden" name="action" value="{$action}">
            {$form->render('id')}
            {$form->render('language_id')}            
        </td></tr>
        </table>
        </form>
        <br>
