<?php

	use PHPMailer\PHPMailer\PHPMailer;

	$site_root = realpath(dirname(__FILE__)."/../../../");
	
	require_once "$site_root/vendor/autoload.php";

	
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
  			$this->phpmailer = new PHPMailer();
		}

		public static function createMessage($params = NULL) {
			return new MailSender();
		}

  
	}
