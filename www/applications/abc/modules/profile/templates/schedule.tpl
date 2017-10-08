
	<p{if $in_chart} class="gname"{/if}>
	
		{if !$in_chart}
			<b>Расписание занятий</b><br />
		{/if}	
	
		{assign var=first_line value=true}
		{foreach item=day_name key=day_number from=$weeksdays}
			{if $group_schedule.$day_number}
				{if !$first_line}<br />{/if}
				{assign var=first_line value=false}
				<span class="day_name">{$day_name}:</span>
				{foreach from=$group_schedule.$day_number item=item key=entry_time name=s_entries}
					{$entry_time|date_format:"%H:%M"}{if !$smarty.foreach.s_entries.last}, {/if}
				{/foreach}
			{/if}	
		{/foreach}
		
	</p>