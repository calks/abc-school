

				<h1>{$profile_document->title}</h1>
				{$profile_document->content}
				
				
				<div class="pass_change_form">
				
					<h2>Смена пароля</h2>
					
										
					<div class="line old_pass">
						<label>Старый пароль:</label>
						<input type="password" name="old_pass">						
					</div>
					
					<div class="line old_pass">
						<label>Новый пароль:</label>
						<input type="password" name="new_pass">						
					</div>
					
					<a class="button" href="#">Изменить пароль</a>
					
				</div>
				
				
				<script type="text/javascript">
					var change_pass_url = '{$change_pass_url}';

					{literal}
								
						$(document).ready(function(){
							$('.pass_change_form .button').click(function(){

								var container = $(this).parents('.pass_change_form');

								var old_pass = $.trim(container.find('input[name=old_pass]').val());
								var new_pass = $.trim(container.find('input[name=new_pass]').val());

								if (old_pass.length==0 || new_pass.length==0) {
									overlay_message('Необходимо заполнить старый и новый пароль', 'warning');
									return false;									
								}

								_block('.pass_change_form');
								$.ajax({
									url: change_pass_url,
									type: 'post',
									dataType: 'json',
									data: {
										old_pass: old_pass,
										new_pass: new_pass
									},
									success: function(data){
										_unblock();
										overlay_message(data.message, data.status);
										if (data.status=='ok') {
											$('.pass_change_form input').val('');
										}
									}
								});

								return false;
							});
						});

					{/literal}					
				</script>
