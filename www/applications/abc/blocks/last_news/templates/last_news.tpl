
			
			
			<div class="block last_news fields text">
				<h2>Новости</h2>
				{foreach item=item from=$news name=last_news}
					<div class="column{if $smarty.foreach.last_news.iteration==1} first{elseif $smarty.foreach.last_news.iteration==3} last{/if}">
						<h3>
							<span class="date">{$item->date|date_format:"%d.%m.%Y"}</span>
							<a href="{$item->link}">{$item->title|escape:"html_all"|strip_tags:false}</a>
						</h3>
						<p>
							{$item->story|escape:"html_all"|strip_tags:false|truncate:230:'...'}
						</p>				
					</div>
				{/foreach}				
			</div>
