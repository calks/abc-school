
		{include file=$common_heading}


		{if $user->role=='student'}
		
			{assign var=user_id value=$user->id}
			
			<pre>
				{$marks_data.$user_id.marks|@print_r}
			</pre>
					
			{if !$marks_data.$user_id.marks}
				<p>Нет ни одной записи в журнале</p>
			{else}
		
				<div class="attendance for_user">
					<div class="chart_container">					
						<table class="chart for-student">
							<tr>
								<th>
									Дата и время занятия
								</th>
								<th>
									Оценка
								</th>
								<th>
									Комментарий
								</th>							

							</tr>
							
							{foreach from=$marks_data.$user_id.marks key=time item=att}
								<tr class="{cycle values='odd,even'}">
									<td class="wide {if $att.missed_two}missed_two{/if}">{$time|date_format:'%d.%m.%Y %H:%M'}</td>
									<td class="{if $att.missed_two}missed_two{/if}">
										{if $att.comment}
											<span class="check comment" title=""></span>
										{/if}													
										{if $att.marks}
											<span class="check mark">{$att.marks}</span>																							
										{/if}
									</td>
									<td class="last {if $att.missed_two}missed_two{/if}" style="text-align:left; padding: 3px 10px">
										{$att.comment}
									</td>
								</tr>
							{/foreach}
							
						</table>
					</div>
				</div>
			{/if}		
		
		
		{else}
			
			{if $chart}
				{if $chart|is_array}
					{foreach from=$chart item=gchart key=gid}
						{if $gchart.data}
							<p class="gname"><b>{$gchart.group_name}</b></p>
							{$gchart.schedule}
							<div class="attendance marks" id="att_{$gid}">
								<input type="hidden" name="gid" value="{$gid}">
								<ul class="students">
									{assign var=num value=1}
									{foreach from=$gchart.data key=user_id item=user_data}
										<li class="{cycle values='odd, even'}">
											{$num}.
											{if $user_data.info_link}
												<a class="user_info" href="{$user_data.info_link}">
													{$user_data.user_name}
												</a>	
											{else}
												{$user_data.user_name}
											{/if}	
										</li>
										{assign var=num value=$num+1}
									{/foreach}
								</ul>
								
								<div class="chart_container">
								
									<table class="chart">
										{$gchart.chart}						
									</table>
								</div>
							
							</div>
						{/if}	
					{/foreach}
				{else}
					<div class="attendance marks" id="att">
						<input type="hidden" name="gid" value="{$smarty.get.group}">
						<ul class="students">
							{assign var=num value=1}
							{foreach from=$marks_data key=user_id item=user_data}
								<li class="{cycle values='odd,even'}">
									{$num}. 
									{if $user_data.info_link}
										<a class="user_info" href="{$user_data.info_link}">
											{$user_data.user_name}
										</a>	
									{else}
									{$user_data.info_link}
										{$user_data.user_name}
									{/if}	
								
								</li>
								{assign var=num value=$num+1}
							{/foreach}
						</ul>
						
						<div class="chart_container">						
							<table class="chart">
								{$chart}						
							</table>
						</div>
					
					</div>
				{/if}
			{elseif $smarty.get.group}
				<p>Нет ни одной записи в журнале</p>
			{else}	
				<p>Для просмотра журнала выберите группу</p>
			{/if}
		{/if}	
	