

	<div class="text fields question_module">
		
		<h1>{$page_heading}</h1>
		
		{$page_content}
		
		<br><br>
		{foreach item=item from=$questions}
			<div class="item">		
				<h2>
					<span class="author">
						<a class="email" href="mailto:{$item->author_email|escape:"htmlall":"utf-8"|strip_tags:false}">{$item->author_name|escape:"htmlall":"utf-8"|strip_tags:false}</a>	
					</span>
					
					<span class="added_date">{$item->created|date_format:"%d.%m.%Y"}</span>
					<span class="added_time">{$item->created|date_format:"%H:%M"}</span>
				</h2>
				
				<p class="question">{$item->question|escape:"htmlall":"utf-8"|strip_tags:false}</p>
				
				<h3>Ответ:</h3>
				<p class="answer">{$item->answer|escape:"htmlall":"utf-8"|strip_tags:false}</p>
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

	
	</div>
	
	
	
	
	
	