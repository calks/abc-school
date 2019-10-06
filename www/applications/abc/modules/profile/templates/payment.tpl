
		{include file=$common_heading}


		{if $user->role=='student'}
		
			{assign var=user_id value=$user->id}
					
			{if !$payment_data.$user_id.payments}
				<p>Нет ни одной записи в журнале</p>
			{else}
		
				<div class="attendance for_user">
					<div class="chart_container">					
						<table class="chart for-student">
							<tr>
								<th>
									Месяц и год
								</th>
								<th>
									Оплата
								</th>
								<th>
									Комментарий
								</th>							
							</tr>
							
							{foreach from=$payment_data.$user_id.payments key=time item=att}							
								<tr class="{cycle values='odd,even'}">
									<td class="wide {if $att.mark_unpayed}missed_two{/if}">{$att.caption|replace:'<br>':' '}</td>
									<td class="{if $att.mark_unpayed}missed_two{/if}">
										{if $att.comment}
											<span class="check comment" title=""></span>												
										{elseif $att.payed}
											<span class="check plus"></span>
										{else}
											<span class="check minus"></span>													
										{/if}
									</td>
									<td class="last {if $att.mark_unpayed}missed_two{/if}" style="text-align:left; padding: 3px 10px">
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
					{foreach from=$chart item=pchart key=gid}
						{if $pchart.data}
					
							<p class="gname">
								<b>{$pchart.group_name}</b>
								{if $pchart.month_price}
									<br>Стоимость обучения: {$pchart.month_price}{if $pchart.month_price_comment}<span class="asterisk">*</span><br>{/if}</span>
									{if $pchart.month_price_comment}
										<span class="comment">*{$pchart.month_price_comment}</span>
									{/if}
								{/if}
							</p>
							{$pchart.schedule}
							<div class="attendance payment" id="att_{$gid}">
								<input type="hidden" name="gid" value="{$gid}">
								<input type="hidden" name="gdays" value="{$gchart.schedule_days}">
								<ul class="students">
									{assign var=num value=1}
									{foreach from=$pchart.data key=user_id item=user_data}
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
										{$pchart.chart}						
									</table>
								</div>
							
							</div>
						{/if}	
					{/foreach}
				{else}			
					<div class="attendance payment" id="att">
						<input type="hidden" name="gid" value="{$smarty.get.group}">
						<input type="hidden" name="gdays" value="{$group_schedule_day_numbers}">
						<ul class="students">
							{assign var=num value=1}
							{foreach from=$payment_data key=user_id item=user_data}
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

			{elseif $smarty.get.group || $smarty.get.branch}
				<p>Нет ни одной записи в журнале</p>
			{else}	
				<p>Для просмотра журнала выберите группу</p>
			{/if}
		{/if}	
	