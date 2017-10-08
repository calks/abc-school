

    <div class="link_add"><a href="{$add_link}">Добавить страницу</a> </div>
    <br clear="all">
    <table class="list" id="hover" summary="">
    <tr>
        <th>Название</th>
        <th>URL</th>
        <th>Выше</th>
        <th>Ниже</th>
        <th>Активен</th>
        <th>В меню</th>        
        <th>Редактировать</th>
        <th>Удалить</th>
    </tr>
    {assign var="tr" value="0"}
    {include file=$line_template_path objects=$objects}
    </table>
