
							<thead>
								<tr>
									{assign var=col_number value=1}
								
									{if $user->role!='student'}
										<td id="col_1">
									    	<span class="data info">
												<span class="time">Дополнит.</span>
												{if $can_edit_user_notes}																									
													<a class="edit" title="править" href="#"></a>
													<a class="save save-notes three-btn hidden" title="сохранить" href="#"></a>
													<a class="cancel three-btn hidden" title="отмена" href="#"></a>
													<input type="hidden" name="start_year" value="{$start_year}">
													<input type="hidden" name="chart_type" value="payment">													
												{/if}
											</span>
									    </td>
									    {assign var=col_number value=$col_number+1}
									{/if}
									{foreach from=$column_keys key=period_start item=caption name=cell_loop}
									    <td id="col_{$col_number}">
									    	<span class="data">
												<span class="time">{$caption}</span>
												{if $can_edit}
													<input type="hidden" name="entry_date" value="{$period_start}" />												
													<a class="edit" title="править" href="#"></a>
													<a class="save three-btn hidden" title="сохранить" href="#"></a><a class="cancel three-btn hidden" title="отмена" href="#"></a><a class="check_all three-btn hidden" title="отметить всех" href="#"></a>
												{/if}
											</span>
									    </td>
									    {assign var=col_number value=$col_number+1}
									{/foreach}
								</tr>							
							</thead>
							<tbody>
							
								{foreach from=$payment_data key=user_id item=user_data}
								
									{strip}
										<tr class="{cycle values='odd,even'}">
										
											{assign var=col_number value=1}
										
											{if $user->role!='student'}
												{strip}
													<td id="col_1">
														<span class="data notes">												
															{if $user_data.user_notes}
																<span class="check comment" title="{$user_data.user_notes}"></span>
															{/if}
				
															{if $can_edit_user_notes}
																<a class="comment hidden {if $user_data.user_notes}has_one{/if}" href="#" title="коментарий"></a>
																<textarea class="hidden" name="notes[{$user_id}]">{$user_data.user_notes}</textarea>														
															{/if}
														</span>		
													</td>
												{/strip}
												{assign var=col_number value=$col_number+1}
											{/if}

										
											{foreach from=$column_keys key=period_start item=entry_id name=cell_loop}
											    <td id="col_{$col_number}" {if $user_data.payments.$period_start.mark_unpayed}class="missed_two"{/if}>{strip}
											    	<span class="data">												
														{if $user_data.payments.$period_start.comment}
															<span class="check comment" title="{$user_data.payments.$period_start.comment}"></span>												
														{elseif $user_data.payments.$period_start.payed}
															<span class="check plus"></span>
														{else}
															<span class="check minus"></span>													
														{/if}
		
														{if $can_edit}
															<a class="comment hidden {if $user_data.payments.$period_start.comment}has_one{/if}" href="#" title="коментарий"></a>
															<textarea class="hidden" name="comment">{$user_data.payments.$period_start.comment}</textarea>
															<input {if $user_data.payments.$period_start.payed && !$user_data.payments.$period_start.comment}checked="checked"{/if} class="hidden" type="checkbox" name="payed[]" value="{$user_id}"/>
														{/if}
													</span>		
											    {/strip}</td>
											{/foreach}
										</tr>
									{/strip}	
								{/foreach}
							</tbody>
