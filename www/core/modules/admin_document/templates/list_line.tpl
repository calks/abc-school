{foreach key=key item=object from=$objects name=objectlist}
{assign var="tr" value=$tr+1}
{if $object->parent_id==""}{assign var='level' value=0}{/if}
    {if $level>10}{assign var='level' value=10}{/if}
    <tr class="{if $tr % 2 == 0}even{else}odd{/if}">
        <td style="padding-left:{$level*20+5}px"><img src="{if $object->category==2}{$app_img_dir}/page.gif{else}{$app_img_dir}/section.gif{/if}" alt="">{$object->title}</td>
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
        <td>{if $object->open_link != ''}[{$object->link|strip_tags:false|escape:"htmlall"}]{else}{$object->link|strip_tags:false|escape:"htmlall"}{/if}</td>
        <td class="up" style="padding-left:{$level*20+5}px">{if $up}<a href="{$object->moveup_link}"><IMG SRC="{$app_img_dir}/up.gif" width="9" height="11" ALT="Move Up"></a>{/if}</td>
        <td class="up" style="padding-left:{$level*20+5}px" >{if $down}<a href="{$object->movedown_link}"><IMG SRC="{$app_img_dir}/down.gif" width="9" height="11" ALT="Move Down"></a>{/if}</td>
        <td class="delete">{if $object->active}да{else}нет{/if}</td>        
        <td class="delete">{$object->menu_str}</td>        
        <td class="delete"><a href="{$object->edit_link}"><img src="{$app_img_dir}/edit.gif" width="15" height="15" alt="Edit"></a></td>
        <td class="delete">{if !$object->protected}<a onclick="return confirm('Точно удалить?');" href="{$object->delete_link}"><img src="{$app_img_dir}/delete.gif" width="14" height="14" alt=""></a>{/if}</td>
    </tr>
    {if $object->children}
        {assign var='level' value=$level+1}
        {include file=$line_template_path objects=$object->children}
        {assign var='level' value=$level-1}
    {/if}
{/foreach}
