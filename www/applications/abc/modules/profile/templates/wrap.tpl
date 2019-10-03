

	<div class="text fields">

		<h1>{$document->meta_title|escape:"htmlall":"utf-8"|strip_tags:false}</h1>
		
		<script type="text/javascript">
			var can_edit_others_profile = {if $can_edit_others_profile}true{else}false{/if};
		</script>

		{$menu}
		
		{*$document->content*}


		{$page_content}
		
	</div>	