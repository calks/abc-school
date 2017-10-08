
    {if !$level}
        {assign var=level value=0}
    {/if}

    {if $level==0}
        <h1>Sitemap</h1>
    {/if}

        <ul {if $level>0}class="submenu"{/if}>
            {foreach key=key item=item from=$sitemap_data name=sitemap}
                {if $item->link != '/sitemap'}
                    <li>
                        <a href="{$item->link}" {if $item->open_new_window == 1}target="_blank"{/if} {if $item->open_new_window == 1}target="_blank"{/if}>
                            {$item->menu_title|strip_tags:false|escape:"htmlall"}
                        </a>
                        {if $item->children}
                            {include file=$template_path level=$level+1 sitemap_data=$item->children}
                        {/if}
                    </li>
                {/if}
            {/foreach}
        </ul>

