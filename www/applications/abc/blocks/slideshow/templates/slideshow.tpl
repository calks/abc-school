			<div class="block slideshow fields">
				<div class="right">
					<div class="frame"></div>
					<div class="slides">
						{foreach item=src from=$images}						
							<img src="{$src}" alt="">
						{/foreach}			
					</div>
				</div>
				<div class="left text">
					{$page_content}									
				</div>
			</div>
			
			{literal}
				<script type="text/javascript">
					$(document).ready(function(){
						$('.slideshow .slides').cycle({
							fx: 'fade',
							timeout: 7000,
							speed: 3000
						});
					});
				</script>
			{/literal}
			
			
			
