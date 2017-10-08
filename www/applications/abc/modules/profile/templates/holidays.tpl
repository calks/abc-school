
	<p>
	
		<b>Праздники</b>
	
		{foreach item=h from=$holidays_list name=hlist}
			<br />
			{$h->date|date_format:"%d.%m.%Y"}
			&ndash;			
			{$h->title}
		{/foreach}
		
	</p>