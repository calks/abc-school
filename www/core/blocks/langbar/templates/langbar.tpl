

    {if $page->lang_versions}
        <div class="lang_bar">
            {foreach item=item from=$page->lang_versions}
                {if $item==$smarty.const.LANGUAGES_ENGLISH}
                    <a href="{$smarty.const.SITE_URL}/{$page->url_name}">English</a>
                {elseif $item==$smarty.const.LANGUAGES_ESPANOL}
                    <a href="{$smarty.const.SITE_URL_ESPANOL}/{$page->url_name}">Español</a>
                {/if}
            {/foreach}
        </div>
    {/if}
