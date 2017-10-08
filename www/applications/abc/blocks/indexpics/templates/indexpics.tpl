
			{strip}
				<div class="block indexpics">			
					<img class="active" src="{$static_dir}/img/content_01.jpg" alt="С языками на &laquo;Ты&raquo;">
					<img src="{$static_dir}/img/content_02.jpg" alt="С языками на &laquo;Ты&raquo;">
					<img src="{$static_dir}/img/content_03.jpg" alt="С языками на &laquo;Ты&raquo;">
					<img src="{$static_dir}/img/content_04.jpg" alt="С языками на &laquo;Ты&raquo;">
				</div>
			{/strip}
			
			<script type="text/javascript">
				{literal}
					jQuery(document).ready(function($){
						var block = $('.block.indexpics');

						var pic_count = block.find('img').size();
						var active_pic_number = 1;
						var fade_speed = 1000;

						function switchImage(){
							next_pic_number = active_pic_number == pic_count ? 1 : active_pic_number + 1;
							var active_pic = block.find('img:nth-child('+active_pic_number+')');
							var next_pic = block.find('img:nth-child('+next_pic_number+')'); 

							active_pic.fadeOut(fade_speed, function(){
								active_pic.removeClass('active');
							});

							next_pic.fadeIn(fade_speed, function(){
								next_pic.addClass('active');
								active_pic_number = next_pic_number; 
							});
						}

						setInterval(switchImage, 3000);
					});
				{/literal}
			</script>
