<?php

	//define('EMAIL_DESTINATION', 'school.abc@mail.ru');
	define('EMAIL_DESTINATION', 'alexey@cyberly.ru');
	define('SMTP_HOST', 'hst2.sibnet.ru');
	define('SMTP_PORT', '465');
	define('SMTP_USER', 'no-reply@abc-school.ru');
	//define('SMTP_PASS', '978fX543x0X6m9s');
	define('SMTP_PASS', 'q~0f{P5!fRu;');
	
	
	define('DKIM_ENABLED', true);
	define('DKIM_DOMAIN', 'abc-school.ru');
	define('DKIM_SELECTOR', 'key1');	
	define('DKIM_PRIVATE_KEY_PATH', '/conf/abcpriv.pem');