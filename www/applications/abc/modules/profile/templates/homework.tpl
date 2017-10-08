
	{include file=$common_heading}

	{if $user->role=='student'}

		{if $homework_data}
			<div class="attendance homework">
			
				<div class="chart_container">					
					<table class="chart">
						{$chart}						
					</table>
				</div>
			
			</div>
		{else}
			<p>Нет ни одной записи в журнале</p>
		{/if}	

	{else}

		{if $chart}	
			<div class="attendance homework">
			
				<div class="chart_container">					
					<table class="chart">
						{$chart}						
					</table>
				</div>
			
			</div>
		{else}	
			<p>Для просмотра журнала выберите группу</p>
		{/if}
	
	{/if}