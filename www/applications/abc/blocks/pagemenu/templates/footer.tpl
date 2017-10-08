

			<ul class="footer_menu">
				{foreach item=item from=$menu name=footer_nemu_loop}
					<li>
						<a href="{$item->link}" {if $item->open_new_window} target="_blank"{/if}>
							{$item->title|@mb_strtolower:"utf8"}
						</a>
					</li>
				{/foreach}
			</ul>
			
