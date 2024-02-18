<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "https://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>Автоматически созданные ученики</title>
	<meta name="keywords" content="">
	<meta name="description" content="">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
	{literal}
	<style type="text/css">
		* {
			font-family: sans-serif;
			font-size: 14px;
		}
		
		table {
			border-collapse: collapse;
			width: 100%;
		}
		
		td {
			border: 1px dotted #000000;
			padding: 10px;
			text-align: left;
		}
	</style>
	{/literal}
</head>

<body>

	<table>
		{foreach from=$students item=chunk}
			<tr>
				{foreach from=$chunk item=item}
					<td>
						логин: <b>{$item->login}</b><br>
						пароль: <b>{$item->pass}</b><br>
					</td>
				{/foreach}			
			</tr>
		{/foreach}
	</table>
</body>
</html>
