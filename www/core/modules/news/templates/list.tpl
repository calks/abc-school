
	<div class="text fields news_module">
	
		<h1>Новости</h1>
		
		{if $news}
		
			{foreach item=item from=$news}
				<div class="news_item">
					<h2>
			    		<span class="date">{$item->date|date_format:"%d.%m.%Y"}</span>
			    		{$item->title|escape:"htmlall":"utf-8"|strip_tags:false}
					</h2>
					
					{if $item->image}
						<div class="image">
							<img src="{$smarty.const.PHOTOS_URL}/news/{$item->id}/small/{$item->image}" alt="{$item->title|escape:"htmlall":"utf-8"|strip_tags:false}">
						</div>	
					{/if}
					
					
					<p {if $item->image}class="right"{/if}>
						{$item->story|escape:"html_all"|strip_tags:false|truncate:1000:'...'}
						
						<a class="more" href="{$item->link}">Подробнее</a>
					</p>

				</div>		
			{/foreach}
			
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