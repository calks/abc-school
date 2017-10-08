	
	{strip}
		<ul class="header_menu profile_menu">
			{foreach item=item key=key from=$items}
				<li {if $task==$key}class="active"{/if}>
					<a href="{$item.link}">{$item.name}</a>		
				</li>
			{/foreach}
		</ul>
	{/strip}	
	