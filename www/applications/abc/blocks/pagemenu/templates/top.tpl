


			<ul class="header_menu">
				{foreach item=item from=$menu name=top_nemu_loop}
					<li>
						<a {if $item->children}href="" onclick="return false"{else}href="{$item->link}"{/if} {if $item->open_new_window} target="_blank"{/if}>
							{$item->title|@mb_strtolower:"utf8"}
						</a>
						
                        {if $item->children}
                            <ul class="dropmenu">
                                {foreach item=sub_item from=$item->children}
                                    <li>
                                        <a href="{$sub_item->link}" {if $sub_item->open_new_window} target="_blank"{/if}>
                                            {$sub_item->title|@mb_strtolower:"utf8"}
                                        </a>
                                    </li>
                                {/foreach}
                            </ul>
                        {/if}						
					</li>									
				{/foreach}			
			
			</ul>


			{literal}
				<script type="text/javascript">
				    jQuery(document).ready(function(){
				        $('.header_menu').dropmenu({	            
				            fade_speed: 200
				        });
				    });
			
				</script>
			{/literal}

