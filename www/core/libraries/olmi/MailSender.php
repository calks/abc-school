<?php 

	class MailSender {
		
		protected $phpmailer;
		
		public function setSubject($subject) {
			$this->phpmailer->Subject = $subject;
		}
		
		public function setFrom($address, $name = "") {
			$this->phpmailer->setFrom($address, $name);
		}
		
		public function setReplyTo($address, $name = "") {
			$this->phpmailer->addReplyTo($address, $name);
		}

		public function setBody($bodyText, $contentType=null, $charset=null, $encoding=null) {
			$this->phpmailer->msgHTML($bodyText);
		}

		public function addTo($address, $name = "") {
			$this->phpmailer->addAddress($address, $name);
		}
		
		
		public static function send($msg, $additional_parameters = NULL) {
			$msg->_send();
		}
		
		
		public function _send() {
			return $this->phpmailer->send();
		}
		
		
  		public function MailSender() {
  			
  			require_once Application::getSitePath() . '/vendor/autoload.php';
  			
  			
  			$this->phpmailer = new PHPMailer();
  			
  			$this->phpmailer->isSMTP();
  			
			$this->phpmailer->Host = SMTP_HOST; 
			$this->phpmailer->Port = SMTP_PORT;
             
			$this->phpmailer->SMTPAuth = true; 
			$this->phpmailer->Username = SMTP_USER;
			$this->phpmailer->Password = SMTP_PASS;  

			//$this->phpmailer->SMTPDebug = true;
			$this->phpmailer->Timeout = 10;
			$this->phpmailer->CharSet = 'UTF-8';
			
            if (DKIM_ENABLED) {
            	$this->phpmailer->DKIM_domain = DKIM_DOMAIN;
            	$this->phpmailer->DKIM_selector = DKIM_SELECTOR;
            	$this->phpmailer->DKIM_private = Application::getSitePath() . DKIM_PRIVATE_KEY_PATH;
            }
  			
		}

		public static function createMessage($params = NULL) {
			return new MailSender();
		}

  
	}