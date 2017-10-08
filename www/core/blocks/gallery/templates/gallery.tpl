{if $photos}

    {if !$photo_size}
        {assign var=photo_size value='super'}
    {/if}
    {if !$panel_width}
        {assign var=panel_width value=430}
    {/if}

    <div class="clearBoth"></div>

    <div id="gallery_container">
        <span class="loader">Please wait while the slide-show <br>is downloading... </span>

        <ul id="gallery">
            {foreach key=key item=item from=$photos name=photo_list}
                {assign var=big_path value=$smarty.const.UPLOAD_PHOTOS|cat:$object_name|cat:'/'|cat:$object_id|cat:'/big/'|cat:$item->filename}
                {assign var=super_path value=$smarty.const.UPLOAD_PHOTOS|cat:$object_name|cat:'/'|cat:$object_id|cat:'/super/'|cat:$item->filename}

                {if $super_path|@is_file}
                    <li>
                        <img src="{$smarty.const.PHOTOS_URL}{$object_name}/{$object_id}/super/{$item->filename}" alt="{$item->alt}" title="{$item->alt}">
                        {if $item->comments}
                            <div class="panel-overlay"><div>{$item->comments}</div></div>
                        {/if}
                    </li>
                {elseif $big_path|@is_file}
                    <li>
                        <img src="{$smarty.const.PHOTOS_URL}{$object_name}/{$object_id}/big/{$item->filename}" alt="{$item->alt}" title="{$item->alt}">
                        {if $item->comments}
                            <div class="panel-overlay"><div>{$item->comments}</div></div>
                        {/if}
                    </li>
                {/if}
            {/foreach}
        </ul>

        <div id="map_link">
            <a href="?gallery=slideshow" onclick="return false;" id="title_link_slideshow">View Slideshow</a>
        </div>
    </div>

    <script type="text/javascript">
        var panel_width={$panel_width};
        {if $object_name=='realestate'}
            var show_filmstrip = false;
            var show_map_link = false;
            {assign var=photo_popup_href value="javascript: popup('photo_"|cat:$popup_name|cat:"', '"|cat:$popup_url|cat:"', 740, 600, options);"}
            var photo_popup_href = "{$photo_popup_href}";
        {else}
            var show_filmstrip = true;
            var show_map_link = true;
            var photo_popup_href = "";
        {/if}
    </script>

    <script type="text/javascript">
    {literal}
            jQuery(document).ready(function(){

                var panel_height = Math.round(panel_width*0.75);
                var offset = Math.round((panel_height-20)/2);
                var bg_pos = offset+40;

                jQuery('#gallery_container').css({width: panel_width, 'background-position': 'center ' + bg_pos + 'px'});
                jQuery('#gallery_container .loader').css({width: panel_width, margin: offset + 'px 0'});
                jQuery('#gallery_container').show();

                jQuery('#gallery').galleryView({
                    panel_width: panel_width,
                    panel_height: panel_height,
                    gallery_padding: 20,
                    show_filmstrip: show_filmstrip
                });

                jQuery('.nav-next-overlay').hide();

                jQuery('#gallery').show();
                if (show_map_link) jQuery('#map_link').show();
                setTimeout("jQuery('#gallery_container').css('background', 'none');jQuery('#gallery_container .loader').hide();", 5000);
                setTimeout("jQuery(window).resize();", 500);
            });

    {/literal}
    </script>

    <div class="clearBoth"></div>

{/if}





