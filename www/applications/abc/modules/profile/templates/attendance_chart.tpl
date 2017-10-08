
							<thead>
								<tr>
									{foreach from=$column_keys key=time item=entry_id name=cell_loop}
									    <td id="col_{$smarty.foreach.cell_loop.iteration}">
									    	<span class="data">
												<span class="time">{$time|date_format:'%d.%m.%Y <br /> %H:%M'}</span>
												{if $can_edit}
													<input type="hidden" name="entry_id" value="{$entry_id}" />
													<input type="hidden" name="entry_date" value="{$time|date_format:'%d.%m.%Y'}" />
													<a class="edit" title="править" href="#"></a><a class="save three-btn hidden" title="сохранить" href="#"></a><a class="cancel three-btn hidden" title="отмена" href="#"></a><a class="check_all three-btn hidden" title="отметить всех" href="#"></a>
												{/if}
											</span>	
									    </td>
									{/foreach}
									{if $can_edit}
										<td id="col_{$columns_count+1}">
											<span class="data">
												<span class="time tmp hidden"></span>
												<input type="hidden" name="entry_id" value="" />
												<input type="hidden" name="entry_date" value="" />
												<a class="create" title="создать" href="#"></a><a class="save three-btn hidden" title="сохранить" href="#"></a><a class="cancel three-btn hidden" title="отмена" href="#"></a><a class="check_all three-btn hidden" title="отметить всех" href="#"></a>
											</span>	
										</td>
									{/if}									
								</tr>							
							</thead>
							<tbody>
								{foreach from=$attendance_data key=user_id item=user_data}
									{strip}
										<tr class="{cycle values='odd,even'}">										
											{foreach from=$column_keys key=time item=entry_id name=cell_loop}
											    <td id="col_{$smarty.foreach.cell_loop.iteration}" {if $user_data.attendance.$time.missed_two}class="missed_two"{/if}>{strip}
											    	<span class="data">												
														{if $user_data.attendance.$time.comment}
															<span class="check comment" title="{$user_data.attendance.$time.comment}"></span>												
														{elseif $user_data.attendance.$time.attendance}
															<span class="check plus"></span>
														{else}
															<span class="check minus"></span>													
														{/if}
		
														{if $can_edit}
															<a class="comment hidden {if $user_data.attendance.$time.comment}has_one{/if}" href="#" title="коментарий"></a>
															<textarea class="hidden" name="comment">{$user_data.attendance.$time.comment}</textarea>
															<input {if $user_data.attendance.$time.attendance && !$user_data.attendance.$time.comment}checked="checked"{/if} class="hidden" type="checkbox" name="attendance[]" value="{$user_id}"/>
														{/if}
													</span>		
											    {/strip}</td>
											{/foreach}
											{if $can_edit}
												<td>
													<span class="data">
														<a class="comment hidden" href="#" title="коментарий"></a>
														<textarea class="hidden" name="comment"></textarea>
														<input class="hidden" type="checkbox" name="attendance[]" value="{$user_id}"/>
													</span>	
												</td>
											{/if}
										</tr>
									{/strip}	
								{/foreach}
							</tbody>
