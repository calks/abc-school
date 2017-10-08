
	<div class="text fields gallery_module">
	
		<h1>{$page_heading}</h1>
		
		{$page_content}
		
		
		
		{if $photos}
		
			<h2 class="gallery_name">
				<a class="back" href="{$back_link}">Назад к разделам</a>
				{$gallery->name}
			</h2>
		
			<ul class="galleries">
				{foreach item=item from=$photos name=photos}
					<li {if $smarty.foreach.photos.iteration%4 == 0}class="last"{/if}>						
						<a class="img" href="{$item->fullsize_url}" title="{$item->comment}">
							<img src="{$item->thumb_url}" alt="{$item->comment}" title="{$item->comment}">
						</a>						
					</li>					
					{if $smarty.foreach.photos.iteration%4 == 0}
						<li class="separator"></li>
					{/if}
				{/foreach}
			</ul>
			
			{if $pagenav_data.total>1}
				<div class="pagenav">
					{foreach key=page item=link from=$pagenav_data.links}
						{if $page==$pagenav_data.page}
							<span>{$page}</span>
						{else}
							<a href="{$link}">{$page}</a>
						{/if}
					{/foreach}		
				</div>
			{/if}
			
			
			<script type="text/javascript">
				var static_dir = '{$static_dir}';

				{literal}
				
					$('.gallery_module a.img').lightBox({
						txtImage:				'Изображение',
						txtOf:					'из',	
						imageLoading:			static_dir+'/img/lightbox/lightbox-ico-loading.gif',
						imageBtnPrev:			static_dir+'/img/lightbox/lightbox-btn-prev.gif',
						imageBtnNext:			static_dir+'/img/lightbox/lightbox-btn-next.gif',
						imageBtnClose:			static_dir+'/img/lightbox/lightbox-btn-close.gif',
						imageBlank:				static_dir+'/img/lightbox/lightbox-blank.gif'
					});
				{/literal}					
			</script>
			
			
		{else}		
			<p>В этом разделе нет фотографий</p>
		{/if}
	
	</div>