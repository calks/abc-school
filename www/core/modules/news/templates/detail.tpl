
    <div class="text fields news_module">
    
    	<h1>
    		<span class="date">{$news->date|date_format:"%d.%m.%Y"}</span>
    		{$news->title|escape:"htmlall":"utf-8"|strip_tags:false}
    	</h1>
		
		{if $news->image}
			<img src="{$smarty.const.PHOTOS_URL}/news/{$news->id}/big/{$news->image}" alt="{$news->title|escape:"htmlall":"utf-8"|strip_tags:false}">
		{/if}
		
        <div>
            {$news->story}
        </div>
        
        <a class="more" href="{$view_all_link}">К списку новостей</a>


    </div>
