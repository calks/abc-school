
	<div class="text fields quizz_module">
	
		<h1>{$page_heading}</h1>
		
		{$page_content}
		
		{if $quizzes}
		
			<ul class="quizzes">
				{foreach item=item from=$quizzes name=galleries}
					<li>
						<a href="{$item->link}">{$item->name}</a>						
					</li>					
				{/foreach}
			</ul>
					
		{/if}
	
	</div>