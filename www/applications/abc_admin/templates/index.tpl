<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "https://www.w3.org/TR/html4/loose.dtd">
<html>
    {$html_head}
	<body{if $layout=='embeded'} class="embeded"{/if}>	
		<div class="body">
			{if $layout!='embeded'}
				{$header}
			
			
				<br clear="all">
				
				{if $errors}
					<div class="error">
						{foreach item=error from=$errors}
							{$error}<br>
						{/foreach}
					</div>
				{/if}
				
				{if $message}
					<div class="success">
						{$message}				
					</div>			
				{/if}
			{/if}				
			
			{$content}
		
			
		</div>
	</body>
</html>
