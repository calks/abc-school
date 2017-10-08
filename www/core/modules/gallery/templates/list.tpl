
	<div class="text fields gallery_module">
	
		<h1>{$page_heading}</h1>
		
		{$page_content}
		
		{if $galleries}
		
			<ul class="galleries">
				{foreach item=item from=$galleries name=galleries}
					<li {if $smarty.foreach.galleries.iteration%4 == 0}class="last"{/if}>
						{if $item->image}
							<a class="img" href="{$item->link}">
								<img src="{$smarty.const.PHOTOS_URL}/gallery/{$item->id}/thumb/{$item->image}" alt="{$item->name}" title="{$item->name}">
							</a>	
						{else}
							<a class="img no_photo" href="{$item->link}">&nbsp;</a>						
						{/if}
						<h2><a href="{$item->link}">{$item->name}</a></h2>
					</li>					
					{if $smarty.foreach.galleries.iteration%4 == 0}
						<li class="separator"></li>
					{/if}
				{/foreach}
			</ul>
			
			{if $pagenav_data.total>1}
				<div class="pagenav">
					{foreach key=page item=link from=$pagenav_data.links}
						{if $page==$pagenav_data.page}
							<span>{$page}</span>
						{else}
							<a href="{$link}">{$page}</a>
						{/if}
					{/foreach}		
				</div>
			{/if}
					
		{/if}
	
	</div>