

						
						
							<tr>
								<th>
									На каком занятии задано
								</th>
								<th>
									Задание
								</th>							
							</tr>

							{if $can_edit}
								<tr class="{cycle values='odd,even'}">
									<td class="wide">
										<span class="time tmp hidden"></span>
										<input type="hidden" name="entry_id" value="" />
										<input type="hidden" name="entry_date" value="" />
									</td>
									<td class="last">
										<textarea class="hidden" name="task"></textarea>
										<a class="create" title="создать" href="#"></a>										
										<a class="cancel hidden" title="отмена" href="#"></a>
										<a class="save hidden" title="сохранить" href="#"></a>
										<input type="hidden" name="schedule_weekdays" value="{$group_schedule_day_numbers}">
									</td>
								</tr>
							{/if}

							
							{foreach from=$homework_data item=task}
								<tr class="{cycle values='odd,even'}">
									<td class="wide">
										{$task->schedule_entry_date|date_format:'%d.%m.%Y'} {$task->starts_at|date_format:'%H:%M'}
										<input type="hidden" name="entry_id" value="{$task->schedule_entry_id}" />
										<input type="hidden" name="entry_date" value="{$task->schedule_entry_date}" />
										
									</td>
									<td class="last">
										<div class="task">{$task->description_html}</div>
										{if $can_edit}
											<textarea name="task" class="hidden">{$task->description}</textarea>
											<a class="edit" title="править" href="#"></a>										
											<a class="cancel hidden" title="отмена" href="#"></a>
											<a class="save hidden" title="сохранить" href="#"></a>
										{/if}	
									</td>
								</tr>
							{/foreach}
							
