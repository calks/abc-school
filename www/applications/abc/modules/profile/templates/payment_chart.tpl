
							<thead>
								<tr>
									{foreach from=$column_keys key=period_start item=caption name=cell_loop}
									    <td id="col_{$smarty.foreach.cell_loop.iteration}">
									    	<span class="data">
												<span class="time">{$caption}</span>
												{if $can_edit}
													<input type="hidden" name="entry_date" value="{$period_start}" />												
													<a class="edit" title="править" href="#"></a><a class="save three-btn hidden" title="сохранить" href="#"></a><a class="cancel three-btn hidden" title="отмена" href="#"></a><a class="check_all three-btn hidden" title="отметить всех" href="#"></a>
												{/if}
											</span>
									    </td>
									{/foreach}
								</tr>							
							</thead>
							<tbody>
							
								{foreach from=$payment_data key=user_id item=user_data}
								
									{strip}
										<tr class="{cycle values='odd,even'}">
										
											{foreach from=$column_keys key=period_start item=entry_id name=cell_loop}
											    <td id="col_{$smarty.foreach.cell_loop.iteration}" {if $user_data.payments.$period_start.mark_unpayed}class="missed_two"{/if}>{strip}
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
