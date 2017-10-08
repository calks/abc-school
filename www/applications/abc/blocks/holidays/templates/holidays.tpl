
	<p>
	
		<b>Праздники</b>
	
		{foreach item=h from=$holidays_list name=hlist}
			<br />
			{$h->date_str}
			&ndash;			
			{$h->title}
		{/foreach}
		
	</p>